<?php
include_once "lib/functions.php";

// Muestra todas las propuestas con su contenido y comentarios

$template = $twig->loadTemplate('propuestas_full.html');

$consulta = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector, p.barrio
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id
ORDER BY p.sum_likes DESC; ';

$consultaordensectorfecha = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.propuesta, p.sum_likes, p.positivos, p.negativos, p.sector, p.barrio
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id
ORDER BY p.sector, p.id DESC; ';

//ORDER BY RAND(); ';

$enmiendas = 'SELECT u.nombre, u.apellidos, e.id, e.enmienda, e.sum_likes, e.autor_id, e.propuesta_id
		FROM prog_enmiendas AS e, users AS u
		WHERE e.autor_id = u.id 
		ORDER BY e.sum_likes DESC, e.propuesta_id ASC';

// Saca todos los comentarios (No está parametrizada)   FIXME ?????
$comentarios = 'SELECT c.enmienda_id, u.nombre, u.apellidos, c.id, c.comentario, c.sum_likes, c.autor_id
FROM prog_enmiendas AS e, users AS u, prog_comentarios AS c
WHERE c.enmienda_id = e.id
AND c.autor_id = u.id';


$debatidas = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector, p.barrio
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id
ORDER BY p.comentarios DESC; ';

$recientes = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.positivos, p.negativos, p.sector, p.barrio
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id
ORDER BY p.id DESC; ';

$consensuadas = 
'SELECT u.nombre, u.apellidos, p.id, p.titulo, p.comentarios, p.sum_likes, p.sector, p.barrio, p.positivos, p.negativos, (LOG(p.positivos+p.negativos)* ((p.positivos-p.negativos) /(p.positivos+p.negativos))) log, (p.positivos /(p.positivos+p.negativos)) porcentaje
FROM users AS u, prog_propuestas AS p
WHERE  `autor_id` = u.id
ORDER BY log DESC; ';



$datos = array('user'=>autentificado(),
               'propuestasfechasector'=>listar($consultaordensectorfecha),
               'enmiendas'=>listarpreparada($buscaID,$enmiendas),
               'comentarios'=>listarpreparada($buscaID, $comentarios),
                'lang'=>$lang);
               
echo $template->render($datos);