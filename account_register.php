<?php

// Permite el registro de una nueva cuenta pero solo si el correo aparece en la tabla users_can_participate
// tras el registro se envía un mail de activación que apunta a activate.php con un email y un hash

include_once "colpr-config.php";
include_once "lib/PasswordHash.php";

include_once "lib/functions.php";

$template = $twig->loadTemplate('account_register.html');

    // Validate the email
    $allcorrect=true;
    $dataproblems=array();
    // if this is a POST, extract the variables  and validate them 
    if(isset($_POST['email_signup']) || isset($_POST['pass_signup']) ){
        $name = mysql_escape_string($_POST['name_signup']);
        $apellidos = mysql_escape_string($_POST['apellidos_signup']);
        $email = mysql_escape_string($_POST['email_signup']);
        $pass = mysql_escape_string($_POST['pass_signup']);
        $pass2 = mysql_escape_string($_POST['pass2_signup']);
        $acceptconditions = mysql_escape_string($_POST['acceptconditions']);
        
        $formdata=$_POST;
        //var_dump($formdata);

        if (!isset($formdata['acceptconditions'])) {
            array_push($dataproblems,gettext('Se deben aceptar las ').'<a target="_blank" href="aviso-legal.php">'.gettext('Condiciones legales').'</a>');
            $allcorrect=false;
        }

        // name is empty?
        if(empty($_POST['name_signup'])){
            array_push($dataproblems,gettext("El nombre está vacío"));
            $allcorrect=false;
        }

        // surname is empty?
        if(empty($_POST['apellidos_signup'])){
            array_push($dataproblems,gettext("Los apellidos están vacíos"));
            $allcorrect=false;
        }

        // email is empty?
        if(empty($_POST['email_signup'])){
            array_push($dataproblems,gettext("El correo está vacío"));
            $allcorrect=false;
        // email is correct?
        }else {
            // email is valid??
            if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", trim($email))) {
                array_push($dataproblems,gettext("El correo no es válido"));
                $allcorrect=false;
            }         
             // Ya existe el usuario en users?
    		$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
    		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    		$muser = array('email'=>$email);
    		$result=$conn->prepare( "SELECT count(*) as nemail FROM users WHERE TRIM(UPPER(email))=TRIM(UPPER(:email));");
    		$result->execute($muser);
    		foreach ($result as $cont) {
    			$nemail = $cont['nemail'];
    		}	
    		if ($nemail>0) {
    			array_push($dataproblems,gettext("El usuario ").$email.gettext(" ya existe en la base de datos"));
                $allcorrect=false;
    		} else {	
                // Está en la base de datos de mails válidos???
           		$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
        		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        		$muser = array('email'=>$email);
        		$result=$conn->prepare( "SELECT count(*) as nemail FROM users_can_participate WHERE TRIM(UPPER(email))=TRIM(UPPER(:email));");
        		$result->execute($muser);
        		foreach ($result as $cont) {
        			$nemail = $cont['nemail'];
        		}	
        		if ($nemail==0) {
        			array_push($dataproblems,gettext("El correo no está autorizado para crear un nuevo usuario"));
                    $allcorrect=false;
        		}	
            }           
        }

        // are the two password equals?
        if ($pass!=$pass2) {
            array_push($dataproblems,gettext("Los passwords son diferentes"));
            $allcorrect=false;
        }

        // Is the password long enough??
        $passwordminlen=6;
        if (strlen($pass)<$passwordminlen) {
            array_push($dataproblems,gettext("El password debe tener al menos ").$passwordminlen.gettext(" carácteres"));
            $allcorrect=false;
        }
        

      if ($allcorrect){
          $msg = gettext('La cuenta se ha creado,').' <br />'.gettext('por favor, actívala accediendo al link que se te ha enviado por correo a ').$email.'.';
          $msg .= gettext('<br /><br /> <b>Revisa la carpeta de spam o correo basura</b> si no ves el correo en la carpeta de entrada.');          
          $msg .= gettext('<br />Marca el correo como <b>«No es spam»</b>.');          
          $hash = md5( rand(0,10000) );
          
          // Enviar correo
          $to      = $email; // Send email to the user
          $subject = gettext('Validación de correo');  
          $message = gettext('Mensaje Validación correo');

          $urlactivate=$entityData['thisweb'].'/account_activate.php?email='.$email.'&hash='.$hash.'&lang='.$lang;

          $message=$message.'<br /> <a href="'.$urlactivate.'">'.$urlactivate.'</a>'; 
                   
          $headers = "From: ".$entityData['noreplytoemail']. "\r\n";  
          $headers .="Reply-To: ".$entityData['noreplytoemail'] . "\r\n" ;
          $headers .="X-Mailer: PHP/" . phpversion();
          $headers .= "MIME-Version: 1.0\r\n";
          $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";   
          
          mail($to, $subject, $message, $headers);
          //echo $message;
          
        	$hasherpw = new PasswordHash(8, FALSE);
	        $hashpw = $hasherpw->HashPassword($pass);
        
            // UPDATE hash and password
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$muser = array('name'=>$name,'apellidos'=>$apellidos,'email'=>$email,'hashpw'=>$hashpw,'hash'=>$hash);
			$result=$conn->prepare( "update users_can_participate set nombre=:name, apellidos=:apellidos, hash=:hash,password=:hashpw WHERE TRIM(UPPER(email))=TRIM(UPPER(:email));");
			$result->execute($muser);

        
      }  //allcorrect

    }   //POST
    
   
// if this is a GET, just show the form 

$datos = array('lang'=>$lang, 'dataproblems'=>$dataproblems,'formdata'=>$formdata,'finalmessage'=>$msg);
	
echo $template->render($datos);

?>