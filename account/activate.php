<?php


// Comprueba si el email pasado está en users_can_participate con el hash correcto.
// si es correcto crea el usuario en la tabla users

include_once "colpr-config.php";

echo '
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>'.gettext('Activate your Account').'</title>

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

if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
    // Verify data
    //FIXME: Comprobar que email i has están en la base de datos
    $email = mysql_escape_string($_GET['email']); // Set email variable
    $hash = mysql_escape_string($_GET['hash']); // Set hash variable
    
    $conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    $muser = array('email'=>$email, 'hash'=>$hash);
    $result=$conn->prepare( "SELECT count(*) as nemail FROM users_can_participate WHERE email=:email AND hash=:hash;");
    $result->execute($muser);
    foreach ($result as $cont) {
    	$nemail = $cont['nemail'];
    }	
    if ($nemail==0) {
    	echo gettext("Este correo no existe o ya ha sido activado o la cadena hash no es correcta").'<br />';
    }else {
        // activate the account

        echo '<div class="statusmsg">'.gettext('El usuario ') . $email . ' '. gettext('se ha activado, ya puedes acceder a la web ').$entityData['thisweb'].'</div>'; //FIXME: URL
        // echo "Hola: ",$nombre, $apellidos, $email, $password;
        // echo $email;

        // INSERT user in users
		$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		// Select from users_can_participate
		$result=$conn->prepare( "select nombre, apellidos,email, password from users_can_participate WHERE email=:email;");
        $muser = array('email'=>$email);
		$result->execute($muser);
        list($nombre, $apellidos, $email, $password)=$result->fetch(PDO::FETCH_NUM);
		
		// Insert in users
		$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $muser = array('nombre'=>$nombre,'apellidos'=>$apellidos,'email'=>$email,'pass'=>$password);
        $result=$conn->prepare( "INSERT INTO users(nombre, apellidos, email, password) 
            VALUES(:nombre, :apellidos, :email, :pass);");
		$result->execute($muser);




        
        // DELETE user from users_can_participate
		$conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$muser = array('email'=>$email);
		$result=$conn->prepare( "delete from users_can_participate WHERE email=:email;");
		$result->execute($muser);
        
      } 
    } else {
        // Invalid approach
        echo gettext("Este correo no existe o ya ha sido activado o la cadena hash no es correcta");
    }

echo "";  //FIXME: Cerrar divs
?>