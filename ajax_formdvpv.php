<?php

    require_once('libs/fpdf/fpdf.php');
    require_once('libs/phpmailer/PHPMailerAutoload.php');
    require_once('libs/dompdf/dompdf_config.inc.php');

    require_once('libs/hashids/HashGenerator.php');
    require_once('libs/hashids/Hashids.php');

    add_action( 'wp_ajax_nopriv_formdvpv', 'ajax_formdvpv' );
    add_action( 'wp_ajax_nopriv_formdvpv_pdf', 'ajax_formdvpv_pdf' );
    add_action( 'wp_ajax_nopriv_formdvpv_zip', 'ajax_formdvpv_zip' );
    add_action( 'wp_ajax_formdvpv', 'ajax_formdvpv' );
    add_action( 'wp_ajax_formdvpv_pdf', 'ajax_formdvpv_pdf' );
    add_action( 'wp_ajax_formdvpv_zip', 'ajax_formdvpv_zip' );

    //--------------------------------------

    function generate_uuid()
    {
        return sprintf( 'DVPV%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    function ajax_formdvpv()
    {
        $jsonArr=Array();
        $jsonArr['errors']=Array();
        $jsonArr['navsteps']=Array();
        $jsonArr['errormsg']=Array();

        $fromStep=$_REQUEST['step'];

        // step 1

        $currentStep=0;
        $jsonArr['navsteps'][$currentStep]=true;

        if($_REQUEST['formdata']['firma']=='' )
        {
            $jsonArr['navsteps'][$currentStep]=false;
            $jsonArr['errors'][]='firma';
        }

        if($_REQUEST['formdata']['phone']=='')
        {
            $jsonArr['navsteps'][$currentStep]=false;
            $jsonArr['errors'][]='phone';
        }

        if($_REQUEST['formdata']['strasse']=='')
        {
            $jsonArr['navsteps'][$currentStep]=false;
            $jsonArr['errors'][]='strasse';
        }

        if($_REQUEST['formdata']['strassenr']=='')
        {
            $jsonArr['navsteps'][$currentStep]=false;
            $jsonArr['errors'][]='strassenr';
        }

        if($_REQUEST['formdata']['ort']=='')
        {
            $jsonArr['navsteps'][$currentStep]=false;
            $jsonArr['errors'][]='ort';
        }

        if($_REQUEST['formdata']['plz']=='' || !is_numeric($_REQUEST['formdata']['plz']) || strlen($_REQUEST['formdata']['plz'])!=5 )
        {
            $jsonArr['navsteps'][$currentStep]=false;
            $jsonArr['errors'][]='plz';
        }

        if($_REQUEST['formdata']['vorname']=='' )
        {
            $jsonArr['navsteps'][$currentStep]=false;
            $jsonArr['errors'][]='vorname';
        }

        if($_REQUEST['formdata']['nachname']=='' )
        {
            $jsonArr['navsteps'][$currentStep]=false;
            $jsonArr['errors'][]='nachname';
        }

        if($_REQUEST['formdata']['email']=='')
        {
            $jsonArr['navsteps'][$currentStep]=false;
            $jsonArr['errors'][]='email';
        }

        if($_REQUEST['formdata']['email2']=='')
        {
            $jsonArr['navsteps'][$currentStep]=false;
            $jsonArr['errors'][]='email2';
        }

        if($_REQUEST['formdata']['email'] =='' && $_REQUEST['formdata']['email2']=='')
        {
            $jsonArr['navsteps'][$currentStep]=false;
            $jsonArr['errors'][]='email';
            $jsonArr['errors'][]='email2';
        }
        else
        if($_REQUEST['formdata']['email'] != $_REQUEST['formdata']['email2'])
        {
            $jsonArr['navsteps'][$currentStep]=false;
            $jsonArr['errors'][]='email';
            $jsonArr['errors'][]='email2';
            $jsonArr['errormsg'][]='email_notsame';
        }
        else
        if (!filter_var($_REQUEST['formdata']['email'], FILTER_VALIDATE_EMAIL))
        {
            $jsonArr['navsteps'][$currentStep]=false;
            $jsonArr['errors'][]='email';
            $jsonArr['errors'][]='email2';
            $jsonArr['errormsg'][]='email_invalid';
        }

        if($_REQUEST['formdata']['ustid']=='' &&  $_REQUEST['formdata']['steuernr']=='')
        {
            $jsonArr['navsteps'][$currentStep]=false;
            $jsonArr['errors'][]='ustid';
            $jsonArr['errors'][]='steuernr';
            $jsonArr['errormsg'][]='steuer';
        }

        /////////////////////////////////////////////////////////////////
        // step 2
        $currentStep=1;
        $jsonArr['navsteps'][$currentStep]=true;

         if($_REQUEST['formdata']['nennleistung']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             if($fromStep >= $currentStep) $jsonArr['errors'][]='nennleistung';
         }

        if($_REQUEST['formdata']['hasNoZaehlbezeichnung']==true)
        {
            if($_REQUEST['formdata']['registrnr']=='')
            {
                $jsonArr['navsteps'][$currentStep]=false;
                if($fromStep >= $currentStep) $jsonArr['errors'][]='registrnr';
            }
        }
        else
        {
            if($_REQUEST['formdata']['zaehlbezeichn']=='')
            {
                $jsonArr['navsteps'][$currentStep]=false;
                if($fromStep >= $currentStep) $jsonArr['errors'][]='zaehlbezeichn';
            }
        }
        if($_REQUEST['formdata']['zaehlernr']=='')
        {
            $jsonArr['navsteps'][$currentStep]=false;
            if($fromStep >= $currentStep) $jsonArr['errors'][]='zaehlernr';
        }
        //  if($_REQUEST['formdata']['eigenverbrauch']=='')
        //  {
        //      $jsonArr['navsteps'][$currentStep]=false;
         //
        //      if($fromStep >= $currentStep)
        //         $jsonArr['errors'][]='eigenverbrauch';
        //  }
         if($_REQUEST['formdata']['anlage_strasse']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             if($fromStep >= $currentStep) $jsonArr['errors'][]='anlage_strasse';
         }
         if($_REQUEST['formdata']['anlage_strassenr']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             if($fromStep >= $currentStep) $jsonArr['errors'][]='anlage_strassenr';
         }
         if($_REQUEST['formdata']['anlage_plz']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             if($fromStep >= $currentStep) $jsonArr['errors'][]='anlage_plz';
         }

         if($_REQUEST['formdata']['anlage_ort']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             if($fromStep >= $currentStep) $jsonArr['errors'][]='anlage_ort';
         }

         if($_REQUEST['formdata']['netzbetreiber']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             if($fromStep >= $currentStep) $jsonArr['errors'][]='netzbetreiber';
         }

         if($_REQUEST['formdata']['anlage_fernsteuerung']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             if($fromStep >= $currentStep) $jsonArr['errors'][]='anlage_fernsteuerung';
         }

         // step 3
         $currentStep=2;
         $jsonArr['navsteps'][$currentStep]=true;

         // step 4
         $currentStep=3;
         $jsonArr['navsteps'][$currentStep]=true;

         if($_REQUEST['formdata']['konto_format']=='' || $_REQUEST['formdata']['konto_format']=='new' )
         {
             if($_REQUEST['formdata']['konto_iban']=='')
             {
                 $jsonArr['navsteps'][$currentStep]=false;
                 if($fromStep >= $currentStep) $jsonArr['errors'][]='konto_iban';
             }

             if($_REQUEST['formdata']['konto_bic']=='')
             {
                 $jsonArr['navsteps'][$currentStep]=false;
                 if($fromStep >= $currentStep) $jsonArr['errors'][]='konto_bic';
             }
         }
         else
         {
             if($_REQUEST['formdata']['konto_nr']=='')
             {
                 $jsonArr['navsteps'][$currentStep]=false;
                 if($fromStep >= $currentStep) $jsonArr['errors'][]='konto_nr';
             }

             if($_REQUEST['formdata']['konto_blz']=='')
             {
                 $jsonArr['navsteps'][$currentStep]=false;
                 if($fromStep >= $currentStep) $jsonArr['errors'][]='konto_blz';
             }

             if($_REQUEST['formdata']['konto_institut']=='')
             {
                 $jsonArr['navsteps'][$currentStep]=false;
                 if($fromStep >= $currentStep) $jsonArr['errors'][]='konto_institut';
             }
         }

         if($_REQUEST['formdata']['konto_inhaber']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             if($fromStep >= $currentStep) $jsonArr['errors'][]='konto_inhaber';
         }

         if($jsonArr['navsteps'][0]==true && $jsonArr['navsteps'][1]==true && $jsonArr['navsteps'][2]==true && $jsonArr['navsteps'][3]==true )
         {
             global $wpdb;

             $row=Array();
             $row['id']=generate_uuid();
             $row['json']=json_encode($_REQUEST['formdata']);

             $row['firma'] = $_REQUEST['formdata']['firma'];
             $row['phone'] = $_REQUEST['formdata']['phone'];
             $row['strasse'] = $_REQUEST['formdata']['strasse'];
             $row['strassenr'] = $_REQUEST['formdata']['strassenr'];
             $row['plz'] = $_REQUEST['formdata']['plz'];
             $row['ort'] = $_REQUEST['formdata']['ort'];
             $row['vorname'] = $_REQUEST['formdata']['vorname'];
             $row['nachname'] = $_REQUEST['formdata']['nachname'];
             $row['email'] = $_REQUEST['formdata']['email'];
             $row['nennleistung'] = $_REQUEST['formdata']['nennleistung'];
             $row['zaehlbezeichn'] = $_REQUEST['formdata']['zaehlbezeichn'];
             $row['registrnr'] = $_REQUEST['formdata']['registrnr'];
             $row['zaehlernr'] = $_REQUEST['formdata']['zaehlernr'];
             $row['eigenverbrauch'] = $_REQUEST['formdata']['eigenverbrauch'];
             $row['anlage_strasse'] = $_REQUEST['formdata']['anlage_strasse'];
             $row['anlage_strassenr'] = $_REQUEST['formdata']['anlage_strassenr'];
             $row['anlage_plz'] = $_REQUEST['formdata']['anlage_plz'];
             $row['anlage_ort'] = $_REQUEST['formdata']['anlage_ort'];
             $row['netzbetreiber'] = $_REQUEST['formdata']['netzbetreiber'];
             $row['konto_inhaber'] = $_REQUEST['formdata']['konto_inhaber'];
             $row['konto_iban'] = $_REQUEST['formdata']['konto_iban'];
             $row['konto_bic'] = $_REQUEST['formdata']['konto_bic'];
             $row['konto_nr'] = $_REQUEST['formdata']['konto_nr'];
             $row['konto_blz'] = $_REQUEST['formdata']['konto_blz'];
             $row['konto_institut'] = $_REQUEST['formdata']['konto_institut'];
             $row['ustid'] = $_REQUEST['formdata']['ustid'];
             $row['vermarktung'] = $_REQUEST['formdata']['vermarktung'];
             $row['beginvermarktung'] = $_REQUEST['formdata']['beginvermarktung'];
             $row['anlage_fernsteuerung'] = $_REQUEST['formdata']['anlage_fernsteuerung'];

             $wpdb->insert('next_formdvpv', $row);
             $pkey=$wpdb->insert_id;

             $hashids = new Hashids\Hashids('superSekretNextSalt!!11', 6, 'ABCDEFGHIKMPRSTUWXYZ123456789');
             $docId = $hashids->encode($pkey);
             $jsonArr['docId']=$docId;

             $wpdb->update( 'next_formdvpv', array( 'docid' => $docId ), array( 'pkey' => $pkey ) );

             if($wpdb->last_error!='')
             {
                 $jsonArr['dberror']=$wpdb->last_error;
             }
             else
             {
                 $jsonArr['id']=$row['id'];

                 $body='';
                 $body.='Sehr geehrte(r) '.$_REQUEST['formdata']['vorname'].' '.$_REQUEST['formdata']['nachname'].',';
                 $body.='<br/><br/>';
                 $body.='vielen Dank f&uuml;r Ihr Interesse an der Direktvermarktung Ihrer PV-Anlage &ouml;ber Next Kraftwerke.';
                 $body.='<br/><br/>';
                 $body.='Anbei finden Sie das von uns auf Basis Ihrer Eingaben erzeugte Dokument. Bitte drucken Sie sowohl den Auftrag als auch die Vollmacht aus, unterschreiben beide Dokumente und senden uns diese per Post zu.';
                 $body.='<br/><br/>';
                 $body.='Nach Pr&uuml;fung Ihrer Unterlagen erhalten Sie von uns eine Auftragsbest&auml;tigung mit dem Hinweis auf den Zeitpunkt, ab dem wir die PV-Anlage f&uuml;r Sie voraussichtlich vermarkten k&ouml;nnen. Erst durch die Auftragsbest&auml;tigung ist der Vertrag zur Direktvermarktung Ihrer PV-Anlage mit uns abgeschlossen.';
                 $body.='<br/><br/>';
                 $body.='Bitte beachten Sie die Hinweise zur Fernsteuerbarkeit  sowie die Allgemeinen Vermarktungsbedingungen f&uuml;r kleine Photovoltaik-Anlagen.<br/>';
                 $body.='F&uuml;r PV-Anlagen mit mehr als 800 kW Nennleistung ben&ouml;tigen wir leider ein paar zus&auml;tzliche Informationen: Ein individuelles Angebot k&ouml;nnen Sie hier anfragen.';
                 $body.='<br/><br/>';
                 $body.='Mit freundlichen Gr&uuml;&szlig;en,<br/>';
                 $body.='Ihr Next Kraftwerke Team<br/><br/>';

                 $dompdf = genPDF($row['id']);
                 $filename = getcwd().'/../../formdvpv/Direktvermarktung_'.$docId.'.pdf';
                 $output = $dompdf->output();
                 file_put_contents($filename, $output);

                 $email = new PHPMailer();
                 $email->From      = 'vermarktung@next-kraftwerke.de';
                 $email->FromName  = 'Next Kraftwerke';
                 $email->Subject   = 'Direktvermarktung Ihrer PV-Anlage > Dokumente zur Unterschrift';
                 $email->Body      = $body;

                 $email->AddAddress( $_REQUEST['formdata']['email'] );
                 $email->AddAttachment( $filename , "Direktvermarktung_".$docId.".pdf" );
                //  $email->addBCC('ew@next-kraftwerke.de');
                 $email->isHTML(true);

                 $email->Send();
             }
         }

        echo json_encode($jsonArr);

        die();
    }


    function genPDF($id)
    {
        global $wpdb;
        $rows = $wpdb->get_results('SELECT * FROM next_formdvpv WHERE id="'.esc_sql($id).'";');

        if($wpdb->last_error!='')
        {
            echo($wpdb->last_error);
        }

        $dompdf = new DOMPDF();
        $dompdf->set_paper("A4");
        $day=date("j", strtotime($rows[0]->date));
        $year=date("Y", strtotime($rows[0]->date));
        $month=date("m", strtotime($rows[0]->date));
        if($month==1)$month="Januar";
        if($month==2)$month="Februar";
        if($month==3)$month="März";
        if($month==4)$month="April";
        if($month==5)$month="Mai";
        if($month==6)$month="Juni";
        if($month==7)$month="Juli";
        if($month==8)$month="August";
        if($month==9)$month="September";
        if($month==10)$month="Oktober";
        if($month==11)$month="November";
        if($month==12)$month="Dezember";

        $rows[0]->readableDate=$day.' '.$month.' '.$year;

        if( substr( $rows[0]->beginvermarktung,2,1 )=='.') $rows[0]->beginvermarktung='dem '.$rows[0]->beginvermarktung;

        $twig=initTwig();
        $template = $twig->loadTemplate('form_dvpv_pdf_de.html');
        $html=$template->render(array( 'data' => $rows[0] ));

        $dompdf->load_html($html);

        $dompdf->render();

        return $dompdf;
    }

    function ajax_formdvpv_pdf()
    {
        $dompdf=genPDF($_REQUEST['id']);

        global $wpdb;
        $rows = $wpdb->get_results('SELECT * FROM next_formdvpv WHERE id="'.esc_sql($_REQUEST['id']).'";');

        $dompdf->stream("Direktvermarktung_".$rows[0]->docid.".pdf",[ 'Attachment'=>0  ]);

        die();
    }

    function ajax_formdvpv_zip()
    {
        global $wpdb;
        $rows = $wpdb->get_results('SELECT * FROM next_zip WHERE zip="'.esc_sql($_REQUEST['zip']).'";');

        echo json_encode($rows[0]);
        die();
    }



?>
