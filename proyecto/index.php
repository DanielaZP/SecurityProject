<?php
include 'db_connection.php';

// Definir una variable para almacenar el mensaje de error
$error_message = '';

// Verificar si se envió el formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    // Verificar si el correo electrónico existe utilizando el servicio ZeroBounce
    $apiKey = 'b82c21c1e31e4559a897fd8f319036bb';
    $emailEncoded = urlencode($email); // Codificar el correo electrónico para incluirlo en el URL
    $url = "https://api.zerobounce.net/v2/validate?api_key=$apiKey&email=$emailEncoded";
    $response = file_get_contents($url);
    $result = json_decode($response);

    // Verificar la respuesta de la API de ZeroBounce
    if ($result && isset($result->status)) {
        if ($result->status === 'valid') {
            // Verificar si la contraseña es segura
            if (esContrasenaFuerte($contrasena)) {
                // Encriptar la contraseña
                $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);

                // Insertar los datos en la tabla usuarios
                $query = "INSERT INTO usuarios (username, email, contrasena) VALUES ('$username', '$email', '$hashedPassword')";
                $insertResult = pg_query($conn, $query);

                // Verificar si la inserción fue exitosa
                if ($insertResult) {
                    // Redireccionar a "autenticacion.php"
                    header("Location: autenticacion.php");
                    exit();
                } else {
                    $error_message = "Error al insertar los datos";
                }
            } else {
                $error_message = "La contraseña debe tener al menos 8 caracteres y contener letras mayúsculas, minúsculas y números.";
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
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login de Usuarios | Encriptacion</title>
    <link rel="stylesheet" href="style.css">
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
