<?php


require_once('libs/phpmailer/PHPMailerAutoload.php');

add_action( 'wp_ajax_nextform', 'nextform_ajax_callback' );
add_action( 'wp_ajax_nopriv_nextform', 'nextform_ajax_callback' );


function nextform_ajax_callback() 
{
    $data=$_REQUEST['formdata'];
    $body='';

    $toEmail='tom@undev.de';

    global $wpdb;
    $lastid = $wpdb->insert('emaillog', array(
      'content' => json_encode($data),
      'templatename' => 'nextform',
      'to' => $toEmail
      )
    );

    foreach ($data['results'] as &$r)
    {
        $body.=''.$r['title'].': <b>'.$r['value'].'</b><br/>';
    }

    $email = new PHPMailer();
    $email->CharSet = 'utf-8';
    $email->From      = 'noreply@next-kraftwerke.de';
    $email->FromName  = 'Next Formular';
    $email->Subject   = 'frormemail';
    $email->Body      = $body;

    $email->AddAddress($toEmail);
    $email->isHTML(true);
    $email->Send();

    echo 'aa{"success":true}';

    wp_die(); 
}

?>