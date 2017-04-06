<?php


// Comprueba si el email pasado está en users_can_participate con el hash correcto.
// si es correcto crea el usuario en la tabla users

include_once "colpr-config.php";
$template = $twig->loadTemplate('account_activate.html');

$dataproblems=array();
if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
    // Verify data
    $email = mysql_escape_string($_GET['email']); // Set email variable
    $hash = mysql_escape_string($_GET['hash']); // Set hash variable
    
    $conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    $muser = array('email'=>$email, 'hash'=>$hash);
    $result=$conn->prepare( "SELECT count(*) as nemail FROM users_can_participate WHERE TRIM(UPPER(email))=TRIM(UPPER(:email)) AND hash=:hash;");
    $result->execute($muser);
    foreach ($result as $cont) {
    	$nemail = $cont['nemail'];
    }	
    if ($nemail==0) {
    	array_push($dataproblems,gettext("Este correo no existe o ya ha sido activado o la cadena hash no es correcta").'<br />');
    }else {
        // activate the account

        // FIXME: Do the inserts and deletes with a try with a commit or rollback

        // INSERT user in users
		$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		// Select from users_can_participate
		$result=$conn->prepare( "select nombre, apellidos,email, password from users_can_participate WHERE TRIM(UPPER(email))=TRIM(UPPER(:email));");
        $muser = array('email'=>$email);
		$result->execute($muser);
        list($nombre, $apellidos, $email, $password)=$result->fetch(PDO::FETCH_NUM);
		
		// Insert in users
		$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $muser = array('nombre'=>$nombre,'apellidos'=>$apellidos,'email'=>$email,'pass'=>$password);
        $result=$conn->prepare( "INSERT INTO users(nombre, apellidos, email, password) 
            VALUES(:nombre, :apellidos, LOWER(:email), :pass);");
		$result->execute($muser);

        // DELETE user from users_can_participate
		$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$muser = array('email'=>$email);
		$result=$conn->prepare( "delete from users_can_participate WHERE TRIM(UPPER(email))=TRIM(UPPER(:email));");
		$result->execute($muser);
        
      } 
    } else {
    	array_push($dataproblems,gettext("Este correo no existe o ya ha sido activado o la cadena hash no es correcta").'<br />');
    }
$thisuser = array('nombre'=>$nombre, 'apellidos'=>$apellidos, 'email'=>$email);
$datos = array('lang'=>$lang, 'dataproblems'=>$dataproblems, 'thisuser'=>$thisuser);
	
echo $template->render($datos);

?>