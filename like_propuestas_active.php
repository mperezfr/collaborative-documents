<?php
include_once "lib/functions.php";
include_once "colpr-config.php";

if(isset ($_POST['propuesta'])){

	$propuesta=$_POST['propuesta'];
	$usuario=$_POST['usuario_id'];
	
	try{
	$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
	$consulta = "SELECT propuesta_voto FROM prog_likes_propuesta 
	WHERE propuesta_id = :propuesta AND usuario_id = :usuario;";
	$arrayusuario = array(':propuesta'=>$propuesta, ':usuario'=>$usuario);
	$result = $conn->prepare($consulta);
	$result->execute($arrayusuario);
	foreach($result as $res){
		$propuesta_voto=$res['propuesta_voto'];
	}

	if ($propuesta_voto==1){
		$consulta="DELETE FROM prog_likes_propuesta 
    	WHERE propuesta_id = :propuesta AND usuario_id = :usuario;";
    	$arrayusuario = array(':propuesta'=>$propuesta, ':usuario'=>$usuario);
		listarpreparada($arrayusuario,$consulta);
		$suma="UPDATE prog_propuestas SET sum_likes=sum_likes-1, positivos=positivos-1 
		WHERE id = :propuesta;";
    	$arrayusuario = array(':propuesta'=>$propuesta);		
		listarpreparada($arrayusuario,$suma);

	}if ($propuesta_voto==-1){
		$consulta="DELETE FROM prog_likes_propuesta 
    	WHERE propuesta_id = :propuesta AND usuario_id = :usuario;";
    	$arrayusuario = array(':propuesta'=>$propuesta, ':usuario'=>$usuario);
		listarpreparada($arrayusuario,$consulta);
		$suma="UPDATE prog_propuestas SET sum_likes = sum_likes+1, negativos = negativos-1 
		WHERE id = :propuesta;";
    	$arrayusuario = array(':propuesta'=>$propuesta);		
		listarpreparada($arrayusuario,$suma);

	}

	
	$consulta = "SELECT sum_likes FROM prog_propuestas WHERE id = :propuesta;";
    $arrayusuario = array(':propuesta'=>$propuesta);
	$result = $conn->prepare($consulta);
	$result->execute($arrayusuario);
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