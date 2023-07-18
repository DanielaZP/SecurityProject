<?php
include 'db_connection.php';

// Definir una variable para almacenar el mensaje de error
$error_message = '';

// Verificar si se envió el formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

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
            $error_message = "Contraseña incorrecta";
        }
    } else {
        $error_message = "El correo electrónico no está registrado";
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
