<?php
include_once "lib/functions.php";

include_once "colpr-config.php";

$template = $twig->loadTemplate('nueva-propuesta.html');
if (!isset($_SESSION["usuario"])){
	header( 'Location: login.php' );
}else{
$datos = array('user'=>autentificado(),'openProps'=>$openProps, 'lang'=>$lang);
echo $template->render($datos);
}