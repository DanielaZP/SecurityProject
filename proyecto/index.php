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
      <form action="insertar_usuario.php" method="POST">
        <div class="cuadro">
          <div class="input-cuadro">
            <label for="username">Username</label>
            <input type="text" name="username" placeholder="Ingrese su nombre de usuario">
            <label for="email">Email Address</label>
            <input type="text" name="email" placeholder="Ingrese la dirección de su correo electrónico">
            <label for="contrasena">Contraseña</label>
            <input type="password" name="contrasena" placeholder="Ingrese contraseña">
          </div>
          <input type="submit" value="Registrarse" class="button">
          <p>¿Ya tienes una cuenta? <a class="link" href="Login.php">Iniciar Sesión</a></p>
        </div>
      </form>
    </div>
  </body>
</html>
