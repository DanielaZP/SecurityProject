<?php
require 'vendor/autoload.php';
require 'db_connection.php'; 
use OTPHP\TOTP;
 // Asegurarse de que el usuario haya iniciado sesión y haya una sesión activa
 session_start();
 if (!isset($_SESSION['email'])) {
     header("Location: login.php");
     exit();
 }
function obtenerSecretDesdeBD($email)
{
    global $conn;

    $query = "SELECT secret FROM usuarios WHERE email = '$email'";
    $result = pg_query($conn, $query);

    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        return $row['secret'];
    }

    return null;
}
// Obtener el correo electrónico del usuario desde la sesión
$email = $_SESSION['email'];

// Verificar si el código TOTP fue enviado desde el formulario
if (isset($_POST['codigo'])) {
    // Lógica para verificar el código ingresado por el usuario
    $codigoUsuario = $_POST['codigo'];
    $secret = obtenerSecretDesdeBD($email); // Lógica para obtener el secret desde la base de datos

    $otp = TOTP::create($secret, 30, 'SHA1', 6);
    $isValidCode = $otp->verify($codigoUsuario);

    // Mostrar mensaje de error si el código no es válido
    if (!$isValidCode) {
        echo "<p>Error: El código ingresado no es válido.</p>";
    } else {
        header("Location: inicio.php");
    }
}

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>2FA - Verificación en dos pasos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <div class="qr-section">
            <h1>Verificación en dos pasos</h1>
            <div class="white-box">
                <!-- Formulario para ingresar el código del usuario -->
                <form method="post">
                    <div class="input-box">
                        <label for="codigo">Ingresa el código del QR:</label>
                        <input type="text" id="codigo" name="codigo" placeholder="Código QR" required>
                    </div>

                    <!-- Botón para verificar el código ingresado -->
                    <button type="submit" class="button">Verificar</button>
                </form>

                <!-- Mostrar mensaje de error si el código no es válido -->
                <?php if (isset($_POST['codigo']) && !$isValidCode): ?>
                    <p>Error: El código ingresado no es válido.</p>
                <?php endif; ?>

                <!-- Enlace para volver atrás -->
                <p><a class="link" href="login.php">Volver atrás</a></p>
            </div>
        </div>
    </div>
</body>
</html>
