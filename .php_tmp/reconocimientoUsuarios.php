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
}

// Verificar si se envió el formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario de inicio de sesión
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    // Consultar la tabla usuarios para verificar las credenciales
    $query = "SELECT * FROM usuarios WHERE email = '$email' AND contrasena = '$contrasena'";
    $result = pg_query($conn, $query);
    $row = pg_fetch_assoc($result);

    // Verificar si se encontraron resultados
    if ($row) {
        // Credenciales válidas, redirigir al usuario a la página de inicio
        header("Location: inicio.php");
        exit();
    } else {
        // Credenciales inválidas, mostrar mensaje de error
        echo "Datos incorrectos";
    }
}

// Cerrar la conexión a la base de datos
pg_close($conn);

?>