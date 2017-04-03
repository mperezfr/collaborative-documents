<?php
include_once "lib/functions.php";

include_once "colpr-config.php";

/* Descomentaríamos la siguiente línea para mostrar errores de php en el fichero: */
//ini_set('display_errors', '1');

$template = $twig->loadTemplate('propuesta.html');
if(isset ($_GET['id'])){
	
	$id =$_GET['id'];
	$buscaID=array('id'=>$id);
	$propuesta = "SELECT  p.id, p.autor_id,p.titulo, p.propuesta, p.sum_likes,p.barrio, p.sector,
	(p.positivos /(p.positivos+p.negativos)) porcentaje, p.comentarios,
	u.nombre, u.apellidos, u.id as user_id
		FROM prog_propuestas as p, users as u
		WHERE p.id=:id and p.autor_id=u.id ";
		
    // Enmiendas a esa propuesta
	$enmiendas = 'SELECT u.nombre, u.apellidos, e.id, e.enmienda, e.sum_likes, e.autor_id
		FROM prog_enmiendas AS e, users AS u
		WHERE e.propuesta_id =:id
		AND e.autor_id = u.id 
		ORDER BY e.sum_likes DESC, e.propuesta_id ASC';

	$id_enmiendas = listarpreparada($buscaID,'SELECT e.id
		FROM prog_enmiendas AS e
		WHERE e.propuesta_id =:id');

    // Saca todos los comentarios (No está parametrizada)   FIXME ?????
	$comentarios = 'SELECT c.enmienda_id, u.nombre, u.apellidos, c.id, c.comentario, c.sum_likes, c.autor_id
FROM prog_enmiendas AS e, users AS u, prog_comentarios AS c
WHERE c.enmienda_id = e.id
AND c.autor_id = u.id';
/*    var_dump($comentarios);
	return;	*/

	$queryVotesDone = 'select count(*) as v from prog_likes_propuesta where usuario_id =:id';

   // $maxVotesReached = (intval(preparada(array('id'=>userid()),$votesDone)["v"]) >= $maxVotes);
   $votesDone=intval(preparada(array(':id'=>userid()),$queryVotesDone)["v"]);
   $maxVotesReached=false; 
   if ($votesDone >= $maxVotes) $maxVotesReached=true;   
   
   //var_dump(preparada(array('id'=>userid()),$votesDone));
   //echo userid()." ".intval(preparada(array('id'=>userid()),$votesDone)["v"])." ".$maxVotes;
   //if ($maxVotesReached) echo "true"; else echo "false";
   //return;
	
	$id_propuesta =$_GET['id'];
	$ID_prop=array('id'=>$id_propuesta);
	$consulta_autor = "SELECT p.autor_id FROM prog_propuestas as p, users 
		WHERE p.id=:id and p.autor_id=users.id;";
	$autor_id = autor_propuesta($ID_prop,$consulta_autor);
	$usuario_id = userid();

	//Compruebo que el autor de la propuesta sea el usuario logueado (para enlace editar)
		if ($autor_id==$usuario_id){
			$autor=array('autor_id'=>$autor_id);
			$usuario=array('usuario_id'=>$usuario_id);
		}else{
			$usuario=array('usuario_id'=>$usuario_id);
			
		}	
}

// FIXME: comentarios tiene todos os comentarios, habría que pasarle solo los de la propuesta
// FIXME: $autor no está definido
$datos = array('autor'=>$autor,'id'=>$buscaID,'user'=>autentificado(),'propuesta' => preparada($buscaID,$propuesta),
               'enmiendas'=>listarpreparada($buscaID,$enmiendas), 
               'comentarios'=>listarpreparada($buscaID, $comentarios), 'maxVotesReached'=>$maxVotesReached,
               'votesDone'=>$votesDone, 'maxVotes'=>$maxVotes,
               'ipr'=>getRealIpAddr() );

/*$datos = array('autor'=>$autor,'id'=>$buscaID,'user'=>autentificado(),'propuesta' => preparada($buscaID,$propuesta),
               'enmiendas'=>listarpreparada($buscaID,$enmiendas), 
               'comentarios'=>listarpreparada($id_enmienda, $comentarios), 'maxVotesReached'=>$maxVotesReached,
               'votesDone'=>$votesDone, 'maxVotes'=>$maxVotes,
               'ipr'=>getRealIpAddr() );
*/
/*foreach($datos as $key => $value)
 {
  echo $key." has the value ***". $value."***<br />\n";
 } 
*/

echo $template->render($datos);
?>
