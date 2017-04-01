<?php
include_once "lib/functions.php";

if(isset ($_GET['sector'])){

$tag= $_GET['sector'];
$etiqueta = str_replace ( '-' ,' ', $tag);

$template = $twig->loadTemplate('propuestas.html');

$consulta = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id and p.sector ="'.$tag.'"
ORDER BY p.sum_likes DESC; ';

$consultaordenaleat = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id and p.sector ="'.$tag.'"
ORDER BY RAND(); ';

$debatidas = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id and p.sector ="'.$tag.'"
ORDER BY p.comentarios DESC; ';

$recientes = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id and p.sector ="'.$tag.'"
ORDER BY p.id DESC; ';

$consensuadas = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector, (LOG(p.positivos+p.negativos)* ((p.positivos-p.negativos) /(p.positivos+p.negativos))) log, (p.positivos /(p.positivos+p.negativos)) porcentaje
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id and p.sector ="'.$tag.'"
ORDER BY log DESC; ';



$datos = array('tag'=>$etiqueta,'user'=>autentificado(),'propaleatorias'=>listar($consultaordenaleat),
               'propuestas'=>listar($consulta), 'debatidas'=>listar($debatidas), '
               recientes'=>listar($recientes), 'consensuadas'=>listar($consensuadas),
               'openProps'=>$openProps);
echo $template->render($datos);
}