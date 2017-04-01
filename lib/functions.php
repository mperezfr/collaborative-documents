<?php
include_once "colpr-config.php";
include_once "config.php";
include_once "PasswordHash.php";



/* Descomentaríamos la siguiente línea para mostrar errores de php en el fichero: */
// ini_set('display_errors', '1');

//La coloco aquí para asegurar que se ejecuta siempre una única vez.
session_start(); 


//Usuarios

//Alta de usuarios
function alta($nombre, $apellidos, $email, $password, $ip){

	// Creamos el objeto que nos permitirá gestionar nuestro hash
	$hasher = new PasswordHash(8, FALSE);
	$hash = $hasher->HashPassword($password);

	try{
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			//$conn = new PDO('mysql:host=localhost;dbname=presupuestos_participativos;charset=utf8',$mpfusername, $mpfpassword);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$user = array('nombre'=>$nombre,'apellidos'=>$apellidos,'email'=>$email,'pass'=>$hash,'ip'=>$ip);
			$result=$conn->prepare( "INSERT INTO users(nombre, apellidos, email, password, ip) 
				VALUES(:nombre, :apellidos, :email, :pass, :ip);");
			$result->execute($user);
			header( 'Location: login.php' );
		}catch(PDOException $e ){
			echo $e -> getMessage();
		}	
		
		$conn = null; 
}

//Alta de usuarios
function altaUsuarioAnonimo($ip){

	try{
		   $nombre="";
		   $apellidos="";
		   $email=$ip;
		   $password="";
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			//$conn = new PDO('mysql:host=localhost;dbname=presupuestos_participativos;charset=utf8',$mpfusername, $mpfpassword);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$user = array('nombre'=>$nombre,'apellidos'=>$apellidos,'email'=>$email,'pass'=>$password,'ip'=>$ip);
			$result=$conn->prepare( "INSERT INTO users(nombre, apellidos, email, password, ip) 
			   	VALUES(:nombre, :apellidos, :email, :pass, :ip);");
			$result->execute($user);
			header( 'Location: login.php' );
		}catch(PDOException $e ){
			echo $e -> getMessage();
		}	
		
		$conn = null; 
}


//Alta de usuarios
// Damos de alta el usuario en la base de datos con los datos de usuario de la segunda base de datos
function altaConDatosCenso($id,$nombre, $apellidos, $email, $password, $ip){

	// Creamos el objeto que nos permitirá gestionar nuestro hash
	$hasher = new PasswordHash(8, FALSE);
	$hash = $hasher->HashPassword($password);

	try{
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			//$conn = new PDO('mysql:host=localhost;dbname=presupuestos_participativos;charset=utf8',$mpfusername, $mpfpassword);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$user = array('id'=>$id,'nombre'=>$nombre,'apellidos'=>$apellidos,'email'=>$email,'pass'=>$hash,'ip'=>$ip);
			$result=$conn->prepare( "INSERT INTO users(id, nombre, apellidos, email, password, ip) 
			   	VALUES(:id, :nombre, :apellidos, :email, :pass, :ip);");
			$result->execute($user);
			header( 'Location: login.php' );
		}catch(PDOException $e ){
			echo $e -> getMessage();
		}	
		
		$conn = null; 
}




//Login
function login($email, $pass){

  //logincenso($email, $pass);
  //logintodovale($email, $pass);
  loginorig($email, $pass);
}

//Login utilizando otra base de datos
function logincenso($email, $pass){

	// Creamos el objeto que nos permitirá gestionar nuestro hash
	// $hasher = new PasswordHash(8, FALSE);
   // DeleteDeleteDeleteDelete 	

	
	  //està en el cens?
     // connexió a la base de dades del cens
     try {
	   $db = new PDO('mysql:host='.DB_HOST2.';dbname='.DB_NAME2.';charset=utf8', DB_USER2, DB_PASS2);
       // } catch (PDOException $e) { die($e->getMessage()); }
       $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $st=$db->prepare("select idu, nombre, apellidos, email, pwd, aip from usuarios where email = ? and pwd = ?");
      $st->execute(array($email,md5($pass)));
      list($idu,$nombre, $apellidos, $email, $pwd, $ip)=$st->fetch(PDO::FETCH_NUM);

          if ($idu) {
				if (!isset($_SESSION["usuario"])){
	    			$_SESSION["usuario"] = $email;
	    			$basecenso=1000000;
	    			//añadimos al usuario del censo en la tabla users de esta base de datos
	    			altaConDatosCenso($idu+$basecenso,$nombre, $apellidos, $email, $pwd, $ip);
	    			//altaConDatosCenso($idu,$nombre, $apellidos, $email, $pwd, $ip);
				}
				if (isset ($_SESSION['error'])){
					unset($_SESSION['error']);
				}
			//Envía al index
			header( 'Location: index.php' );
    	
			} else {
			//Si no coinciden envía a login con sesión de error.
				$_SESSION["error"] = "error";
			    header( 'Location: login.php' );
			}
		}catch(PDOException $e ){
			echo $e -> getMessage();
		}	
		
		$conn = null; 

}






//Login, no hace comprobación, deja pasar con cualquier usuario
function logintodovale($email, $pass){



  /* De moment no fem cap comprovació, s'ha de comprovar el email i el password contra la base de 
  dades del cens i la base de dades de wordpress si qualsevol de les dos retorna cert, deixa passar */
  // try{
  	
  $validuser=false; 
 	   
      // Si es usuari valid
          $validuser=true;      
      // else
          // connexió a la base de dades de wordpress
          
          // if it is a valid user
             $validuser=true;      

       if ($validuser){ 	   
            $_SESSION["usuario"] = $email;
				if (isset ($_SESSION['error'])){
					unset($_SESSION['error']);
				}
            //Envía al index
            header( 'Location: index.php' );
			} else {
			//Si no coinciden envía a login con sesión de error.
				$_SESSION["error"] = "error";
			    header( 'Location: login.php' );
			}

/*
	// Creamos el objeto que nos permitirá gestionar nuestro hash
	$hasher = new PasswordHash(8, FALSE);
	try{
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$user = array('email'=>$email);
			$result=$conn->prepare( "SELECT password FROM users WHERE email=:email;");
			$result->execute($user);
			foreach ($result as $cont) {
				$password = $cont['password'];
				$user_id =$cont['id'];
			}	
			//comprueba que usuario y contraseña coinciden y crea sesión de usuario.
			if ($hasher->CheckPassword($pass, $password)) {
				if (!isset($_SESSION["usuario"])){
	    			$_SESSION["usuario"] = $email;
				}
				if (isset ($_SESSION['error'])){
					unset($_SESSION['error']);
				}
			//Envía al index
			header( 'Location: index.php' );
    	
			} else {
			//Si no coinciden envía a login con sesión de error.
				$_SESSION["error"] = "error";
			    header( 'Location: login.php' );
			}
		}catch(PDOException $e ){
			echo $e -> getMessage();
		}	
		
		$conn = null; 
*/
}









//Login
function loginorig($email, $pass){

	// Creamos el objeto que nos permitirá gestionar nuestro hash
	$hasher = new PasswordHash(8, FALSE);
	try{
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$user = array('email'=>$email);
			$result=$conn->prepare( "SELECT password FROM users WHERE email=:email;");   //FIXME: hash=1
			$result->execute($user);
			foreach ($result as $cont) {
				$password = $cont['password'];
				$user_id =$cont['id'];
			}	
			//comprueba que usuario y contraseña coinciden y crea sesión de usuario.
			if ($hasher->CheckPassword($pass, $password)) {   //FIXME: hash=1 o similar
				if (!isset($_SESSION["usuario"])){
	    			$_SESSION["usuario"] = $email;
				}
				if (isset ($_SESSION['error'])){
					unset($_SESSION['error']);
				}
			//Envía al index
			header( 'Location: index.php' );
    	
			} else {
			//Si no coinciden envía a login con sesión de error.
				$_SESSION["error"] = "error";
			    header( 'Location: login.php' );
			}
		}catch(PDOException $e ){
			echo $e -> getMessage();
		}	
		
		$conn = null; 

}

//Forgot

function forgot($email){

$password = RandomString(8,TRUE,TRUE,FALSE);

$hasher = new PasswordHash(8, FALSE);
$hash = $hasher->HashPassword($password);
	try{
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$user = array('email'=>$email, 'password'=>$hash);
			$result=$conn->prepare( "UPDATE users SET password=:password WHERE email=:email;");
			$result->execute($user);
	}catch(PDOException $e ){
			echo $e -> getMessage();
		}	
		
	$conn = null; 	

//Envía mail

	$para      = $email;
	$titulo    = 'nueva contraseña';
	$mensaje   = 'Hola:'. "\r\n" .'Recibes este email porque has solicitado una nueva contraseña. 
	Si no es así, ponte en contacto con nosotros en este mismo correo'. "\r\n" .'Nueva contraseña: '.$password. "\r\n" . 'Un saludo';
	$cabeceras = 'From: '. $entityData['fromemail']. "\r\n" .
    'Reply-To: '. $entityData['replytoemail'] . "\r\n" .
    'Content-type: text/html; charset=utf-8' . "\r\n".
    'X-Mailer: PHP/' . phpversion();

	mail($para, $titulo, $mensaje, $cabeceras);
	echo "Se ha enviado un mail";
	return;
	header( 'Location: login.php' );


}

//logout
function logout() {
//		var_dump($_SESSION);
		unset($_SESSION['usuario']);
//		var_dump($_SESSION);
		header( 'Location: index.php' );
}

//Busca todos los datos del usuario identificado

// Comprobamos en el censo
function autentificadoCenso(){
	
	if (isset($_SESSION["usuario"])){
		$usuario=$_SESSION["usuario"];
		
		try {
	   $db = new PDO('mysql:host='.DB_HOST2.';dbname='.DB_NAME2.';charset=utf8', DB_USER2, DB_PASS2);
     // } catch (PDOException $e) { die($e->getMessage()); }
     $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      
            $user=array($usuario);
            $consulta= "SELECT * FROM usuarios WHERE email=?;";
            $result= $db->prepare($consulta);
            $result->execute($user);
			foreach($result as $res){
				$nombre = $res['nombre'];
				$apellidos = $res['apellidos'];
				$id = $res['idu'];
			}
		}catch(PDOException $e ){
			echo $e -> getMessage();
		}	
			
		return array("nombre"=>$nombre, "apellidos"=>$apellidos, "id"=>$id);
	}
}

function autentificado(){
  if(!AUTH_USER){
	  $nombre = 'anonymous_user';
	  $apellidos = 'anonymous_user';
	  $id = -1;
	  return array("nombre"=>$nombre, "apellidos"=>$apellidos, "id"=>$id);
	}
	else 
	if (isset($_SESSION["usuario"])){
		$usuario=$_SESSION["usuario"];
		try{
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $user=array($usuario);
            $consulta= "SELECT * FROM users WHERE email=?;";
            $result= $conn->prepare($consulta);
            $result->execute($user);
			foreach($result as $res){
				$nombre = $res['nombre'];
				$apellidos = $res['apellidos'];
				$id = $res['id'];
			}
		}catch(PDOException $e ){
			echo $e -> getMessage();
		}	

		return array("nombre"=>$nombre, "apellidos"=>$apellidos, "id"=>$id, "email"=>$usuario);
	}
}

//Búsquedas

//Recibe una consulta SQL y tras ejecutarla devuelve el resultado de la misma
function listar($consulta){
	
	$propuesta=array();
	try{
		$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		
		$resultado = $conn -> query($consulta);  
		$resultado->setFetchMode(PDO::FETCH_ASSOC);
		$propuesta = $resultado->fetchall();
		$conn = null;
		
	}catch(PDOException $e ){
		$propuesta =  $e->getMessage();
	}	
	return $propuesta;
}

//Ejecuta una búsqueda preparada que devuelve un resultado único. 
//Usar siempre que se reciban datos del usuario.
function preparada($prep, $consulta){
	
	try{
		$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$result = $conn->prepare($consulta);
		$result->execute($prep);
		$propuesta = $result->fetch(PDO::FETCH_ASSOC);
		$conn = null;
	}catch(PDOException $e ){
		$propuesta =  $e->getMessage();
	}
	
	return $propuesta;
}

//Ejecuta una sentencia preparada que devuelve varios resultados
//Usar siempre que se reciban datos del usuario.
function listarpreparada($prep, $consulta){
	
	try{
		$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$result = $conn->prepare($consulta);
		$result->execute($prep);
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$propuesta = $result->fetchall();
		$conn = null;
	}catch(PDOException $e ){
		$receta =  $e->getMessage();
	}
	
	return $propuesta;
}

//Consulta el autor de la propuesta
function autor_propuesta($buscaID,$consulta_autor){

	try{
		$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$result = $conn->prepare($consulta_autor);
		$result->execute($buscaID);
		foreach ($result as $res){
			$autor=$res['autor_id'];
		}	
		
	}catch(PDOException $e ){
		$result =  $e->getMessage();
	}
	$conn = null;
	$autor=(int)($autor);
	return $autor;
}









// en el censo
function useridCenso(){


	if(isset($_SESSION["usuario"])){
		//Busco el usuario
		try{
			$usuario=$_SESSION["usuario"];
	   $db = new PDO('mysql:host='.DB_HOST2.';dbname='.DB_NAME2.';charset=utf8', DB_USER2, DB_PASS2);
	       // } catch (PDOException $e) { die($e->getMessage()); }
   		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $user=array($usuario);
            $consulta= "SELECT idu FROM usuarios WHERE email=?;";
            $result= $db->prepare($consulta);
            $result->execute($user);
			foreach($result as $idusuario){
				$userid = $idusuario['idu'];
			}
		}catch(PDOException $e ){
			echo $e -> getMessage();
		}	
		
		$db = null; 
		$userid=(int)($userid);
		return $userid;
	}
}



//consigue el id del usuario
function userid(){
	if(isset($_SESSION["usuario"])){
		//Busco el usuario
		try{
			$usuario=$_SESSION["usuario"];
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $user=array($usuario);
            $consulta= "SELECT id FROM users WHERE email=?;";
            $result= $conn->prepare($consulta);
            $result->execute($user);
			foreach($result as $idusuario){
				$userid = $idusuario['id'];
			}
		}catch(PDOException $e ){
			echo $e -> getMessage();
		}	
		
		$conn = null; 
		$userid=(int)($userid);
		return $userid;
	}
}

//Propuestas

//nueva propuesta
function nueva_propuesta($titulo, $propuesta, $sector, $barrio ){

	$autor = userid();
	try{
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$cadena = array('autor'=>$autor, 'titulo'=>$titulo, 'propuesta'=>$propuesta,'sector'=>$sector,'barrio'=>$barrio);
			$result=$conn->prepare( "INSERT INTO prog_propuestas(autor_id, titulo, propuesta, sum_likes, positivos, negativos, comentarios, sector, barrio) 
				VALUES(:autor, :titulo, :propuesta, 1, 1, 0, 0, :sector, :barrio);");
			$result->execute($cadena);
	}catch(PDOException $e ){
			echo $e -> getMessage();
		}	
		
		$conn = null;

	//consigo el id de la propuesta
	try{
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$cadena = array('autor'=>userid(), 'titulo'=>$titulo, 'propuesta'=>$propuesta);
			$result=$conn->prepare( "SELECT id FROM prog_propuestas WHERE propuesta = :propuesta and autor_id =:autor and titulo= :titulo;");
			$result->execute($cadena);
			foreach($result as $res){
				$id = $res['id'];
			}
			//redirije a la propuesta una vez grabada en la base de datos
			header( 'Location: propuesta.php?id='.$id );
		}catch(PDOException $e ){
			echo $e -> getMessage();
		}	
		
		$conn = null; 
}

//Editar propuesta

function editar_propuesta($titulo, $propuesta, $sector, $barrio, $id){

	try{
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$cadena = array('titulo'=>$titulo, 'propuesta'=>$propuesta,'id'=>$id, 'sector'=>$sector,'barrio'=>$barrio);
			$result=$conn->prepare( "UPDATE prog_propuestas SET titulo =:titulo, propuesta=:propuesta, sector=:sector, barrio=:barrio 
				WHERE id=:id;");
			$result->execute($cadena);
			//redirije a la propuesta una vez editada
			header( 'Location: propuesta.php?id='.$id );
	}catch(PDOException $e ){
			echo $e -> getMessage();
		}	
		
		$conn = null;
}

//Randomstring (cadena aleatoria)

function RandomString($length=10,$uc=TRUE,$n=TRUE,$sc=FALSE)
{
    $source = 'abcdefghijklmnopqrstuvwxyz';
    if($uc==1) $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if($n==1) $source .= '1234567890';
    if($sc==1) $source .= '|@#~$%()=^*+[]{}-_';
    if($length>0){
        $rstr = "";
        $source = str_split($source,1);
        for($i=1; $i<=$length; $i++){
            mt_srand((double)microtime() * 1000000);
            $num = mt_rand(1,count($source));
            $rstr .= $source[$num-1];
        }
 
    }
    return $rstr;
}

//FIXME security issues???
function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}