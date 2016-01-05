<?php

    require_once('libs/fpdf/fpdf.php');

    add_action( 'wp_ajax_nopriv_formdvpv', 'ajax_formdvpv' );
    add_action( 'wp_ajax_nopriv_formdvpv_pdf', 'ajax_formdvpv_pdf' );
    add_action( 'wp_ajax_nopriv_formdvpv_zip', 'ajax_formdvpv_zip' );
    add_action( 'wp_ajax_formdvpv', 'ajax_formdvpv' );
    add_action( 'wp_ajax_formdvpv_pdf', 'ajax_formdvpv_pdf' );
    add_action( 'wp_ajax_formdvpv_zip', 'ajax_formdvpv_zip' );

    //--------------------------------------
    function generate_uuid() {
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

             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='nennleistung';
         }
         if($_REQUEST['formdata']['zaehlbezeichn']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;

             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='zaehlbezeichn';
         }
         if($_REQUEST['formdata']['registrnr']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;

             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='registrnr';
         }
         if($_REQUEST['formdata']['zaehlernr']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;

             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='zaehlernr';
         }
         if($_REQUEST['formdata']['eigenverbrauch']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;

             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='eigenverbrauch';
         }
         if($_REQUEST['formdata']['anlage_strasse']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;

             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='anlage_strasse';
         }
         if($_REQUEST['formdata']['anlage_strassenr']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;

             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='anlage_strassenr';
         }
         if($_REQUEST['formdata']['anlage_plz']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;

             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='anlage_plz';
         }

         if($_REQUEST['formdata']['anlage_ort']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;

             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='anlage_ort';
         }

         if($_REQUEST['formdata']['netzbetreiber']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;

             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='netzbetreiber';
         }


         // step 3
         $currentStep=2;
         $jsonArr['navsteps'][$currentStep]=true;

         // step 4
         $currentStep=3;
         $jsonArr['navsteps'][$currentStep]=true;

         if($_REQUEST['formdata']['konto_inhaber']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='konto_inhaber';
         }

         if($_REQUEST['formdata']['konto_iban']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='konto_iban';
         }

         if($_REQUEST['formdata']['konto_bic']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='konto_bic';
         }

         if($_REQUEST['formdata']['konto_nr']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='konto_nr';
         }

         if($_REQUEST['formdata']['konto_blz']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='konto_blz';
         }

         if($_REQUEST['formdata']['konto_institut']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             if($fromStep >= $currentStep )
                $jsonArr['errors'][]='konto_institut';
         }


         if($jsonArr['navsteps'][0]==true && $jsonArr['navsteps'][1]==true && $jsonArr['navsteps'][2]==true && $jsonArr['navsteps'][3]==true )
         {
             $row=Array();
             $row['id']=generate_uuid();
             $row['json']=json_encode($_REQUEST['formdata']);

             global $wpdb;
             $wpdb->insert('next_formdvpv', $row);

            //  echo json_encode($row);


             $jsonArr['id']=$row['id'];

         }

        echo json_encode($jsonArr);

        //  if($_REQUEST['pdf']==true)
        //  {
        //  }




        die();
    }

    function ajax_formdvpv_pdf()
    {
        global $wpdb;
        $rows = $wpdb->get_results('SELECT * FROM next_formdvpv WHERE id="'.esc_sql($_REQUEST['id']).'";');

        if($wpdb->last_error!='')
        {
            echo($wpdb->last_error);
            // if($DEBUG_SHOW_SQL) printError($wpdb->last_query);
            // return true;
        }

        // var_dump($rows[0]);
        $data=json_decode($rows[0]->json);



        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',10);

        $pdf->Cell(0,10, "ID: ".$rows[0]->id,0,1);
        $pdf->Cell(0,10, "Firma: ".$data->firma,0,1);




        $pdf->Cell(0,6, 'firma: '.$data->firma ,0,1);
        $pdf->Cell(0,6, 'phone: '.$data->phone ,0,1);
        $pdf->Cell(0,6, 'strasse: '.$data->strasse ,0,1);
        $pdf->Cell(0,6, 'strassenr: '.$data->strassenr ,0,1);
        $pdf->Cell(0,6, 'plz: '.$data->plz ,0,1);

        $pdf->Cell(0,6, 'vorname: '.$data->vorname ,0,1);
        $pdf->Cell(0,6, 'nachname: '.$data->nachname ,0,1);

        $pdf->Cell(0,6, 'email: '.$data->email ,0,1);

        $pdf->Cell(0,6, 'nennleistung: '.$data->nennleistung ,0,1);
        $pdf->Cell(0,6, 'zaehlbezeichn: '.$data->zaehlbezeichn ,0,1);
        $pdf->Cell(0,6, 'registrnr: '.$data->registrnr ,0,1);
        $pdf->Cell(0,6, 'zaehlernr: '.$data->zaehlernr ,0,1);
        $pdf->Cell(0,6, 'eigenverbrauch: '.$data->eigenverbrauch ,0,1);
        $pdf->Cell(0,6, 'anlage_strasse: '.$data->anlage_strasse ,0,1);
        $pdf->Cell(0,6, 'anlage_strassenr: '.$data->anlage_strassenr ,0,1);
        $pdf->Cell(0,6, 'anlage_plz: '.$data->anlage_plz ,0,1);

        $pdf->Cell(0,6, 'anlage_ort: '.$data->anlage_ort ,0,1);
        $pdf->Cell(0,6, 'netzbetreiber: '.$data->netzbetreiber ,0,1);
        $pdf->Cell(0,6, 'konto_inhaber: '.$data->konto_inhaber ,0,1);
        $pdf->Cell(0,6, 'konto_iban: '.$data->konto_iban ,0,1);
        $pdf->Cell(0,6, 'konto_bic: '.$data->konto_bic ,0,1);

        $pdf->Cell(0,6, 'konto_nr: '.$data->konto_nr ,0,1);
        $pdf->Cell(0,6, 'konto_blz: '.$data->konto_blz ,0,1);
        $pdf->Cell(0,6, 'konto_institut: '.$data->konto_institut ,0,1);

        $pdf->Output();
        // $pdf->Output('../','F');

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
