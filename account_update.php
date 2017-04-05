<?php

// Permite modificar los datos del usuario

include_once "colpr-config.php";
include_once "lib/PasswordHash.php";
include_once "lib/functions.php";

if (!isset($_SESSION["usuario"])){
	header( 'Location: login.php' );
}else{

$thisuser=autentificado();
$email=$thisuser['email'];
$formdata=array('name_signup'=>$thisuser['nombre'],'apellidos_signup'=>$thisuser['apellidos']);


$template = $twig->loadTemplate('account_update.html');

    // Validate the email
    $allcorrect=true;
    $dataproblems=array();
    // if this is a POST, extract the variables  and validate them 
    if(isset($_POST['email_signup']) || isset($_POST['pass_signup']) ){
        $name = mysql_escape_string($_POST['name_signup']);
        $apellidos = mysql_escape_string($_POST['apellidos_signup']);
        $pass = mysql_escape_string($_POST['pass_signup']);
        $pass2 = mysql_escape_string($_POST['pass2_signup']);
        
        $formdata=$_POST;
        //var_dump($formdata);

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

        // If the user has send passwords
        if ($pass!="" and $pass2!="") {
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
         }        

      if ($allcorrect){
            $msg = gettext('Los datos se han actualizado:').' <br /><br />';          
	        
            if ($pass!="" and $pass2!="") {
                $hasherpw = new PasswordHash(8, FALSE);
                $hashpw = $hasherpw->HashPassword($pass);
                
                // UPDATE password
                $conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
                $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                $muser = array(':hashpw'=>$hashpw,':email'=>$email);
                $result=$conn->prepare( "UPDATE users SET password=:hashpw WHERE email=:email;");
                $result->execute($muser);
            }
            
            // UPDATE name
            $conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
            $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $muser = array(':nombre'=>$name,':apellidos'=>$apellidos,':email'=>$email);
            $result=$conn->prepare( "UPDATE users SET nombre=:nombre, apellidos=:apellidos WHERE email=:email;");
            $result->execute($muser);
                
        
      }  //allcorrect

    }   //POST
    
    
// if this is a GET, just show the form 
$thisuser=autentificado();
$datos = array('lang'=>$lang, 'dataproblems'=>$dataproblems,'formdata'=>$formdata,'finalmessage'=>$msg,'user'=>$thisuser);
	
echo $template->render($datos);
}
?>