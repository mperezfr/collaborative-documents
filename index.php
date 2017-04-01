<?php
include_once "lib/functions.php";

include_once "colpr-config.php";


// Las 12 propuestas mejor valoradas
$consultaValoradas = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector, p.barrio
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id
ORDER BY p.sum_likes DESC LIMIT '.$limitProps.'; ';

// X propuestas en orden aleatorio
$consultaordenaleat = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector, p.barrio
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id
ORDER BY RAND() DESC;';
// ORDER BY RAND() DESC LIMIT '.$limitProps.';';

// Las X propuestas más comentadas ????
$debatidas = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector, p.barrio
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id
ORDER BY p.comentarios DESC LIMIT '.$limitProps.'; ';

// Las X últimas propuestas
$recientes = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector, p.barrio
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id
ORDER BY p.id DESC LIMIT '.$limitProps.'; ';

// Las X propuestas más consensuadas ????
$consensuadas = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.sector, p.barrio, p.positivos, p.negativos, (LOG(p.positivos+p.negativos)* ((p.positivos-p.negativos) /(p.positivos+p.negativos))) log, (p.positivos /(p.positivos+p.negativos)) porcentaje
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id
ORDER BY log DESC LIMIT '.$limitProps.'; ';

$datos = array('user'=>autentificado(),
               'propaleatorias'=>listar($consultaordenaleat),
               'propuestasvaloradas'=>listar($consultaValoradas), 
               'debatidas'=>listar($debatidas), 
               'recientes'=>listar($recientes), 
               'consensuadas'=>listar($consensuadas),
               'openProps'=>$openProps,   // Indica si se pueden hacer nuevas propuestas
               'ipr'=>getRealIpAddr(),    // La IP desde la que se ha hecho la consulta
                'lang'=>$lang);           //FIXME: Mirar si el lang se puede leer desde la plantilla twig

/*foreach($datos as $key => $value)
 {
  echo $key." has the value ***". $value."***<br />\n";
  var_dump($value);
 } 
return;*/


$template = $twig->loadTemplate('index.html');               
echo $template->render($datos);
