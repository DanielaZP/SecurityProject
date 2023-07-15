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

// Verificar si se envió el formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];

    // Insertar los datos en la tabla usuarios
    $query = "INSERT INTO usuarios (username, email, contrasena) VALUES ('$username', '$email', '$contrasena')";
    $result = pg_query($conn, $query);

    // Verificar si la inserción fue exitosa
    if ($result) {
        echo "Datos insertados correctamente";
    } else {
        echo "Error al insertar los datos";
    }
}

// Cerrar la conexión a la base de datos
pg_close($conn);
?>
