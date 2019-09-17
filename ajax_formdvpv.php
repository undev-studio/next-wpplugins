<?php

require_once('libs/fpdf/fpdf.php');
require_once('libs/phpmailer/PHPMailerAutoload.php');
require_once('libs/dompdf/dompdf_config.inc.php');
require_once('libs/iban/iban.php');

require_once('libs/hashids/HashGenerator.php');
require_once('libs/hashids/Hashids.php');

add_action('wp_ajax_nopriv_formdvpv', 'ajax_formdvpv');
add_action('wp_ajax_nopriv_formdvpv_pdf', 'ajax_formdvpv_pdf');
add_action('wp_ajax_nopriv_formdvpv_zip', 'ajax_formdvpv_zip');
add_action('wp_ajax_formdvpv', 'ajax_formdvpv');
add_action('wp_ajax_formdvpv_pdf', 'ajax_formdvpv_pdf');
add_action('wp_ajax_formdvpv_zip', 'ajax_formdvpv_zip');

//--------------------------------------

function generate_uuid()
{
  return sprintf('DVPV%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    mt_rand(0, 0xffff), mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0x0fff) | 0x4000,
    mt_rand(0, 0x3fff) | 0x8000,
    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
  );
}

function ajax_formdvpv()
{
  $jsonArr = Array();
  $jsonArr['errors'] = Array();
  $jsonArr['navsteps'] = Array();
  $jsonArr['errormsg'] = Array();

  $fromStep = $_REQUEST['step'];

  // step 1

  $currentStep = 0;
  $jsonArr['navsteps'][$currentStep] = true;

  if ($_REQUEST['formdata']['firma'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'firma';
  }

  if ($_REQUEST['formdata']['phone'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'phone';
  }

  if ($_REQUEST['formdata']['strasse'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'strasse';
  }

  if ($_REQUEST['formdata']['strassenr'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'strassenr';
  }

  if ($_REQUEST['formdata']['ort'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'ort';
  }

  if ($_REQUEST['formdata']['land'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'land';
  }

  if ($_REQUEST['formdata']['plz'] == '' || !is_numeric($_REQUEST['formdata']['plz']) || strlen($_REQUEST['formdata']['plz']) != 5) {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'plz';
  }

  if ($_REQUEST['formdata']['vorname'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'vorname';
  }

  if ($_REQUEST['formdata']['nachname'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'nachname';
  }

  if ($_REQUEST['formdata']['email'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'email';
  }

  if ($_REQUEST['formdata']['email2'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'email2';
  }

  if ($_REQUEST['formdata']['email'] == '' && $_REQUEST['formdata']['email2'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'email';
    $jsonArr['errors'][] = 'email2';
  } else
    if ($_REQUEST['formdata']['email'] != $_REQUEST['formdata']['email2']) {
      $jsonArr['navsteps'][$currentStep] = false;
      $jsonArr['errors'][] = 'email';
      $jsonArr['errors'][] = 'email2';
      $jsonArr['errormsg'][] = 'email_notsame';
    } else
      if (!filter_var($_REQUEST['formdata']['email'], FILTER_VALIDATE_EMAIL)) {
        $jsonArr['navsteps'][$currentStep] = false;
        $jsonArr['errors'][] = 'email';
        $jsonArr['errors'][] = 'email2';
        $jsonArr['errormsg'][] = 'email_invalid';
      }

  if ($_REQUEST['formdata']['ustid'] == '' && $_REQUEST['formdata']['steuernr'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'ustid';
    $jsonArr['errors'][] = 'steuernr';
    $jsonArr['errormsg'][] = 'steuer';
  }

  if ($_REQUEST['formdata']['ustid'] != '') {
    if (substr(strtolower($_REQUEST['formdata']['ustid']), 0, 2) != 'de' || strlen($_REQUEST['formdata']['ustid']) != 11) {
      $jsonArr['navsteps'][$currentStep] = false;
      $jsonArr['errors'][] = 'ustid';
    }
  }


  /////////////////////////////////////////////////////////////////
  // step 2
  $currentStep = 1;
  $jsonArr['navsteps'][$currentStep] = true;

  if ($_REQUEST['formdata']['nennleistung'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'nennleistung';
  }

  if ($_REQUEST['formdata']['hasNoZaehlbezeichnung'] == 'true') {
    if ($_REQUEST['formdata']['registrnr'] == '') {
      $jsonArr['navsteps'][$currentStep] = false;
      if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'registrnr';
    }
  } else {
    if ($_REQUEST['formdata']['marktlokation'] == '' || strlen($_REQUEST['formdata']['marktlokation']) != 11) {
      $jsonArr['navsteps'][$currentStep] = false;
      if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'marktlokation';
    }
  }

  if ($_REQUEST['formdata']['anlage_strasse'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'anlage_strasse';
  }
  if ($_REQUEST['formdata']['anlage_strassenr'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'anlage_strassenr';
  }
  if ($_REQUEST['formdata']['anlage_plz'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'anlage_plz';
  }

  if ($_REQUEST['formdata']['anlage_ort'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'anlage_ort';
  }

  if ($_REQUEST['formdata']['netzbetreiber'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'netzbetreiber';
  }

  if ($_REQUEST['formdata']['anlage_fernsteuerung'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'anlage_fernsteuerung';
  }


  if ($_REQUEST['formdata']['anlage_modul_anzahl'] != '' && !is_numeric($_REQUEST['formdata']['anlage_modul_anzahl'])) {
    $jsonArr['navsteps'][$currentStep] = false;
    if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'anlage_modul_anzahl';
  }


  // step 3
  $currentStep = 2;
  $jsonArr['navsteps'][$currentStep] = true;

  if($fromStep == 2) {
    if (substr($_REQUEST['formdata']['beginvermarktung'], 2, 1) != '.') {
      $jsonArr['navsteps'][$currentStep] = false;
      // $jsonArr['errors'][]='beginvermarktunginput';
      // $jsonArr['errors'][]='datuminbetriebnahme';
      $jsonArr['errormsg'][] = 'beginvermarktung';
    }
  }

  // step 4
  $currentStep = 3;
  $jsonArr['navsteps'][$currentStep] = true;

  if($fromStep == 3) {

    if ($_REQUEST['formdata']['voucher'] != '') {

      global $wpdb;
      $sql = 'SELECT * FROM next_formdvpv_codes WHERE BINARY code = "' . esc_sql($_REQUEST['formdata']['voucher']) . '";';
      $rows = $wpdb->get_results($sql);

      if ($wpdb->last_error != '') {
        echo($wpdb->last_error);
      }

      $sql = 'SELECT * FROM next_formdvpv WHERE BINARY voucher = "' . esc_sql($_REQUEST['formdata']['voucher']) . '";';
      $rowsExisting = $wpdb->get_results($sql);

      if ($wpdb->last_error != '') {
        echo($wpdb->last_error);
      }


      if (count($rows) == 0 || count($rowsExisting) > 0) {
        $jsonArr['navsteps'][$currentStep] = false;
        if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'voucher';
        $jsonArr['errormsg'][] = 'voucher';
      }
    }

    if (!iban_verify_checksum($_REQUEST['formdata']['konto_iban'])) {
      $jsonArr['navsteps'][$currentStep] = false;
      if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'konto_iban';
      $jsonArr['errormsg'][] = 'iban';
    }

    $iso = strtoupper(substr($_REQUEST['formdata']['konto_iban'], 0, 2));
    $isoArr = array("BE", "BG", "DK", "DE", "EE", "FI", "FR", "GR", "GB", "IE", "IS", "IT", "HR", "LV", "LI", "LT", "LU", "MT", "MC", "NL", "NO", "AT", "PL", "PT", "RO", "SM", "SE", "CH", "SK", "SI", "ES", "CZ", "HU", "CY");

    if (!in_array($iso, $isoArr)) {
      $jsonArr['navsteps'][$currentStep] = false;
      if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'konto_iban';
      $jsonArr['errormsg'][] = 'iban';
    }


    if ($_REQUEST['formdata']['konto_bic'] == '') {
      $jsonArr['navsteps'][$currentStep] = false;
      if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'konto_bic';
    }

    if ($_REQUEST['formdata']['konto_inhaber'] == '') {
      $jsonArr['navsteps'][$currentStep] = false;
      if ($fromStep >= $currentStep) $jsonArr['errors'][] = 'konto_inhaber';
    }

  }

  if ($fromStep == 3 && $jsonArr['navsteps'][0] == true && $jsonArr['navsteps'][1] == true && $jsonArr['navsteps'][2] == true && $jsonArr['navsteps'][3] == true) {
    global $wpdb;

    $row = Array();
    $row['id'] = generate_uuid();
    $row['json'] = json_encode($_REQUEST['formdata']);

    $row['firma'] = $_REQUEST['formdata']['firma'];
    $row['phone'] = $_REQUEST['formdata']['phone'];
    $row['strasse'] = $_REQUEST['formdata']['strasse'];
    $row['strassenr'] = $_REQUEST['formdata']['strassenr'];
    $row['plz'] = $_REQUEST['formdata']['plz'];
    $row['ort'] = $_REQUEST['formdata']['ort'];
    $row['land'] = $_REQUEST['formdata']['land'];
    $row['vorname'] = $_REQUEST['formdata']['vorname'];
    $row['nachname'] = $_REQUEST['formdata']['nachname'];
    $row['email'] = $_REQUEST['formdata']['email'];
    $row['nennleistung'] = $_REQUEST['formdata']['nennleistung'];
    $row['zaehlbezeichn'] = $_REQUEST['formdata']['zaehlbezeichn'];
    $row['marktlokation'] = $_REQUEST['formdata']['marktlokation'];

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
    $row['voucher'] = $_REQUEST['formdata']['voucher'];

    $row['anlage_modul_anzahl'] = $_REQUEST['formdata']['anlage_modul_anzahl'];
    $row['anlage_modul_hersteller'] = $_REQUEST['formdata']['anlage_modul_hersteller'];
    $row['anlage_modul_typ'] = $_REQUEST['formdata']['anlage_modul_typ'];

    $row['anlage_pos_ausrichtungswinkel'] = $_REQUEST['formdata']['anlage_pos_ausrichtungswinkel'];
    $row['anlage_pos_neigungswinkel'] = $_REQUEST['formdata']['anlage_pos_neigungswinkel'];


    // $row['konto_nr'] = $_REQUEST['formdata']['konto_nr'];
    // $row['konto_blz'] = $_REQUEST['formdata']['konto_blz'];
    // $row['konto_institut'] = $_REQUEST['formdata']['konto_institut'];
    $row['ustid'] = $_REQUEST['formdata']['ustid'];
    $row['steuernr'] = $_REQUEST['formdata']['steuernr'];

    $row['vermarktung'] = $_REQUEST['formdata']['vermarktung'];
    $row['beginvermarktung'] = $_REQUEST['formdata']['beginvermarktung'];
    $row['anlage_fernsteuerung'] = $_REQUEST['formdata']['anlage_fernsteuerung'];
    $row['ummeldung'] = $_REQUEST['formdata']['ummeldung_radio'];


    $wpdb->insert('next_formdvpv', $row);
    $pkey = $wpdb->insert_id;


    $hashids = new Hashids\Hashids('superSekretNextSalt!!11', 6, 'ABCDEFGHIKMPRSTUWXYZ123456789');
    $docId = $hashids->encode($pkey);
    $jsonArr['docId'] = $docId;

    $wpdb->update('next_formdvpv', array('docid' => $docId), array('pkey' => $pkey));

    if ($wpdb->last_error != '') {
      $jsonArr['dberror'] = $wpdb->last_error;
    } else {
      $jsonArr['id'] = $row['id'];

      $body = '';
      $body .= 'Sehr geehrte(r) ' . $_REQUEST['formdata']['vorname'] . ' ' . $_REQUEST['formdata']['nachname'] . ',';
      $body .= '<br/><br/>';
      $body .= 'vielen Dank f&uuml;r Ihr Interesse an der Direktvermarktung Ihrer PV-Anlage &uuml;ber Next Kraftwerke.';
      $body .= '<br/><br/>';
      $body .= 'Anbei finden Sie das von uns auf Basis Ihrer Eingaben erzeugte Dokument. Bitte drucken Sie sowohl den <b>Auftrag</b> als auch die <b>Vollmacht</b> aus, <b>unterschreiben beide</b> Dokumente und senden uns diese <b>per Post</b> zu.';
      $body .= '<br/><br/>';
      $body .= 'Nach Pr&uuml;fung Ihrer Unterlagen erhalten Sie von uns eine Auftragsbest&auml;tigung mit dem Hinweis auf den Zeitpunkt, ab dem wir die PV-Anlage f&uuml;r Sie voraussichtlich vermarkten k&ouml;nnen. Erst durch die Auftragsbest&auml;tigung ist der Vertrag zur Direktvermarktung Ihrer PV-Anlage mit uns abgeschlossen.';
      $body .= '<br/><br/>';
      $body .= 'Bitte beachten Sie die <a href="https://www.next-kraftwerke.de/wp-content/uploads/Umsetzung-verpflichtende-Fernsteuerbarkeit.pdf">Hinweise zur Fernsteuerbarkeit</a> sowie die <a href="https://www.next-kraftwerke.de/wp-content/uploads/Vermarktungsbedingungen-Direktvermarktung-PV-Next-Kraftwerke.pdf">Allgemeinen Vermarktungsbedingungen f&uuml;r kleine Photovoltaik-Anlagen</a>.<br/>';
      $body .= 'F&uuml;r PV-Anlagen mit mehr als 800 kW Nennleistung ben&ouml;tigen wir leider ein paar zus&auml;tzliche Informationen: Ein <a href="https://www.next-kraftwerke.de/meta/erloesrechner">individuelles Angebot k&ouml;nnen Sie hier anfragen.</a>';
      $body .= '<br/><br/>';
      $body .= 'Mit freundlichen Gr&uuml;&szlig;en,<br/>';
      $body .= 'Ihr Next Kraftwerke Team<br/><br/>';

      $dompdf = genPDF($row['id']);
      $filename = getcwd() . '/../../formdvpv/Direktvermarktung_PV-Anlage_' . $docId . '.pdf';
      $output = $dompdf->output();
      file_put_contents($filename, $output);

      $email = new PHPMailer();
      $email->CharSet = 'utf-8';
      $email->From = 'ew@next-kraftwerke.de';
      $email->FromName = 'Next Kraftwerke';
      $email->Subject = 'Direktvermarktung Ihrer PV-Anlage > Dokumente zur Unterschrift';
      $email->Body = $body;

      $email->AddAddress($_REQUEST['formdata']['email']);
      $email->AddAttachment($filename, "Direktvermarktung_" . $docId . ".pdf");
      $email->AddAttachment('/var/www/website/sites/de/htdocs/wp-content/uploads/Vermarktungsbedingungen-Direktvermarktung-PV-Next-Kraftwerke.pdf', "Allgemeine_Vermarktungsbedingungen.pdf");
      $email->addBCC('ew@next-kraftwerke.de');
      $email->isHTML(true);

      global $wpdb;
      $lastid = $wpdb->insert('emaillog', array(
          'content' => json_encode($email),
          'templatename' => 'form_dvpv',
          'to' => $_REQUEST['formdata']['email']
        )
      );

      $body .= '---\n';
      $body .= file_get_contents(getcwd() . 'signature_solarspot.html');
      $email->Body = $body;

      $email->Send();

      $nextBody = '';
      $nextBody .= 'Abgesendet am: ' . strftime('%d.%m.%Y %H:%M:%S');
      $nextBody .= '<br/><br/>';
      $nextBody .= 'Vorname: ' . $_REQUEST['formdata']['vorname'];
      $nextBody .= '<br/><br/>';
      $nextBody .= 'Name: ' . $_REQUEST['formdata']['nachname'];
      $nextBody .= '<br/><br/>';
      $nextBody .= 'Firma: ' . $_REQUEST['formdata']['firma'];
      $nextBody .= '<br/><br/>';
      $nextBody .= 'Telefon: ' . $_REQUEST['formdata']['phone'];
      $nextBody .= '<br/><br/>';
      $nextBody .= 'PLZ: ' . $_REQUEST['formdata']['plz'];
      $nextBody .= '<br/><br/>';
      $nextBody .= 'E-Mail: ' . $_REQUEST['formdata']['email'];
      $nextBody .= '<br/><br/>';
      $nextBody .= 'Marktlokations-ID:' . $_REQUEST['formdata']['marktlokation'];
      $nextBody .= '<br/><br/>';
      $nextBody .= '<br/><br/>';
      $nextBody .= 'Viele Grüße,';
      $nextBody .= 'eure U-komm';

      $nextEmail = new PHPMailer();
      $nextEmail->CharSet = 'utf-8';
      $nextEmail->From = 'ew@next-kraftwerke.de';
      $nextEmail->FromName = 'Next Kraftwerke';
      $nextEmail->Subject = 'Neuer Online-Vertrag SolarSpot ausgefüllt';
      $nextEmail->Body = $nextBody;

      $nextEmail->AddAddress('info@next-kraftwerke.de');

      // try to get responsible recipient
      $recipients = $wpdb->get_results('SELECT * FROM `erloes_plz` WHERE "' . esc_sql(trim($_REQUEST['formdata']['plz'])) . '"  BETWEEN start AND end;', ARRAY_A);
      if ($recipients) {
        foreach ($recipients as $recipient) {
          $sentTo = trim($recipient['email']);
          break;
        }
      } else {
        $sentTo = 'beratung@next-kraftwerke.de';
      }

      $nextEmail->AddAddress($sentTo);
      $nextEmail->isHTML(true);

      $nextEmail->Send();

      $lastid = $wpdb->insert('emaillog', array(
          'content' => json_encode($nextEmail),
          'templatename' => 'notification_form_dvpv',
          'to' => $sentTo
        )
      );

    }
  }

  echo json_encode($jsonArr);

  die();
}


function genPDF($id)
{
  global $wpdb;
  $sql = 'SELECT * FROM next_formdvpv WHERE id="' . esc_sql($id) . '";';
  $rows = $wpdb->get_results($sql);

  if ($wpdb->last_error != '') {
    echo($wpdb->last_error);
  }

  $dompdf = new DOMPDF();
  $dompdf->set_paper("A4");
  $day = date("j", strtotime($rows[0]->date));
  $year = date("Y", strtotime($rows[0]->date));
  $month = date("m", strtotime($rows[0]->date));
  if ($month == 1) $month = "Januar";
  if ($month == 2) $month = "Februar";
  if ($month == 3) $month = "März";
  if ($month == 4) $month = "April";
  if ($month == 5) $month = "Mai";
  if ($month == 6) $month = "Juni";
  if ($month == 7) $month = "Juli";
  if ($month == 8) $month = "August";
  if ($month == 9) $month = "September";
  if ($month == 10) $month = "Oktober";
  if ($month == 11) $month = "November";
  if ($month == 12) $month = "Dezember";

  if (empty($rows[0])) $rows[0] = new stdClass();
  $rows[0]->readableDate = $day . '. ' . $month . ' ' . $year;

  if (substr($rows[0]->beginvermarktung, 2, 1) == '.') $rows[0]->beginvermarktung = '' . $rows[0]->beginvermarktung;

  $twig = initTwig();
  $template = $twig->loadTemplate('form_dvpv_pdf_de.html');
  $html = $template->render(array('data' => $rows[0]));

  $dompdf->load_html($html);

  $dompdf->render();

  return $dompdf;
}

function ajax_formdvpv_pdf()
{
  $dompdf = genPDF($_REQUEST['id']);

  global $wpdb;
  $rows = $wpdb->get_results('SELECT * FROM next_formdvpv WHERE id="' . esc_sql($_REQUEST['id']) . '";');

  $dompdf->stream("Direktvermarktung_PV-Anlage_" . $rows[0]->docid . ".pdf", ['Attachment' => 0]);

  die();
}

function ajax_formdvpv_zip()
{
  global $wpdb;
  $rows = $wpdb->get_results('SELECT * FROM next_zip WHERE zip="' . esc_sql($_REQUEST['zip']) . '";');

  echo json_encode($rows[0]);
  die();
}


?>
