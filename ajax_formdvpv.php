<?php

    require_once('libs/fpdf/fpdf.php');


    //--------------------------------------
    function generate_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }


    function ajax_formDvPv()
    {
        $jsonArr=Array();
        $jsonArr['errors']=Array();
        $jsonArr['navsteps']=Array();



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


        if($_REQUEST['formdata']['plz']=='')
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

         if($_REQUEST['formdata']['email'] != $_REQUEST['formdata']['email2'])
         {
             $jsonArr['errors'][]='email';
             $jsonArr['errors'][]='email2';
         }


         // step 2
         $currentStep=1;
         $jsonArr['navsteps'][$currentStep]=true;

         if($_REQUEST['formdata']['nennleistung']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             $jsonArr['errors'][]='nennleistung';
         }
         if($_REQUEST['formdata']['zaehlbezeichn']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             $jsonArr['errors'][]='zaehlbezeichn';
         }
         if($_REQUEST['formdata']['registrnr']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             $jsonArr['errors'][]='registrnr';
         }
         if($_REQUEST['formdata']['zaehlernr']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             $jsonArr['errors'][]='zaehlernr';
         }
         if($_REQUEST['formdata']['eigenverbrauch']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             $jsonArr['errors'][]='eigenverbrauch';
         }
         if($_REQUEST['formdata']['anlage_strasse']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             $jsonArr['errors'][]='anlage_strasse';
         }
         if($_REQUEST['formdata']['anlage_strassenr']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             $jsonArr['errors'][]='anlage_strassenr';
         }
         if($_REQUEST['formdata']['anlage_plz']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             $jsonArr['errors'][]='anlage_plz';
         }

         if($_REQUEST['formdata']['anlage_ort']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             $jsonArr['errors'][]='anlage_ort';
         }

         if($_REQUEST['formdata']['netzbetreiber']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
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
             $jsonArr['errors'][]='konto_inhaber';
         }

         if($_REQUEST['formdata']['konto_iban']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             $jsonArr['errors'][]='konto_iban';
         }

         if($_REQUEST['formdata']['konto_bic']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             $jsonArr['errors'][]='konto_bic';
         }

         if($_REQUEST['formdata']['konto_nr']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             $jsonArr['errors'][]='konto_nr';
         }

         if($_REQUEST['formdata']['konto_blz']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
             $jsonArr['errors'][]='konto_blz';
         }

         if($_REQUEST['formdata']['konto_institut']=='')
         {
             $jsonArr['navsteps'][$currentStep]=false;
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

    function ajax_formDvPv_pdf()
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
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(0,10, "firma: ".$data->firma);
        $pdf->Output();
        //
        // $pdf->Output('../','F');

        die();
    }

    add_action( 'wp_ajax_formdvpv', 'ajax_formDvPv' );
    add_action( 'wp_ajax_formdvpv_pdf', 'ajax_formDvPv_pdf' );



?>
