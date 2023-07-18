<?php
// Configuración de la conexión a la base de datos PostgreSQL
$host = "localhost";
$port = 5432;
$dbname = "security";
$user = "postgres";
$password = "password";

// Conexión a la base de datos
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Verificar si la conexión fue exitosa
if (!$conn) {
    die("Error en la conexión a la base de datos");
} else{
 echo ("conexion con la base de datos exitosa");
}
?>
