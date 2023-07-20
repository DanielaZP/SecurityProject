<?php

// Incluye el archivo de autoloader generado por Composer
require 'vendor/autoload.php';

use OTPHP\TOTP;

// Genera un secreto compartido único para el usuario (esto debería guardarse en la base de datos del servidor)
$user_secret = 'SECRETO_DEL_USUARIO'; // Reemplaza esto con el secreto real del usuario

// Crea una instancia de TOTP con el secreto del usuario
$totp = TOTP::create($user_secret);

// Genera un código OTP en función del tiempo actual
$otp_code = $totp->now();

echo "Código OTP actual: " . $otp_code . PHP_EOL;

// Ahora que tienes el código OTP, puedes enviarlo al usuario (por ejemplo, mostrarlo en una interfaz o enviarlo por correo electrónico, SMS, etc.).

// Cuando el usuario intenta iniciar sesión, el código ingresado por el usuario se verifica utilizando el mismo secreto compartido.

// Supongamos que el usuario ingresa el código a verificar
$user_input_code = $_POST['otp_code']; // Asegúrate de obtener el código ingresado por el usuario de la solicitud POST.

// Verifica el código ingresado
if ($totp->verify($user_input_code)) {
    // Código válido. El usuario está autenticado.
    echo "Código válido. Usuario autenticado.";
} else {
    // Código inválido. Autenticación fallida.
    echo "Código inválido. Autenticación fallida.";
}
