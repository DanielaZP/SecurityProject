<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Aplicación | Encriptación y Desencriptación AES</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
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

                // Crear un enlace para descargar el archivo desencriptado
                var downloadLink = document.createElement('a');
                downloadLink.href = 'data:application/octet-stream,' + encodeURIComponent(decryptedText);
                downloadLink.download = file.name.replace(/\.([^/.]+)$/, '_desencriptado.$1'); // Nombre desencriptado con la misma extensión
                downloadLink.click();
            };
            reader.readAsText(file);
        }
    </script>
</body>
</html>
