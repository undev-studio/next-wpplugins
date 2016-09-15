<?php

    error_reporting(E_ERROR|E_WARNING);
    ini_set('display_errors', '1');

    add_action( 'wp_ajax_nopriv_getLanguageStructure', 'getLanguageStructure' );
    add_action( 'wp_ajax_getLanguageStructure', 'getLanguageStructure' );

    function getLanguageStructure()
    {
        echo readfile( realpath(dirname(__FILE__))."/language.json");
    }

?>
