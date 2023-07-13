<!-- Aqui tiene que estar la conexion con la bd, pero aun no encuentro su extrension xd-->
<?php
$host = 'localhost';
$port = '5432';
$dbname = 'encriptacion';
$user = 'postgres';
$password = 'Pincholin7';

$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

$conn = pg_connect($connection_string);
if ($conn === false) {
  echo "Error al conectar a la base de datos";
  exit;
}


pg_close($conn);
?>
