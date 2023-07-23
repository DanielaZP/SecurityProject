# SecurityProject
REGISTRO DE USUARIOS

Para que un usuario nuevo pueda registrarse es necesario que lo haga sin utilizar un nombre de usuario o un correo que ya hayan sido registrados previamente, implementamos funciones de reconocimiento para evitar algun tipo de error al registrarse o despues al iniciar sesion como usuarios ya registrado, necesitabamos que el usuario se pudiera registrar utilizando un correo electronico autentico y valido, es decir, un correo que si existiera ya que implemente la API ZeroBounce para poder hacer un reconocimiento de correos realesy funcionales al mismo tiempo utilizando la libreria PHPMailer logre hacer que los usuarios registrados reciebieran un correro electronico de confirmacion de registro para que el usuarios este al dia con sus registros, todos los datos ingresados por el usuario al momento de iniciar sesion son guardados dentro la base de datos en nuestra tabla usuarios en donde identificados a cada usuarios con un id e insertamos los datos como: username, email y contrasena esta ultima guardandose encriptada con el algoritmo hash dentro la bd como medida de seguridad, aplicamos filtros de reconocimiento para los usuarios como el sanitizeString para el useername y la contrasena y el filter_var para el correo  de esa manera logramos elimimar caracteres que no son validos al momento de registrarse, tambien aplicamos seguridad para las contraseñas haciendo funciones para que puedan reconocerla antes de encriptarla y asi lograr que sean consideradas seguras para el usuario, el usuario debe proporcionar una contraseña de al menos 8 caracteres en mayusculas, minusculas y numeros para que pueda registrarse.

INCIO DE SESION DEL USUARIO

AUTENTICACION DE DOS FACTORES(2FA)
Para realizar la autenticacion de dos factores es recomendable tener descargado la aplicacion de Google Authenticator esta es una aplicacion que nos permite añadir un nivel de seguridad a nuestras cuentas, en este proyecto despues del incio de sesion correctamente y ya teniendo el codigo QR en nuestro correo electronico se escanea con esta aplicacion lo que te proporciona codigos de verificacion por determinados tiempos.

![f5433014-538b-4e40-9c9f-95a026ee835e](https://github.com/DanielaZP/SecurityProject/assets/131422347/3a50e17e-f903-44a5-b838-17d0e0a17668)

En caso de que el usuario halla ingresado el codigo de verificacion mal o se halla terminado el tiempo aparece un mensaje de error que indica que el codigo ingresado no es valido y caso contrario se realizo correctamente la autenticacion te dirige a la parte donde puedes encriptar y desencriptar archivos

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




