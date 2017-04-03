<?php
include_once "lib/functions.php";
include_once "colpr-config.php";


if(isset ($_POST['comentario'])){

	$comentario=$_POST['comentario'];
	$usuario=$_POST['usuario_id'];

	try{
      $conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
      $consulta = "SELECT comentario_voto FROM prog_likes_comentarios 
    	WHERE comentario_id = :comentario AND usuario_id = :usuario;";
    	$arrayusuario = array(':comentario'=>$comentario, ':usuario'=>$usuario);
		$result->execute($arrayusuario);
		foreach($result as $res){
			$comentario_voto=$res['comentario_voto'];
		}

		if ($comentario_voto==1){
			$consulta="DELETE FROM prog_likes_comentarios WHERE comentario_id = ".$comentario." AND usuario_id = ".$usuario.";";
			listar($consulta);
			$suma="UPDATE prog_comentarios SET sum_likes=sum_likes+(-1) WHERE id = ".$comentario.";";
			listar($suma);

		}if ($comentario_voto==-1){
			$consulta="DELETE FROM prog_likes_comentarios WHERE comentario_id = ".$comentario." AND usuario_id = ".$usuario.";";
			listar($consulta);
			$suma="UPDATE prog_comentarios SET sum_likes=sum_likes+1 WHERE id = ".$comentario.";";
			listar($suma);

		}

	$consulta = "SELECT sum_likes FROM prog_comentarios WHERE id = :comentario;";
	$arraycomentario = array(':comentario'=>$comentario);
	$result = $conn->prepare($consulta);
	$result->execute($arraycomentario);
	//Se crea array vacío
	$output= array();
	foreach($result as $res){
		$like=$res['sum_likes'];
	}
	//Array serializado para pasar datos con json.
	$output[] = array('sum_likes' => $like);
	echo json_encode($output);
		
	}catch(PDOException $e ){
		echo $e -> getMessage();
	}	

}