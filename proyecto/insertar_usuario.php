<?php
// Datos de conexión a la base de datos
$host = 'localhost'; // Nombre o dirección IP del servidor PostgreSQL
$dbname = 'encriptacion'; // Nombre de la base de datos
$user = 'postgres'; // Nombre de usuario de la base de datos
$password = 'Pincholin7'; // Contraseña del usuario de la base de datos

// Recuperar los datos enviados por el formulario
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

var_dump($_POST); // Agregado para imprimir los datos enviados por el formulario

try {
    // Conexión a la base de datos utilizando PDO
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

    // Preparar la consulta SQL para insertar los datos en la tabla "usuarios"
    $query = "INSERT INTO usuarios (username, email, password) VALUES (?, ?, ?)";
    $statement = $pdo->prepare($query);

    // Ejecutar la consulta con los valores proporcionados por el usuario
    $statement->execute([$username, $email, $password]);

    // Redireccionar al usuario a una página de registro exitoso
    header("Location: registro_exitoso.html");
    exit();
} catch (PDOException $e) {
    // Manejo de errores de la base de datos
    echo "Error al insertar los datos en la base de datos: " . $e->getMessage();
    exit();
}
?>
