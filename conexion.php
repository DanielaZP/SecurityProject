<?php
$host = 'localhost';
$dbname = 'encriptacion';
$user = 'postgres';
$password = 'Pincholin7';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    echo 'Conectado a la base de datos';
} catch (PDOException $e) {
    echo 'Error al conectar a la base de datos: ' . $e->getMessage();
}
?>
