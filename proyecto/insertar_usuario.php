<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'security';
$user = 'postgres';
$bd_password = 'password';

$guardar = pg_connect("host=$host dbname=$dbname user=$user password=$bd_password");

$username = isset($_POST['username']) ? $_POST['username'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

$insertarbd = "INSERT INTO usuarios (username, email, contrasena) VALUES ('$username', '$email', '$contrasena')";

$consulta = pg_query($guardar, $insertarbd);

echo 'Guardado';
?>
