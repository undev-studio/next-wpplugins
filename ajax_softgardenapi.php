<?php

    error_reporting(E_ERROR|E_WARNING);
    ini_set('display_errors', '1');

    add_action( 'wp_ajax_nopriv_softgardenapi', 'ajax_softgardenapi_callback' );
    add_action( 'wp_ajax_softgardenapi', 'ajax_softgardenapi_callback' );

    add_action( 'wp_ajax_nopriv_softgardenlist', 'ajax_softgardenlist_callback' );
    add_action( 'wp_ajax_softgardenlist', 'ajax_softgardenlist_callback' );


    require_once("settings_next.php");
    require_once 'libs/unirest/Unirest.php';

    function ajax_softgardenapi_callback()
    {
        $globalDb = new wpdb(DB_USER,DB_PASSWORD,'next_global','localhost');
        if($globalDb->last_error!='') echo($globalDb->last_error); 

        $headers = array('Accept' => 'application/json');
        Unirest\Request::auth('f799c05f-8293-43cc-8c8d-580450847565', '');
        $response = Unirest\Request::get('https://api.softgarden.io/api/rest/v2/frontend/jobboards/27828_extern/jobs', $headers, $query);

        if($response->code==200)
        {
            $globalDb->query("TRUNCATE TABLE jobs_softgarden");

            foreach($response->body->results as $job)
            {
                $keywords='';
                if(isset($job->config->softgarden_keywords)) $keywords=join(',',$job->config->softgarden_keywords);

                $globalDb->insert('jobs_softgarden', array(
                    'sgid' => $job->jobPostingId,
                    'applylink' => $job->applyOnlineLink,
                    'title' => $job->title,
                    'keywords' => $keywords
                ));

                if($globalDb->last_error!='') echo($globalDb->last_error); 
            }

            echo '<script>window.history.back();</script>';
            // echo count($response->body->results.' jobs');
            // echo '<pre>';
            // var_dump($response->body->results);
        }
        else
        {
            echo 'error: invalid response from softgarden.';

            // var_dump($response->body);            
        }

    }

    function ajax_softgardenlist_callback()
    {

        $globalDb = new wpdb(DB_USER,DB_PASSWORD,'next_global','localhost');
        if($globalDb->last_error!='') echo($globalDb->last_error);

        $result=$globalDb->get_results("SELECT * FROM jobs_softgarden");

        if($globalDb->last_error!='') echo($globalDb->last_error);

        echo json_encode($result);

        die();
    }

?>