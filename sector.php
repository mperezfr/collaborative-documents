<?php
include_once "lib/functions.php";

if(isset ($_GET['sector'])){

$tag= $_GET['sector'];
$etiqueta = str_replace ( '-' ,' ', $tag);

$template = $twig->loadTemplate('propuestas.html');

$consulta = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id and p.sector = :tag
ORDER BY p.sum_likes DESC; ';

$consultaordenaleat = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id and p.sector = :tag
ORDER BY RAND(); ';

/*echo $consultaordenaleat;
return;*/

$debatidas = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id and p.sector = :tag
ORDER BY p.comentarios DESC; ';

$recientes = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id and p.sector = :tag
ORDER BY p.id DESC; ';

$consensuadas = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector, (LOG(p.positivos+p.negativos)* ((p.positivos-p.negativos) /(p.positivos+p.negativos))) log, (p.positivos /(p.positivos+p.negativos)) porcentaje
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id and p.sector = :tag
ORDER BY log DESC; ';

$arraytag = array(':tag' => $tag);

$datos = array('tag'=>$etiqueta,'user'=>autentificado(),'propaleatorias'=>listarpreparada($arraytag,$consultaordenaleat),
               'propuestas'=>listarpreparada($arraytag,$consulta), 'debatidas'=>listarpreparada($arraytag,$debatidas), '
               recientes'=>listarpreparada($arraytag,$recientes), 'consensuadas'=>listarpreparada($arraytag,$consensuadas),
               'openProps'=>$openProps, 'lang'=>$lang);
               
echo $template->render($datos);
}