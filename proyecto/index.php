<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use OTPHP\TOTP;
use Base32\Base32;


include 'db_connection.php';
require 'vendor/autoload.php'; 

// Verificar si se envió el formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario y aplicar filtrado
    $username = sanitizeString($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $contrasena = sanitizeString($_POST['contrasena']);

    // Validar los datos ingresados
    if (empty($username) || empty($email) || empty($contrasena)) {
        $error_message = "Todos los campos son requeridos";
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        $error_message = "El nombre de usuario solo puede contener letras y números";
    } elseif (!$email) {
        $error_message = "Ingrese un correo electrónico válido";
    } elseif (!esContrasenaFuerte($contrasena)) {
        $error_message = "La contraseña debe tener al menos 8 caracteres y contener letras mayúsculas, minúsculas y números.";
    } else {
        // Verificar si el usuario o el correo electrónico ya están registrados
        $query = "SELECT * FROM usuarios WHERE username = '$username' OR email = '$email'";
        $existingUser = pg_query($conn, $query);

        if (pg_num_rows($existingUser) > 0) {
            $error_message = "El usuario o el correo electrónico ya están registrados";
        } else {

         // Generar el secreto y encriptar la contraseña
         $secret = generateRandomSecret();
         $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);   

            // Verificar si el correo electrónico existe utilizando el servicio ZeroBounce
            $apiKey = '8034085449e041778520a8feb70fa15b';
            $emailEncoded = urlencode($email); // Codificar el correo electrónico para incluirlo en el URL
            $url = "https://api.zerobounce.net/v2/validate?api_key=$apiKey&email=$emailEncoded";
            $response = file_get_contents($url);
            $result = json_decode($response);

            // Verificar la respuesta de la API de ZeroBounce
            if ($result && isset($result->status)) {
                if ($result->status === 'valid') {
                    // Encriptar la contraseña
                    $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);

                     // Insertar los datos en la tabla usuarios
            $query = "INSERT INTO usuarios (username, email, contrasena, secret) VALUES ('$username', '$email', '$hashedPassword', '$secret')";
            $insertResult = pg_query($conn, $query);

                    // Verificar si la inserción fue exitosa
                    if ($insertResult) {
                        // Envía el correo de confirmación al usuario
                        mailConfirmation($email);

                        // Redireccionar a "autenticacion.php"
                        header("Location: inicio.php");
                        exit();
                    } else {
                        $error_message = "Error al insertar los datos";
                    }
                } elseif ($result->status === 'invalid') {
                    $error_message = "Ingrese un correo electrónico válido";
                } elseif ($result->status === 'unknown') {
                    $error_message = "No se pudo verificar el correo electrónico en este momento";
                } else {
                    $error_message = "Respuesta inválida de la API ZeroBounce";
                }
            } else {
                $error_message = "No se pudo conectar con la API ZeroBounce";
            }
        }
    }
}


// Función para generar un secreto aleatorio
function generateRandomSecret()
{
    // Longitud del secreto (en bytes)
    $length = 10; // Ajusta la longitud según tus necesidades

    // Generar un secreto aleatorio utilizando la función random_bytes()
    $randomBytes = random_bytes($length);

    // Codificar el secreto en base32 utilizando base_convert()
    $base32Characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'; // Caracteres permitidos para Base32
    $base32Secret = '';
    foreach (str_split($randomBytes) as $byte) {
        $base32Secret .= $base32Characters[ord($byte) & 31];
    }

    return $base32Secret;
}


// Función para sanitizar una cadena de texto
function sanitizeString($text)
{
    // Remover etiquetas HTML y caracteres especiales
    $text = strip_tags($text);
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    
    return $text;
}

// Función para verificar si la contraseña es segura
function esContrasenaFuerte($contrasena)
{
    // Verificar la longitud de la contraseña
    if (strlen($contrasena) < 8) {
        return false;
    }

    // Verificar si contiene letras mayúsculas, minúsculas y números
    if (!preg_match('/[A-Z]/', $contrasena) || !preg_match('/[a-z]/', $contrasena) || !preg_match('/[0-9]/', $contrasena)) {
        return false;
    }

    return true;
}

// Función para enviar el correo de confirmación al usuario
function mailConfirmation($email)
{
    require 'vendor/autoload.php';

    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '202103856@est.umss.edu';
        $mail->Password = 'pxzottnypumfnumg';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('202103856@est.umss.edu', 'Teresa');
        $mail->addAddress($email); 

        $mail->isHTML(true);
        $mail->Subject = 'Confirmacion de Registro';
        $mail->Body = 'Hola,<br>Gracias por registrarte en nuestro sitio. Tu registro ha sido confirmado correctamente.';
        $mail->send();
    } catch (Exception $e) {
        echo 'Mensaje ' . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login de Usuarios | Encriptacion</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <img src="Imagenes/Logo.jpg" class="avatar">
    <div class="registro">
        <h1>Registro</h1>
        <form action="index.php" method="POST">
            <div class="cuadro">
                <div class="input-cuadro">
                    <label for="username">Username</label>
                    <input type="text" name="username" placeholder="Ingrese su nombre de usuario" required>
                    <label for="email">Email Address</label>
                    <input type="email" name="email" placeholder="Ingrese la dirección de su correo electrónico" required>
                    <label for="contrasena">Contraseña</label>
                    <input type="password" name="contrasena" placeholder="Ingrese contraseña" required>
                </div>
                <input type="submit" value="Registrarse" class="button">
                <p>¿Ya tienes una cuenta? <a class="link" href="Login.php">Iniciar Sesión</a></p>
                <?php
                // Mostrar el mensaje de error si existe
                if (!empty($error_message)) {
                    echo "<div class='error'>$error_message</div>";
                }
                ?>
            </div>
        </form>
    </div>
</body>
</html>
