<?php

    error_reporting(E_ERROR|E_WARNING);
    ini_set('display_errors', '1');

    add_action( 'wp_ajax_nopriv_getLanguageStructure', 'getLanguageStructure' );
    add_action( 'wp_ajax_getLanguageStructure', 'getLanguageStructure' );

    add_action( 'wp_ajax_nopriv_getLanguage', 'getLanguage' );
    add_action( 'wp_ajax_getLanguage', 'getLanguage' );

    function getLanguageStructure()
    {
        $filename = realpath(dirname(__FILE__))."/language.json";
        $json = file_get_contents( $filename );
        echo $json;
        die();
    }

    function getLanguage()
    {
        $fn='lang_'.basename($_REQUEST['l']).'.json' ; 
        $filename = realpath(dirname(__FILE__)).'/'.$fn;
        if(file_exists($filename))
        {
            $json = file_get_contents( $filename );
            echo $json;
        }
        die();
    }

?>
