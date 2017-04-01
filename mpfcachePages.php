<?php
// This script forces the generation of the cache for all the templates in the directory $tmpDir
//
// with the navigator acces: http://localhost/app/mpfcachePages.php
//
// then in the bash prompt execute
//
//xgettext --default-domain=messages -p ./locale --from-code=UTF-8 -n --omit-header -L PHP /tmp/cache/*.php


//http://twig-extensions.readthedocs.io/en/latest/i18n.html

include_once "lib/functions.php";
include_once "colpr-config.php";

$tplDir = dirname(__FILE__).'/vistas';
$tmpDir = '/tmp/cache/';
$loader = new Twig_Loader_Filesystem($tplDir);


/* Descomentaríamos la siguiente línea para mostrar errores de php en el fichero: */
ini_set('display_errors', '1');




//Una instancia del cargador (le pasamos donde están las vistas
$loader = new Twig_Loader_Filesystem(realpath(dirname(__FILE__)."/vistas"));

$twig = new Twig_Environment($loader, array(
    'cache' => $tmpDir,
    'auto_reload' => true
));

//MPF add i18n extension
$twig->addExtension(new Twig_Extensions_Extension_I18n());



if(isset($_GET['page'])) {
   $page=$_GET['page'];
   $file=$tplDir.'/'.$page;
    echo $file."<br/>";
   if (file_exists($file)) {
        $twig->loadTemplate(str_replace($tplDir.'/', '', $file));
    }
} else {


// iterate over all your templates
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tplDir), RecursiveIteratorIterator::LEAVES_ONLY) as $file)
{
    // force compilation
    echo $file."<br/>";
    if ($file->isFile()) {
        $twig->loadTemplate(str_replace($tplDir.'/', '', $file));
    }
}

}