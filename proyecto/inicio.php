<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Encriptacion y desencriptacion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="Encrip">
        <div class="cuadro">
          
            <div class="input-cuadro">
            <label for="encriptar">Encriptacion de archivos</label>
                <label for="archivo">Seleccionar Archivo</label>
                <input type="file" name="archivo" required>
            </div>
            <div class="input-cuadro">
                <label for="contrasena">Contraseña</label>
                <input type="password" name="contrasena" placeholder="Ingrese contraseña" required>
            </div>
            <div class="input-cuadro">
                <input type="submit" value="Encriptar" class="button">
            </div>
        </div>

        <div class="cuadro">
            <div class="input-cuadro">
            <label for="Desencriptar">Desencriptar Archivos</label>
                <label for="archivo">Seleccionar Archivo Encriptado</label>
                <input type="file" name="archivo-encriptado" required>
            </div>
            <div class="input-cuadro">
                <label for="contrasena">Contraseña</label>
                <input type="password" name="contrasena" placeholder="Ingrese contraseña" required>
            </div>
            <div class="input-cuadro">
                <input type="submit" value="Desencriptar" class="button">
            </div>
        </div>
    </div>
</body>
</html>


