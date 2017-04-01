<?php

// Permite el registro de una nueva cuenta pero solo si el correo aparece en la tabla users_can_participate
// tras el registro se envía un mail de activación que apunta a activate.php con un email y un hash

include_once "colpr-config.php";
include_once "lib/PasswordHash.php";

// FIXME: Poner HTML en plantillas ????

echo '
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title></title>

    <!-- Estilos -->
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="static/css/main.css">
  	
  	<!-- Font Awesome CDN -->
	<link href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
		
	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700,300" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet" type="text/css">

	<!-- Javascript -->
  	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  	<script src="static/js/jquery.validate.js"></script>
	<script src="static/js/messages_es.js"></script>
	<script src="static/js/bootstrap.min.js"></script>
	<script>
  (function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,"script","//www.google-analytics.com/analytics.js","ga");

  ga("create", "UA-54953216-1", "auto");
  ga("send", "pageview");

</script>

</head>
<body>


<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#myNavbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>

       <a href="https://aprofundirelcanvi.info/" class="navbar-brand"><img src="static/images/logoAProfCanvi.png" class="img-responsive " alt="Aprofundir el Canvi"></a>
    </div>

   <div class="navbar-collapse collapse" id="myNavbar">

     <ul class="nav navbar-nav">
               
        <li><a href="../index.php">Inici</a></li>
        <li><a href="../todas.php">Veure totes</a></li>

     

     <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Idioma        <span class="caret"></span></a>
        <ul class="dropdown-menu">
            <li><a href="../index.php?lang=ca" class="login pull-left"><span>Valencià</span></a></li>
            <li><a href="../index.php?lang=es" class="login pull-left"><span>Castellà</span></a></li>
        </ul>
      </li>
   </ul>



        <ul class="nav navbar-nav navbar-right">

                        <li ><a href="../login.php"><span>Entrar</span></a></li>
               
         
    </ul>


</div>
</div>
</nav>

<div class="container principal">
	<div class="row">
		<div class="central col-md-8">

    <div class="form-wrap">
        <div class="row">
     
';

