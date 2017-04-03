<?php
include_once "lib/functions.php";
include_once "colpr-config.php";

if(isset ($_POST['cuenta'])){

	$enmienda=$_POST['enmienda'];
	$usuario=$_POST['usuario_id'];
	$cuenta=$_POST['cuenta'];

	//compruebo que se ha votado ya a esa enmienda:
	try{
	$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
	$consulta = "SELECT enmienda_voto FROM prog_likes_enmiendas 
	WHERE enmienda_id = :enmienda AND usuario_id = :usuario;";
	$arrayusuario = array(':enmienda'=>$enmienda, ':usuario'=>$usuario);
	$result = $conn->prepare($consulta);
	$result->execute($arrayusuario);
	foreach($result as $res){
		$enmienda_voto=$res['enmienda_voto'];
	}

	if ($enmienda_voto==1){
		$consulta="UPDATE prog_likes_enmiendas SET enmienda_voto=-1 
    	WHERE enmienda_id = :enmienda AND usuario_id = :usuario;";
    	$arrayusuario = array(':enmienda'=>$enmienda, ':usuario'=>$usuario);
		listarpreparada($arrayusuario,$consulta);
		$suma="UPDATE prog_enmiendas SET sum_likes=sum_likes+(-2) WHERE id = :enmienda;";
    	$arrayusuario = array(':enmienda'=>$enmienda);
		listarpreparada($arrayusuario,$suma);

	}elseif ($enmienda_voto==-1){
		$consulta="UPDATE prog_likes_enmiendas SET enmienda_voto=1
    	WHERE enmienda_id = :enmienda AND usuario_id = :usuario;";
    	$arrayusuario = array(':enmienda'=>$enmienda, ':usuario'=>$usuario);
		listarpreparada($arrayusuario,$consulta);
		$suma="UPDATE prog_enmiendas SET sum_likes=sum_likes+2 WHERE id = ".$enmienda.";";
    	$arrayusuario = array(':enmienda'=>$enmienda);
		listarpreparada($arrayusuario,$suma);

	}else{
		//Registro voto
		$consulta="INSERT INTO prog_likes_enmiendas(usuario_id, enmienda_id, enmienda_voto) 
		VALUES (:usuario, :enmienda, :cuenta);";
    	$arrayusuario = array(':usuario'=>$usuario,':enmienda'=>$enmienda, ':cuenta'=>$cuenta);
		listarpreparada($arrayusuario,$consulta);
		//sumo voto
		$suma="UPDATE prog_enmiendas SET sum_likes=sum_likes+(:cuenta) WHERE id = :enmienda;";
    	$arrayusuario = array(':cuenta'=>$cuenta, ':enmienda'=>$enmienda);
		listarpreparada($arrayusuario,$suma);
	}
	

	//recuento votos
	$consulta = "SELECT sum_likes FROM prog_enmiendas WHERE id = :enmienda;";
   	$arrayusuario = array(':enmienda'=>$enmienda);
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