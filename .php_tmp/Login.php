<?php
include 'db_connection.php';

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
                // Contraseña válida, redireccionar a "inicio.php"
                header("Location: inicio.php");
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