function writesentence($message) { 
  return '	<div class="row">	
	  <div class="col-xs-12">
	  '.$message.'
	  <div class="row">	
	  <div class="col-xs-12">';
}

    // Validate the email
    $allcorrect=true;
    // if this is a POST, extract the variables  and validate them 
    if(isset($_POST['email_signup']) || isset($_POST['pass_signup']) ){
        $name = mysql_escape_string($_POST['name_signup']);
        $apellidos = mysql_escape_string($_POST['apellidos_signup']);
        $email = mysql_escape_string($_POST['email_signup']);
        $pass = mysql_escape_string($_POST['pass_signup']);
        $pass2 = mysql_escape_string($_POST['pass2_signup']);

        $acceptconditions = mysql_escape_string($_POST['acceptconditions']);
        
        if (!$acceptconditions){
            echo writesentence(gettext('Se deben aceptar las ').'<a target="_blank" href="../aviso-legal.php">'.gettext('Condiciones legales').'</a>');

            $allcorrect=false;

        }

        // name is empty?
        if(empty($_POST['name_signup'])){

            echo writesentence(gettext("El nombre está vacío"));

            $allcorrect=false;
        }

        // surname is empty?
        if(empty($_POST['apellidos_signup'])){
            echo writesentence(gettext("Los apellidos están vacíos"));
            $allcorrect=false;
        }

        // email is empty?
        if(empty($_POST['email_signup'])){
            echo writesentence(gettext("El correo está vacío"));
            $allcorrect=false;
        }else {
            // email is valid??
            if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)){
                echo writesentence(gettext("El correo no es válido"));
                $allcorrect=false;

            }
            
             // Ya existe el usuario en users    
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$muser = array('email'=>$email);
			$result=$conn->prepare( "SELECT count(*) as nemail FROM users WHERE email=:email;");
			$result->execute($muser);
			foreach ($result as $cont) {
				$nemail = $cont['nemail'];
			}	
			if ($nemail>0) {
				echo writesentence(gettext("El usuario ").$email.gettext(" ya existe en la base de datos"));
                $allcorrect=false;
			} else {	
            // Está en la base de datos de mails válidos???
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$muser = array('email'=>$email);
			$result=$conn->prepare( "SELECT count(*) as nemail FROM users_can_participate WHERE email=:email;");
			$result->execute($muser);
			foreach ($result as $cont) {
				$nemail = $cont['nemail'];
			}	
			if ($nemail==0) {
				echo writesentence(gettext("El correo no está autorizado para crear un nuevo usuario"));
                $allcorrect=false;
			}	
            }

                                   
            
        }

        // are the two password equals?
        if ($pass!=$pass2) {
            echo writesentence(gettext("Los passwords son diferentes"));
            $allcorrect=false;
        }

        // Is the password long enough??
        $passwordminlen=6;
        if (strlen($pass)<$passwordminlen) {
            echo writesentence(gettext("El password debe tener al menos ").$passwordminlen.gettext(" carácteres"));
            $allcorrect=false;
        }
        

      if ($allcorrect){
          $msg = gettext('La cuenta se ha creado,').' <br />'.gettext('por favor, actívala accediendo al link que se te ha enviado por correo a ').$email.'.';
          $msg .= gettext('<br /><br /> <b>Revisa la carpeta de spam o correo basura</b> si no ves el correo en la carpeta de entrada.');          
          $msg .= gettext('<br />Marca el correo como <b>«No es spam»</b>.');          
          $hash = md5( rand(0,10000) );
          // Insert
          // FIXME          
          
          // Enviar correo
          $to      = $email; // Send email to the user
          $subject = gettext('Validación de correo');  
          $message = gettext('Mensaje Validación correo');
          $messorig='
 
Thanks for signing up!
Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.
 
Please click this link to activate your account:';

           $urlactivate=$entityData['thisweb'].'/account/activate.php?email='.$email.'&hash='.$hash.'&lang='.$lang;

           $message=$message.'<br /> <a href="'.$urlactivate.'">'.$urlactivate.'</a>'; 
           //FIXME: sacarlo de configuración
                   
          $headers = "From: noreply@aprofundirelcanvi.info" . "\r\n";  //FIXME: Cogerlo de configuración
          $headers .="Reply-To: noreply@aprofundirelcanvi.info" . "\r\n" ;
          $headers .="X-Mailer: PHP/" . phpversion();
          $headers .= "MIME-Version: 1.0\r\n";
          $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";   
          
          mail($to, $subject, $message, $headers);
          
        	$hasherpw = new PasswordHash(8, FALSE);
	        $hashpw = $hasherpw->HashPassword($pass);
        
            // UPDATE hash and password
			$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
			$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$muser = array('name'=>$name,'apellidos'=>$apellidos,'email'=>$email,'hashpw'=>$hashpw,'hash'=>$hash);
			$result=$conn->prepare( "update users_can_participate set nombre=:name, apellidos=:apellidos, hash=:hash,password=:hashpw WHERE email=:email;");
			$result->execute($muser);

        
      }  //allcorrect

    }   //POST
    
    // If the account has been created show a message
    if (isset($msg)){  
        echo '<div class="">'.$msg.'</div>';   //FIXME: Poner div tipo nota
        // echo $message; //FIXME: Quitar FIXME FIXME
        return;
    } 
    
    // if this is a GET, show the form 

// FIXME: Poner el mensaje de condiciones para registrarse en el fichero de configuración y traducido

    echo '        
            <div id="signup">
                <p>Per a registrar-te has d\'haver signat <a href="https://aprofundirelcanvi.info/">https://aprofundirelcanvi.info/</a> </p>
                <div class="form-header">
                    <i class="fa fa-user"></i>
                    <h2>Alta</h2>
                </div>
                <form id="signup_form" method="post" action="">
                <div class="form-main">
                    <div class="form-group">
                        <input type="text" name="name_signup" id="name_signup" class="form-control" placeholder="'.gettext('Nombre').'">
                        <input type="text" name="apellidos_signup" id="apellidos_signup" class="form-control" placeholder="'.gettext('Apellidos').'">
                        <input type="text" name="email_signup" id="email_signup" class="form-control" placeholder="'.gettext('Email').'">
                        <input type="password" name="pass_signup" id="pass_signup" class="form-control" placeholder="'.gettext('Contraseña').'">
                        <input type="password" name="pass2_signup" id="pass2_signup" class="form-control" placeholder="'.gettext('Repetir Contraseña').'">
                        <input type="checkbox" name="acceptconditions" value="acceptconditions"> <span style="color:white;">'.gettext('He leído y acepto').'</span> <a target="_blank" href="../aviso-legal.php" style="color:white;">'.gettext('las condiciones legales').'</a>
                    </div>
                    <button id="button_signup" type="submit" class="btn btn-block signin">'.gettext('Registrarse').'</button>
                </form>
                </div>
                <div class="form-footer">

                <a target="_blank" href="../aviso-legal.php">'.gettext('Aviso legal').'</a>
                    </div>

                </div>  
        
    </div>
</body>
</html>
';
?>