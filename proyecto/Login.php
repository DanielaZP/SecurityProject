<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
include 'db_connection.php';
use OTPHP\TOTP;
require 'vendor/autoload.php';

// Función para codificar en base32
function base32Encode($input)
{
    $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    $inputLength = strlen($input);
    $binaryString = '';
    for ($i = 0; $i < $inputLength; $i++) {
        $binaryString .= sprintf('%08b', ord($input[$i]));
    }

    $base32String = '';
    $binaryStringLength = strlen($binaryString);
    $paddingLength = 8 - ($binaryStringLength % 8);
    if ($paddingLength !== 8) {
        $binaryString .= str_repeat('0', $paddingLength);
    }

    $segments = str_split($binaryString, 5);
    foreach ($segments as $segment) {
        $index = bindec($segment);
        $base32String .= $base32Chars[$index];
    }

    return $base32String;
}

// Función para generar un secret aleatorio de longitud $length
function generateRandomSecret($length = 16)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $charactersLength = strlen($characters);
    $randomSecret = '';
    for ($i = 0; $i < $length; $i++) {
        $randomSecret .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomSecret;
}

// Función para generar un código QR en base al secret proporcionado
function generateQRCode($secret)
{
    $label = 'MyApp'; 

    if (empty($label)) {
        throw new \InvalidArgumentException("The label is not set.");
    }

    $otp = TOTP::create($secret, 30, 'SHA1', 6); // Configuramos el tamaño del código en 6 dígitos y el algoritmo SHA-1
    $otp->setLabel($label); // Configurar la etiqueta en el objeto TOTP
    $otp->setIssuer($label); // Especificar el emisor del token (mismo valor que la etiqueta)
    $otpUri = $otp->getProvisioningUri();

    return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($otpUri);
}

// Lógica para verificar el código ingresado por el usuario
$isValidCode = false;
if (isset($_POST['codigo'])) {
    $codigoUsuario = $_POST['codigo'];
    $otp = TOTP::create($secret, 30, 'SHA1', 6);
    $isValidCode = $otp->verify($codigoUsuario);
}
// Definir la duración del bloqueo en segundos (1 hora)
$block_duration = 3600;

// Definir una variable para almacenar el mensaje de error
$error_message = '';

// Verificar si se envió el formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    // Verificar si la cuenta está bloqueada
    if (isAccountBlocked($email)) {
        $error_message = "La cuenta está temporalmente bloqueada. Intente nuevamente más tarde.";
    } else {
        // Buscar el usuario por correo electrónico en la base de datos
        $query = "SELECT * FROM usuarios WHERE email = '$email'";
        $result = pg_query($conn, $query);

        // Verificar si se encontró un usuario con el correo electrónico especificado
        if (pg_num_rows($result) > 0) {
            $user = pg_fetch_assoc($result);

            // Verificar si la contraseña coincide utilizando password_verify()
            if (password_verify($contrasena, $user['contrasena'])) {
                
             // Generar un nuevo secret aleatorio y actualizar la columna "secret" en la tabla "usuarios"
             $newSecret = generateRandomSecret();
             $updateQuery = "UPDATE usuarios SET secret = '$newSecret' WHERE email = '$email'";
             pg_query($conn, $updateQuery);

             // Generar el nuevo código QR con el nuevo secret
             $qrCodeUrl = generateQRCode($newSecret);

                 enviarCorreo($user['email'], $qrCodeUrl);
                 // Iniciar la sesión y guardar el correo electrónico en la sesión
                 session_start();
                  $_SESSION['email'] = $email;
               
                header("Location: 2fa.php");
                exit();
            } else {
                // Contraseña incorrecta, incrementar el contador de intentos fallidos
                incrementFailedAttempts($email);

                // Verificar si se alcanzó el límite de intentos fallidos
                if (isMaxFailedAttemptsExceeded($email)) {
                    // Bloquear la cuenta por la duración especificada
                    blockAccount($email, $block_duration);

                    $error_message = "La cuenta está temporalmente bloqueada. Intente nuevamente más tarde.";
                } else {
                    $error_message = "Contraseña incorrecta";
                }
            }
        } else {
            $error_message = "El correo electrónico no está registrado";
        }
    }
}

