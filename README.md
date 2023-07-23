# SecurityProject
REGISTRO DE USUARIOS

Para que un usuario nuevo pueda registrarse es necesario que lo haga sin utilizar un nombre de usuario o un correo que ya hayan sido registrados previamente, implementamos funciones de reconocimiento para evitar algun tipo de error al registrarse o despues al iniciar sesion como usuarios ya registrado, necesitabamos que el usuario se pudiera registrar utilizando un correo electronico autentico y valido, es decir, un correo que si existiera ya que implemente la API ZeroBounce para poder hacer un reconocimiento de correos realesy funcionales al mismo tiempo utilizando la libreria PHPMailer logre hacer que los usuarios registrados reciebieran un correro electronico de confirmacion de registro para que el usuarios este al dia con sus registros, todos los datos ingresados por el usuario al momento de iniciar sesion son guardados dentro la base de datos en nuestra tabla usuarios en donde identificados a cada usuarios con un id e insertamos los datos como: username, email y contrasena esta ultima guardandose encriptada con el algoritmo hash dentro la bd como medida de seguridad, aplicamos filtros de reconocimiento para los usuarios como el sanitizeString para el useername y la contrasena y el filter_var para el correo  de esa manera logramos elimimar caracteres que no son validos al momento de registrarse, tambien aplicamos seguridad para las contraseñas haciendo funciones para que puedan reconocerla antes de encriptarla y asi lograr que sean consideradas seguras para el usuario, el usuario debe proporcionar una contraseña de al menos 8 caracteres en mayusculas, minusculas y numeros para que pueda registrarse.


INCIO DE SESION DEL USUARIO

Para que un usuario pueda iniciar sesion tiene que estar registrado previamente para esto aplicamos funciones de reconocimientor mediante la bd en la tabla usuarios en la que nuestrosusuarios estan registrados, reconocimienotde la contraseña y correo en la bd, la primera estando encriptada con el algoritmo hash y logrando que esta sea reconocida utilizando password_verify para que la contraseña sin encriptar ingresada al momento de inisiar sesion por el usuario coincida con la contraseña que esta encriptada con hash dentro la bd, el usuario tiene 3 intentos para poder iniciar sesion, si llegara a equivocarse una cuarta vez la cuenta es bloqueada temporalmente por una hora, para eso implementamos varias funciones en el codigo y tablas en la bd. algunas como: isAccountBlocked en la que ingresamos a la bd en la tabla bloqueo_cuenta para poder verificar si la cuenta no se encuentra bloqueada en ese momento y en caso de estar bloqueada no permite el ingreso aun con los datos correctos hasta que el tiempo predeterminado de una hora acabe, despues incrementFailedAttempts cpn esta funcion nosotros creamos un contador para los intentos de inicio fallidos del usuario y los vamosactualizando en la tabla intentos_fallidos de la bd donde al llegar a 3 la cuenta se bloquea usando la funcion blockAccount en la que calculamos el tiempo de bloqueo $current_time y $block_start_time en el que empieza a contar el bloque desde ese momento y terminara dentro de una hora con la variable block_duration. Una vez el usuario hay iniciado sesion automaticamente envia un mensaje al correo con un codigo qr para realizar la doble autentificacion y el inicio sea seguro.

AUTENTICACION DE DOS FACTORES(2FA)

