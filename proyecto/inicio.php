<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Aplicación | Encriptación y Desencriptación AES</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <style>
        /* Estilos del panel lateral */
        .panel-lateral {
            position: fixed;
            top: 0;
            right: 0;
            width: 200px;
            height: 100%;
            background-color: #b1d3e4;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .panel-lateral button {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <form class="registro">
        <div class="cuadro">
            <div class="input-cuadro">
                <label for="Archivo">Seleccione un Archivo</label>
                <input type="file" id="archivoSeleccionado" onchange="mostrarNombreArchivo()">
                <script>
                    function mostrarNombreArchivo() {
                        var inputElement = document.getElementById('archivoSeleccionado');
                        var fileName = inputElement.files[0].name;
                    }
                </script>
                <!-- PASSWORD INPUT -->
                <label for="password">Contraseña</label>
                <input type="password" id="passwordInput" placeholder="Ingrese contraseña">
            </div>

            <div class="botones">
                <button class="button" onclick="encriptar()">
                    Encriptar
                </button>
                <button class="button" onclick="desencriptar()">
                    Desencriptar
                </button>
            </div>
        </div>
    </form>
                    
    <!-- Panel lateral con opciones -->
    <div class="panel-lateral">
        <button class="button" onclick="cerrarSesion()">
            Cerrar sesión
        </button>
        <!-- Otras opciones del panel lateral -->
    </div>

    <script>
        function encriptar() {
            var password = document.getElementById('passwordInput').value;
            var inputElement = document.getElementById('archivoSeleccionado');
            var file = inputElement.files[0];

            var reader = new FileReader();
            reader.onload = function (e) {
                var fileData = e.target.result;

                // Encriptar usando AES
                var encryptedData = CryptoJS.AES.encrypt(fileData, password);

                // Crear un enlace para descargar el archivo encriptado
                var downloadLink = document.createElement('a');
                downloadLink.href = 'data:application/octet-stream,' + encodeURIComponent(encryptedData);
                downloadLink.download = file.name.replace(/\.([^/.]+)$/, '_encriptado.$1'); // Nombre encriptado con la misma extensión
                downloadLink.click();
            };
            reader.readAsText(file);
        }

        function desencriptar() {
            var password = document.getElementById('passwordInput').value;
            var inputElement = document.getElementById('archivoSeleccionado');
            var file = inputElement.files[0];

            var reader = new FileReader();
            reader.onload = function (e) {
                var fileData = e.target.result;

                // Desencriptar usando AES
                var decryptedData = CryptoJS.AES.decrypt(fileData, password);
                var decryptedText = decryptedData.toString(CryptoJS.enc.Utf8);
                
                //Verifica si contiene datos validos
                if(decryptedText){
                // Crear un enlace para descargar el archivo desencriptado
                var downloadLink = document.createElement('a');
                downloadLink.href = 'data:application/octet-stream,' + encodeURIComponent(decryptedText);
                downloadLink.download = file.name.replace(/_encriptado\.([^/.]+)$/, '_desencriptado.$1'); // Nombre desencriptado con la misma extensión
                downloadLink.click();
                }else{
                    alert('Error al desencriptar el archivo. Contraseña incorrecta o archivo corrupto.');
                }
            };
            reader.readAsText(file);
        }
        function cerrarSesion() {
            // Redireccionar a la página "login.php" al hacer clic en "Cerrar sesión"
            window.location.href = "login.php";
        }
    </script>
</body>
</html>