// Función para verificar si la cuenta está bloqueada
function isAccountBlocked($email)
{
    global $conn;

    // Obtener la información de bloqueo de la cuenta
    $query = "SELECT * FROM bloqueo_cuenta WHERE email = '$email'";
    $result = pg_query($conn, $query);

    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $block_start_time = $row['block_start_time'];
        $block_duration = $row['block_duration'];

        // Calcular el tiempo actual y el tiempo de finalización del bloqueo
        $current_time = time();
        $block_end_time = $block_start_time + $block_duration;

        // Verificar si la cuenta está aún bloqueada
        if ($current_time < $block_end_time) {
            return true;
        } else {
            // Eliminar el registro de bloqueo de la cuenta
            $delete_query = "DELETE FROM bloqueo_cuenta WHERE email = '$email'";
            pg_query($conn, $delete_query);
        }
    }

    return false;
}

// Función para incrementar el contador de intentos fallidos
function incrementFailedAttempts($email)
{
    global $conn;

    // Verificar si existe un registro de intentos fallidos para el correo electrónico especificado
    $query = "SELECT * FROM intentos_fallidos WHERE email = '$email'";
    $result = pg_query($conn, $query);

    if (pg_num_rows($result) > 0) {
        // Actualizar el contador de intentos fallidos
        $row = pg_fetch_assoc($result);
        $failed_attempts = $row['failed_attempts'] + 1;
        $update_query = "UPDATE intentos_fallidos SET failed_attempts = $failed_attempts WHERE email = '$email'";
        pg_query($conn, $update_query);
    } else {
        // Insertar un nuevo registro de intentos fallidos
        $insert_query = "INSERT INTO intentos_fallidos (email, failed_attempts) VALUES ('$email', 1)";
        pg_query($conn, $insert_query);
    }
}

// Función para verificar si se alcanzó el límite de intentos fallidos
function isMaxFailedAttemptsExceeded($email)
{
    global $conn;

    // Obtener el número máximo de intentos fallidos permitidos
    $max_failed_attempts = 3;

    // Obtener el contador de intentos fallidos para el correo electrónico especificado
    $query = "SELECT failed_attempts FROM intentos_fallidos WHERE email = '$email'";
    $result = pg_query($conn, $query);

    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $failed_attempts = $row['failed_attempts'];

        // Verificar si se alcanzó el límite de intentos fallidos
        if ($failed_attempts >= $max_failed_attempts) {
            return true;
        }
    }

    return false;
}

// Función para bloquear la cuenta
function blockAccount($email, $duration)
{
    global $conn;

    // Calcular el tiempo actual y el tiempo de inicio del bloqueo
    $current_time = time();
    $block_start_time = $current_time;

    // Insertar un registro de bloqueo de la cuenta en la base de datos
    $insert_query = "INSERT INTO bloqueo_cuenta (email, block_start_time, block_duration) VALUES ('$email', $block_start_time, $duration)";
    pg_query($conn, $insert_query);
}



// Función para enviar el correo de confirmación al usuario con el código QR
function enviarCorreo($email, $qrCodeUrl)
{
    require 'vendor/autoload.php';

    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '202103856@est.umss.edu';
        $mail->Password = 'yemtpdsaesqoipzn';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('202103856@est.umss.edu', 'Teresa');
        $mail->addAddress($email); 

        $mail->isHTML(true);
        $mail->Subject = 'Código QR para verificación en dos pasos';
        $mail->Body = 'Aquí está tu código QR para la verificación en dos pasos:<br><img src="' . $qrCodeUrl . '" alt="Código QR">';
        $mail->send();
    } catch (Exception $e) {
        echo 'Mensaje ' . $mail->ErrorInfo;
    }
}
?>


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login de Usuarios | Encriptacion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form class="registro" action="login.php" method="POST">
        <img src="Imagenes/Logo.jpg" class="avatar">
        <h1>Bienvenidos</h1>
        <div class="cuadro">
            <div class="input-cuadro">
                <label for="email">Email Address</label>
                <input type="email" name="email" placeholder="Ingrese la dirección de su correo electrónico" required>
                <label for="contrasena">Contraseña</label>
                <input type="password" name="contrasena" placeholder="Ingrese contraseña" required>
            </div>
            <input type="submit" value="Login" class="button">
            <p>¿No tienes una cuenta? <a class="link" href="index.php">Registrate</a></p>
            <?php
            // Mostrar el mensaje de error si existe
            if (!empty($error_message)) {
                echo "<div class='error'>$error_message</div>";
            }
            ?>
        </div>
    </form>
</body>
</html>
