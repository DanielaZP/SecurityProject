

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>2FA - Verificación en dos pasos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <div class="qr-section">
            <h1>Verificación en dos pasos</h1>
            <div class="white-box">
                <!-- Formulario para ingresar el código del usuario -->
                <form method="post">
                    <div class="input-box">
                        <label for="codigo">Ingresa el código del QR:</label>
                        <input type="text" id="codigo" name="codigo" placeholder="Código QR" required>
                    </div>

                    <!-- Botón para verificar el código ingresado -->
                    <button type="submit" class="button">Verificar</button>
                </form>

                <!-- Mostrar mensaje de error si el código no es válido -->
                <?php if (isset($_POST['codigo']) && !$isValidCode): ?>
                    <p>Error: El código ingresado no es válido.</p>
                <?php endif; ?>

                <!-- Enlace para volver atrás -->
                <p><a class="link" href="login.php">Volver atrás</a></p>
            </div>
        </div>
    </div>
</body>
</html>
