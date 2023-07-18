<?php
include 'db_connection.php';

// Verificar si se envió el formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    // Validar el formato del correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Ingrese un correo electrónico válido";
        exit();
    }

    // Encriptar la contraseña
    $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);

    // Insertar los datos en la tabla usuarios
    $query = "INSERT INTO usuarios (username, email, contrasena) VALUES ('$username', '$email', '$hashedPassword')";
    $result = pg_query($conn, $query);

    // Verificar si la inserción fue exitosa
    if ($result) {
        // Redireccionar a "autenticacion.php"
        header("Location: autenticacion.php");
        exit();
    } else {
        echo "Error al insertar los datos";
    }
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
            </div>
        </form>
    </div>
</body>
</html>
