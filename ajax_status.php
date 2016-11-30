<?php

    error_reporting(E_ERROR|E_WARNING);
    ini_set('display_errors', '1');

    add_action( 'wp_ajax_nopriv_status', 'ajax_status' );
    add_action( 'wp_ajax_status', 'ajax_status' );

    function ajax_status()
    {
        header('Access-Control-Allow-Origin: *');

        $response=Array();
        global $wp_version;

        $response['version']=$wp_version;

        // var_dump(wp_get_update_data());
        // $response['updates']=wp_get_update_data();

        $update_plugins = get_site_transient( 'update_plugins' );
        if ( ! empty( $update_plugins->response ) )
            $response['count_plugins'] = count( $update_plugins->response );

        $response['update_wordpress'] = get_core_updates( array('dismissed' => false) );

        echo json_encode($response);

        die();
    }

?>
