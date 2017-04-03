<?php
include_once "lib/functions.php";
$template = $twig->loadTemplate('aviso-legal.html');
$datos = array('user'=>autentificado(),'lang'=>$lang);
echo $template->render($datos);