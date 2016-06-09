<?php

    require_once('libs/phpmailer/PHPMailerAutoload.php');

    error_reporting(E_ERROR|E_WARNING);
    ini_set('display_errors', '1');

    add_action( 'wp_ajax_nopriv_widgetforms', 'ajax_widgetforms' );
    add_action( 'wp_ajax_widgetforms', 'ajax_widgetforms' );

    function ajax_widgetforms()
    {
        $jsonArr=Array();
        $jsonArr['errors']=Array();
        $jsonArr['navsteps']=Array();
        // var_dump($_REQUEST["formdata"]);

        $form=$_REQUEST["formdata"];
        
        if($form['form']=='flexheft')
        {
            if($form['name']=='' ) $jsonArr['errors'][]='name';
            if($form['street']=='' ) $jsonArr['errors'][]='street';
            if($form['city']=='' ) $jsonArr['errors'][]='city';
            if($form['phone']=='' ) $jsonArr['errors'][]='phone';
            if($form['email']=='' ) $jsonArr['errors'][]='email';
            if($form['zip']=='' ) $jsonArr['errors'][]='zip';
            if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) $jsonArr['errors'][]='email';
        }

        if(count($jsonArr['errors'])==0)
        {
            $email = new PHPMailer();
            $email->CharSet = 'utf-8';
            $email->From      = 'beratung@next-kraftwerke.de';
            $email->FromName  = 'Next Kraftwerke';
            $email->Subject   = 'Flex-Heft Bestellung';
            $email->Body      = 'Vielen Dank, dass Sie unser Flex-Heft bestellt haben! <br/><br/>Es sollte spätestens in zwei Wochen bei Ihnen ankommen. <br/>Sollte sich etwas verzögern, melden Sie sich bitte telefonisch unter 0221 820085 0.';

            $email->AddAddress( $form['email'] );
            $email->addBCC('tom@undev.de');
            $email->addBCC('beratung@next-kraftwerke.de');
            $email->addBCC('presse@next-kraftwerke.de');
            $email->isHTML(true);

            $email->Send();


            $email = new PHPMailer();
            $email->CharSet = 'utf-8';
            $email->From      = 'beratung@next-kraftwerke.de';
            $email->FromName  = 'Next Kraftwerke';
            $email->Subject   = 'Flex-Heft Bestellung';
            
            $email->Body      = 'Das Formular wurde ausgefüllt:<br/><br/>';
            $email->Body     .= '<b>Name:</b> '.$form['name'].' <br/>';
            $email->Body     .= '<b>Firma:</b> '.$form['company'].' <br/>';
            $email->Body     .= '<b>Strasse:</b> '.$form['street'].' <br/>';
            $email->Body     .= '<b>PLZ/Ort:</b> '.$form['zip'].' '.$form['city'].' <br/>';
            $email->Body     .= '<b>Telefonnummer:</b> '.$form['phone'].' <br/>';
            $email->Body     .= '<b>Nachricht:</b> '.$form['msg'].' <br/>';

            $email->AddAddress( 'beratung@next-kraftwerke.de' );
            $email->addCC('tom@undev.de');
            $email->addCC('romina.pankoke@gmail.com');
            $email->addCC('presse@next-kraftwerke.de');
            $email->isHTML(true);

            $email->Send();


            $jsonArr['mailsent']=true;
        }

        echo json_encode($jsonArr);

        die();
    }


?>
