<?php

    error_reporting(E_ERROR|E_WARNING);
    ini_set('display_errors', '1');

    add_action( 'wp_ajax_nopriv_quotes', 'ajax_quotes_callback' );
    add_action( 'wp_ajax_quotes', 'ajax_quotes_callback' );


    function ajax_quotes_callback()
    {
        global $wpdb;
        $cat=335;

        $sql='SELECT * FROM next_quotes';

        $tag=esc_sql($_REQUEST['tag']);

        if(isset($_REQUEST['tag']))
        {
            
            $sql='SELECT * FROM next_quotes WHERE tags LIKE "%'.$tag.'%"';
        }

        $refs=$wpdb->get_results( $sql );

        $response=Array();
        $response['quotes']=$refs;
        $response['tag']=$tag;

        echo json_encode($response);

        die();
    }



?>