El código implementa la autenticación de dos factores (2FA) mediante el uso de códigos de un solo uso generados por la aplicación TOTP (Time-Based One-Time Password) usando la libreria spomky-labs/otphp .
primero generamos un Secreto Aleatorio:
La función generateRandomSecret() se utiliza para generar un secreto aleatorio en Base32. Este secreto se utiliza como una clave compartida entre el servidor y la aplicación de autenticación 2FA del usuario, el cual es insertado en la tabla usuarios actualizandola cada inicio de sesion para que el usuario obtenga un codigo diferente cada inicio de sesion.
Generación del Código QR:
La función generateQRCode($secret) se utiliza para generar un código QR que contiene el secreto aleatorio generado. El código QR se muestra al usuario y debe ser escaneado por la aplicación de autenticación 2FA (como Google Authenticator) para configurar la autenticación.
Autenticación del Usuario:
Cuando un usuario intenta iniciar sesión, se verifica si la cuenta está bloqueada debido a intentos fallidos anteriores.
Si la cuenta no está bloqueada, se verifica el correo electrónico y la contraseña ingresados.
Si las credenciales son válidas, se genera un nuevo secreto aleatorio utilizando generateRandomSecret().
Se actualiza el secreto en la base de datos y se genera un nuevo código QR utilizando generateQRCode($newSecret).
El nuevo código QR se envía al usuario por correo electrónico a través de la función enviarCorreo($email, $qrCodeUrl) para ser escaneado en la aplicación 2FA del usuario.


Verificación del Código 2FA:

Una vez configurado, cuando el usuario intenta iniciar sesión nuevamente, se le pedirá que ingrese un código 2FA de su aplicación de autenticación.
El código ingresado por el usuario se verifica utilizando el algoritmo TOTP para validar su autenticidad. Esto se hace en la parte del código que inicia con $otp = TOTP::create($secret, 30, 'SHA1', 6);.
Si el código es válido, el usuario se autentica correctamente y se le permite acceder al sistema.
Obtención del Secreto desde la Base de Datos:

La función obtenerSecretDesdeBD($email) se utiliza para obtener el secreto almacenado en la base de datos correspondiente al correo electrónico del usuario actual que ha iniciado sesión.
Verificación del Código TOTP:
Cuando el usuario ingresa el código del QR en el formulario y envía el formulario, se recupera el código ingresado en la variable $codigoUsuario.
Luego, se obtiene el secreto del usuario desde la base de datos utilizando obtenerSecretDesdeBD($email).
Se crea un objeto TOTP con el secreto obtenido, configurando el intervalo de tiempo y otros parámetros necesarios para la verificación.
A continuación, el código ingresado por el usuario se verifica llamando a $otp->verify($codigoUsuario). Si el código es válido, devuelve true, lo que indica que el usuario ha sido autenticado correctamente.
Redirección o Mensaje de Error:
Si el código ingresado no es válido (es decir, la verificación falla), se muestra un mensaje de error en la página.
Si el código es válido, se redirige al usuario a la página de inicio, asumiendo que ha completado exitosamente la verificación en dos pasos.

ENCRIPTACION Y DESENCRIPTACION DE ARCHIVO SELECCIONADO

Una vez iniciado sesion correctamente la pagina creada abre la interfaz del proceso de encriptacion y desencriptacion usando el algoritmo AES.
Se puede observar que pide seleccionar un archivo de tu sistema, una vez seleccionado el archivo que desea encriptar debe ingresar una contraseña esta permite que no cualquier persona desencripte el archivo sino solo las que conocen la contraseña.

![image](https://github.com/DanielaZP/SecurityProject/assets/131422347/3d2fd638-acf1-4b89-9dfe-d2db57b098f3)

Al ingresar los datos correspondientes tendra la opcion de hacer click en dos botones ya sea en el caso que desee si es una encriptacion o una desencriptacion.
En nuestro codigo despues de encriptar el archivo seleccionado hacer click en "Encriptar" hace que el archivo encriptado se descargue en las descargas de su sistema con el nombre original del archivo y una etiqueta de <encriptado>

![image](https://github.com/DanielaZP/SecurityProject/assets/131422347/f06c34f1-028d-40b9-abc0-03376b6d27b4)

Al momento de querer desencriptar el archivo de la misma forma de la encriptacion selecciona el archivo ya encriptado e ingresando la misma contraseña que con la que se encripto el archivo se desencripta y se descarga en las descargas de sus sitema

![image](https://github.com/DanielaZP/SecurityProject/assets/131422347/68cfea2f-0f81-4007-8bb4-ec17025db259)

En caso de que la contraseña no sea la misma con la que fue encriptada el archivo aparece un error




