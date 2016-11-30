<?php



add_action( 'wp_ajax_next_cleverreach', 'next_cleverreach_ajax_callback' );
add_action( 'wp_ajax_nopriv_next_cleverreach', 'next_cleverreach_ajax_callback' );


function next_cleverreach_ajax_callback() 
{
    $apiKey = "0592af3841157578d4907311b5ebce38-2";
    $wsdl_url="http://api.cleverreach.com/soap/interface_v5.1.php?wsdl";
    $listId = "526774";
    $formId="156538";




    global $wpdb; // this is how you get access to the database
    // $whatever = intval( $_POST['whatever'] );


    $api = new SoapClient($wsdl_url);

     $user = array(
         "email" => $_REQUEST['email'],
         "registered" => time(),
         // "activated" => time(),
         "source" => "Next Website "

     );
     
    $result = $api->receiverAdd($apiKey, $listId, $user);
    // echo '-------------';
    // print_r($result);


    $doidata = array(
        "user_ip" => $_SERVER['REMOTE_ADDR'], //the IP of the user who registered. not yours!
        "user_agent" => $_SERVER['HTTP_USER_AGENT'],
        "referer" => "https://www.nextkraftwerke.de",
        "postdata" => "firtsname:bruce,lastname:whayne,nickname:Batman" //just an example. any txt format will do.
    );

    $result = $api->formsSendActivationMail($apiKey, $formId, $_REQUEST['email'],$doidata);

    // echo '-------------';
    // print_r($result);

    if($result->status=="SUCCESS"){                 //successfull list call
        echo "success";
    }else{              
        echo "fail";
    }
    


    wp_die(); 
}

?>