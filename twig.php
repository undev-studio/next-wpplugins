<?php

require_once __DIR__.'/Twig/Autoloader.php';
require_once __DIR__.'/Twig/ExtensionInterface.php';
require_once __DIR__.'/Twig/Extension.php';

function initTwig()
{
    global $twig;

    Twig_Autoloader::register();

    $loader = new Twig_Loader_Filesystem(__DIR__.'/templates/');
    $twig = new Twig_Environment($loader, array());
    return $twig;
}



?>