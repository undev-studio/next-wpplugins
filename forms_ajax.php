<?php

require_once('language.php');
require_once('libs/phpmailer/PHPMailerAutoload.php');



add_action( 'wp_ajax_nextform', 'nextform_ajax_callback' );
add_action( 'wp_ajax_nopriv_nextform', 'nextform_ajax_callback' );

function nextform_validate($data,$form)
{
    $errors=array();


    // echo '------------- data';
    // var_dump($form);
    // echo '------------- END DATA';


    foreach($data['results'] as &$dataField)
    {
        foreach($form as &$formField)
        {
            if($formField->title == $dataField['title'])
            {
                if($formField->required==true)
                {
                    if( !isset($dataField['value']) || strlen($dataField['value'])==0)
                    {
                        $errors[]=array(
                            "message" => $formField->title.': '.nextTranslate('forms_required'),
                            "field" => $formField->title,
                            "fieldId"=>$dataField['id']
                            );
                    }
                }

                if($formField->type=="input_plz")
                {
                    if(!is_numeric($dataField['value']) || strlen($dataField['value'])!=5)
                    {
                        $errors[]=array(
                            "message" => $formField->title.': '.nextTranslate('forms_invalid_zip'),
                            "field" => $formField->title,
                            "fieldId"=>$dataField['id']
                            );
                    }
                }

                if($formField->type=="input_email")
                {
                    if (!filter_var($dataField['value'], FILTER_VALIDATE_EMAIL))
                    {
                        $errors[]=array(
                            "message" => $formField->title.': '.nextTranslate('forms_invalid_email'),
                            "field" => $formField->title,
                            "fieldId"=>$dataField['id']
                            );
                    }
                }
            }
        }
    }

    return $errors;
}


function nextform_getEmailAdresses($form,$formFields,$data)
{
    global $wpdb;
    $toEmail=array();
    

    if(strpos($form->email, ',') !== false)
    {
        // echo 'true';
        $toEmail=explode(',',$form->email);

    }
    else
    {
        $toEmail[]=$form->email;
    }

    
    if($form->maillogic=='zip')
    {
        foreach($formFields as &$formField)
        {
            if($formField->type=="input_plz")
            {
                foreach($data['results'] as &$dataField)
                {
                    if($formField->title == $dataField['title'])
                    {
                        $q='SELECT * FROM erloes_plz WHERE start <= '.(int)$dataField['value'].' AND end > '.(int)$dataField['value'];
                        $res = $wpdb->get_results($q);
                        $toEmail[]=$res[0]->email;
                    }
                }
            }
        }
    }

    if( count($toEmail)==0 ) $toEmail[]='tom-unknownemail@undev.de';

    return $toEmail;
}


function nextform_ajax_callback() 
{
    global $wpdb;

    $data=$_REQUEST['formdata'];
    $form = $wpdb->get_results('SELECT * FROM next_forms WHERE id='.(int)$_REQUEST['formId'].';');

    $title=$form[0]->title;
    $formFields=json_decode($form[0]->rowdata);
    $formFields=array_merge($formFields->column1,$formFields->column2);
    $emails=nextform_getEmailAdresses($form[0],$formFields,$data);
    $flatEmails=join(',',$emails);

    $errors=nextform_validate($data,$formFields);

    if(count($errors)>0)
    {
        echo '{"errors":'.json_encode($errors).',"title":"'.nextTranslate('forms_form_error').'"}';
        wp_die();
    }

    $lastid = $wpdb->insert('emaillog', array(
        'content' => json_encode($data),
        'templatename' => 'forms '.$title,
        'to' => $flatEmails
        ));

    $body='<h3>'.$title.'</h3><br/>';

    foreach($data['results'] as &$r)
    {
        $body.=$r['title'];

        if(isset($r['value']) && isset($r['title']) && $r['title']!='' ) $body.=": ";

        $body.='<b>'.$r['value'].'</b>';
        $body.='<br/>';
    }

    $body.='<br/>send to: '.$flatEmails;

    $email = new PHPMailer();
    $email->CharSet = 'utf-8';
    $email->From      = 'noreply@next-kraftwerke.de';
    $email->FromName  = 'Next Formular';
    $email->Subject   = '[Next Forms] - '.$title;
    $email->Body      = $body;

    $counter=0;
    foreach($emails as &$toEmail)
    {
        if($counter==0)$email->AddAddress($toEmail);
        else $email->addBCC($toEmail);
        $counter++;
    }
    
    $email->isHTML(true);
    $email->Send();

    echo '{"success":true,"redirect":"'.get_permalink($form[0]->successPage).'"}';

    wp_die();
}

























?>