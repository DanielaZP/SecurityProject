<?php
// Obtener los datos del formulario
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// Conectar a la base de datos PostgreSQL
$host = 'localhost';
$dbname = 'encriptacion';
$user = 'postgres';
$password = 'Pincholin7';

$conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    echo "Conexión exitosa a la base de datos PostgreSQL";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}

// Insertar los datos en la tabla usuarios
$stmt = $conn->prepare("INSERT INTO usuarios (username, email, password) VALUES (:username, :email, :password)");
$stmt->bindParam(':username', $username);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':password', $password);

$stmt->execute();

// Redireccionar a una página de éxito
header('Location: registro_exitoso.html');
?>
