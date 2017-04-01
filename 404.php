<?php
include_once "lib/functions.php";

$template = $twig->loadTemplate('404.html');

$datos = array();



echo $template->render($datos);



?>