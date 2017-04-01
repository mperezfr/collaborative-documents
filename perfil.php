<?php
include_once "lib/functions.php";

include_once "colpr-config.php";

$user=autentificado();

// Propuestas en orden cronológico del usuario
$consultaPropuestasUsuario = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector, p.barrio
FROM users AS u join prog_propuestas AS p ON (`autor_id` = u.id)
WHERE  `autor_id` = '.$user['id'].'
ORDER BY p.id;';

// Propuestas votadas en orden cronológico del usuario
$consultaVotadasUsuario = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector, p.barrio
FROM users AS u join prog_propuestas AS p ON (`autor_id` = u.id)
JOIN prog_likes_propuesta AS lp ON (p.id = lp.propuesta_id)
WHERE lp.usuario_id = '.$user['id'].'
ORDER BY p.id DESC;';



/*echo $consultaVotadasUsuario;
return;*/

$datos = array('user'=>$user,
               'propusuario'=>listar($consultaPropuestasUsuario),
               'votosusuario'=>listar($consultaVotadasUsuario),
               'openProps'=>$openProps,   // Indica si se pueden hacer nuevas propuestas
               'ipr'=>getRealIpAddr(),    // La IP desde la que se ha hecho la consulta
                'lang'=>$lang);           //FIXME: Mirar si el lang se puede leer desde la plantilla twig

/*foreach($datos as $key => $value)
 {
  echo $key." has the value ***". $value."***<br />\n";
  var_dump($value);
 } 
return;*/


$template = $twig->loadTemplate('perfil.html');               
echo $template->render($datos);
