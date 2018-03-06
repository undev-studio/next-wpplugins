<?php

    error_reporting(E_ERROR|E_WARNING);
    ini_set('display_errors', '1');

    add_action( 'wp_ajax_nopriv_references', 'ajax_references_callback' );
    add_action( 'wp_ajax_references', 'ajax_references_callback' );


    function ajax_references_callback()
    {
        global $wpdb;
        $cat=335;

        $sql='SELECT * FROM next_references';

        if(isset($_REQUEST['tag']))
        {
            $tag=esc_sql($_REQUEST['tag']);
            $sql='SELECT * FROM next_references WHERE tags LIKE "%'.$tag.'%"';
        }

        $refs=$wpdb->get_results( $sql );

        foreach ($refs as &$ref)
        {
            $ref->url = get_permalink($ref->page);    
        }

        $response=Array();

        $response['references']=$refs;

        echo json_encode($response);

        die();
    }



?>
