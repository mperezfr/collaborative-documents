<?php
/**
 * The base configurations of the app
 
 Copy this file to colpr-config.php and change the values of the variables properly
 
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'databasename');

/** MySQL database username */
define('DB_USER', 'dbusername');

/** MySQL database password */
define('DB_PASSWORD', 'password');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/* Si la base de datos que contiene los usuarios está separada, */

/** The name of the database for WordPress */
define('DB_NAME2', 'databasename2');

/** MySQL database username */
define('DB_USER2', 'dbusername2');

/** MySQL database password */
define('DB_PASS2', 'password2');

/** MySQL hostname */
define('DB_HOST2', 'localhost');


//Data about the entity maintaining the web
$entityData = array('fromemail'=>"contacte@correo.info",
                    'replytoemail'=>"contacte2@correo2.info",
                    'thisweb'=>"https://www/this"
);

// Number of props shown in the main page
$limitProps=10;


// This variable defines if the process of sending proposals is open or not
$openProps=true;
//$openProps=false;

// This variable defines the max number of votes a user can do
$maxVotes=10;

// This variable defines if it is necessary to be authenticated or not
define('AUTH_USER', true);
//define('AUTH_USER', false);    # FIXME # FIXME # FIXME 


//Configuración Twig - Motor de plantillas
//Cargador de Twig
//Realpath nos da la ruta absoluta de ese directorio.
require_once realpath(dirname(__FILE__)."/vendor/twig/twig/lib/Twig/Autoloader.php");

//Clase::método estático (Con 4 puntos), frente a objeto flecha método
Twig_Autoloader::register();

//MPF register extensions
require_once realpath(dirname(__FILE__)."/vendor/twig/extensions/lib/Twig/Extensions/Autoloader.php");
Twig_Extensions_Autoloader::register();

//Una instancia del cargador (le pasamos donde están las vistas
$loader = new Twig_Loader_Filesystem(realpath(dirname(__FILE__)."/../vistas"));

$twig = new Twig_Environment($loader);

//MPF add i18n extension
$twig->addExtension(new Twig_Extensions_Extension_I18n());

/*
//MPF save IP

if(AUTH_USER==false) {
    $rem_ip=getRealIpAddr();
    $_SESSION["usuario"] = $rem_ip;
    // $_SESSION["usuario"] = ip2long (getRealIpAddr());
    $_SESSION['remote_ip'] = $rem_ip;
	
  

    $conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		
    // Test if the IP is in the database
    $queryIP = "select id from users WHERE ip = '".$rem_ip."';";

    $resultado = $conn -> query($queryIP);
	//$resultado->setFetchMode(PDO::FETCH_ASSOC); ??????????????  FIXME
	$row = $resultado->fetch();
    // echo "*****".$row[0]."<br />"; // 42

    // Si esta IP no se ha gastado, guardamos el usuario
    if (!$row)
        altaUsuarioAnonimo($_SESSION["usuario"], $rem_ip);
 
	$conn = null;
}
*/

$availableLanguages = array(
    'ca' => 'ca_ES',
    'es' => 'es_ES',
    'default' => 'ca_ES'
);

// Set language
// $locale = array_key_exists($_GET['lang'], $availableLanguages) ? $availableLanguages[$_GET['lang']] : $availableLanguages['default'];


if(isSet($_GET['lang']))
{
$lang = $_GET['lang'];
 
// register the session and set the cookie
$_SESSION['lang'] = $lang;
 
setcookie('lang', $lang, time() + (3600 * 24 * 30));
}
else if(isSet($_SESSION['lang']))
{
$lang = $_SESSION['lang'];
}
else if(isSet($_COOKIE['lang']))
{
$lang = $_COOKIE['lang'];
}
else
{
$lang = 'default';
} 

$locale = array_key_exists($lang, $availableLanguages) ? $availableLanguages[$lang] : $availableLanguages['default'];


//echo $locale;

putenv('LC_ALL='.$locale);
setlocale(LC_ALL, $locale);

// Specify the location of the translation tables
$bt=bindtextdomain('messages', './locale');
$bt2=bind_textdomain_codeset('messages', 'UTF-8');

// Choose domain
textdomain('messages');


?>