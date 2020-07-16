<?php

require_once('libs/phpmailer/PHPMailerAutoload.php');

require_once('libs/hashids/HashGenerator.php');
require_once('libs/hashids/Hashids.php');

add_action('wp_ajax_nopriv_formangebot', 'ajax_formangebot');
add_action('wp_ajax_formangebot', 'ajax_formangebot');

add_role("angebot_contact",
  "Kontakt: SolarSpot  750 kWp",
  array(
    "read" => true,  // true allows this capability
    "edit_posts" => true,
  )
);

function ajax_formangebot()
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

  if ($_REQUEST['formdata']['strasse'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'strasse';
  }

  if ($_REQUEST['formdata']['strassenr'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'strassenr';
  }

  if ($_REQUEST['formdata']['plz'] == '' || !is_numeric($_REQUEST['formdata']['plz']) || strlen($_REQUEST['formdata']['plz']) != 5) {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'plz';
  }

  if ($_REQUEST['formdata']['ort'] == '') {
    $jsonArr['navsteps'][$currentStep] = false;
    $jsonArr['errors'][] = 'ort';
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
  } else {
    if ($_REQUEST['formdata']['email'] != $_REQUEST['formdata']['email2']) {
      $jsonArr['navsteps'][$currentStep] = false;
      $jsonArr['errors'][] = 'email';
      $jsonArr['errors'][] = 'email2';
      $jsonArr['errormsg'][] = 'email_notsame';
    } else {
      if (!filter_var($_REQUEST['formdata']['email'], FILTER_VALIDATE_EMAIL)) {
        $jsonArr['navsteps'][$currentStep] = false;
        $jsonArr['errors'][] = 'email';
        $jsonArr['errors'][] = 'email2';
        $jsonArr['errormsg'][] = 'email_invalid';
      }
    }
  }

  if ($fromStep > 0) {
    //print(json_encode($_REQUEST)); die();
    // validate anlagen
    $currentStep = $fromStep;
    while ($currentStep > 0) {
      $jsonArr['navsteps'][$currentStep] = true;
      $energieTraeger = "energietraeger" . $fromStep;
      $nennleistung = "nennleistung" . $fromStep;
      $datum = "datum" . $fromStep;
      $plz = "anlage_plz" . $fromStep;
      $ort = "anlage_ort" . $fromStep;
      $verbrauch = "eigenverbrauch" . $fromStep;
      $verbrauch_number = "eigenverbrauch_input" . $fromStep;

      if ($_REQUEST['formdata'][$energieTraeger] == '') {
        $jsonArr['navsteps'][$currentStep] = false;
        if ($fromStep >= $currentStep) $jsonArr['errors'][] = $energieTraeger;
      }

      if ($_REQUEST['formdata'][$nennleistung] == '') {
        $jsonArr['navsteps'][$currentStep] = false;
        if ($fromStep >= $currentStep) $jsonArr['errors'][] = $nennleistung;
      }

      if ($_REQUEST['formdata'][$datum] == '') {
        $jsonArr['navsteps'][$currentStep] = false;
        if ($fromStep >= $currentStep) $jsonArr['errors'][] = $datum;
      }

      if ($_REQUEST['formdata'][$plz] == '') {
        $jsonArr['navsteps'][$currentStep] = false;
        if ($fromStep >= $currentStep) $jsonArr['errors'][] = $plz;
      }

      if ($_REQUEST['formdata'][$ort] == '') {
        $jsonArr['navsteps'][$currentStep] = false;
        if ($fromStep >= $currentStep) $jsonArr['errors'][] = $ort;
      }

      if ($_REQUEST['formdata'][$verbrauch] != '' && empty($_REQUEST['formdata'][$verbrauch_number])) {
        $jsonArr['navsteps'][$currentStep] = false;
        if ($fromStep >= $currentStep) $jsonArr['errors'][] = $verbrauch_number;
      }
      $currentStep--;
    }
  }

  $valid = true;
  $checkStep = $fromStep;
  while ($checkStep > -1) {
    if (!$jsonArr['navsteps'][$checkStep]) {
      $valid = false;
      break;
    }
    $checkStep--;
  }

  $return = json_encode($jsonArr);
  if ($valid && $fromStep > 0) {
    send_angebot_email($fromStep);
  }

  echo $return;
  die();
}

function send_angebot_email($lastStep)
{

  global $wpdb;

  $nextBody = '';
  $nextBody .= 'Abgesendet am: ' . strftime('%d.%m.%Y %H:%M:%S');
  $nextBody .= '<br/><br/>';
  $nextBody .= 'Vorname: ' . $_REQUEST['formdata']['vorname'];
  $nextBody .= '<br/><br/>';
  $nextBody .= 'Name: ' . $_REQUEST['formdata']['nachname'];
  $nextBody .= '<br/><br/>';
  $nextBody .= 'Firma: ' . $_REQUEST['formdata']['firma'];
  $nextBody .= '<br/><br/>';
  $nextBody .= 'Straße: ' . $_REQUEST['formdata']['strasse'] . " " . $_REQUEST['formdata']['strassenr'];
  $nextBody .= '<br/><br/>';
  if ($_REQUEST['formdata']['zusatz']) {
    $nextBody .= 'Zusatz: ' . $_REQUEST['formdata']['zusatz'];
    $nextBody .= '<br/><br/>';
  }
  $nextBody .= 'Ort: ' . $_REQUEST['formdata']['plz'] . $_REQUEST['formdata']['ort'];
  $nextBody .= '<br/><br/>';
  $nextBody .= 'Land: ' . $_REQUEST['formdata']['land'];
  $nextBody .= '<br/><br/>';
  $nextBody .= 'Telefon: ' . $_REQUEST['formdata']['phone'];
  $nextBody .= '<br/><br/>';
  $nextBody .= 'E-Mail: ' . $_REQUEST['formdata']['email'];
  $nextBody .= '<br/><br/>';
  $nextBody .= 'Anlagen:';
  $nextBody .= '<br/><br/>';
  $currentStep = 1;
  while ($currentStep <= $lastStep) {
    $nextBody .= 'Anlage ' . $currentStep . ':';
    $nextBody .= '<br/><br/>';

    $energieTraeger = "energietraeger" . $currentStep;
    $nennleistung = "nennleistung" . $currentStep;
    $datum = "datum" . $currentStep;
    $plz = "anlage_plz" . $currentStep;
    $ort = "anlage_ort" . $currentStep;
    $verbrauch = "eigenverbrauch" . $currentStep;
    $verbrauch_number = "eigenverbrauch_input" . $currentStep;
    // $inbetriebname = "inbetriebname" . $currentStep;
    // $beginvermarktunginput = "beginvermarktunginput" . $currentStep;
    $betriebdatum = "betriebdatum" . $currentStep;
    $sonstiges = "sonstiges" . $currentStep;

    $nextBody .= 'Energieträger:' . $_REQUEST['formdata'][$energieTraeger];
    $nextBody .= '<br/><br/>';
    $nextBody .= 'Nennleisung:' . $_REQUEST['formdata'][$nennleistung];
    $nextBody .= '<br/><br/>';
    $nextBody .= 'Datum Inbetriebnahme:' . $_REQUEST['formdata'][$datum];
    $nextBody .= '<br/><br/>';
    $nextBody .= 'PLZ:' . $_REQUEST['formdata'][$plz];
    $nextBody .= '<br/><br/>';
    $nextBody .= 'Ort:' . $_REQUEST['formdata'][$ort];
    $nextBody .= '<br/><br/>';
    $nextBody .= 'Eigenverbrauch:' . $_REQUEST['formdata'][$verbrauch];
    $nextBody .= '<br/><br/>';
    $nextBody .= 'Eigenverbrauch, Prozent' . $_REQUEST['formdata'][$verbrauch_number];
    $nextBody .= '<br/><br/>';
    $nextBody .= 'Datum Anmeldung:' . $_REQUEST['formdata'][$betriebdatum];
    $nextBody .= '<br/><br/>';
    $nextBody .= 'Sonstiges:' . $_REQUEST['formdata'][$sonstiges];
    $nextBody .= '<br/><br/>';
    $currentStep++;
  }
  $nextBody .= '<br/><br/>';
  $nextBody .= 'Viele Grüße,';
  $nextBody .= 'eure U-komm';

  $nextEmail = new PHPMailer();
  $nextEmail->CharSet = 'utf-8';
  $nextEmail->From = 'ew@next-kraftwerke.de';
  $nextEmail->FromName = 'Next Kraftwerke';
  $nextEmail->Subject = 'Neues Formular: Angebotsanfrage PV-Direktvermarktung';
  $nextEmail->Body = $nextBody;

  $nextEmail->AddAddress('info@next-kraftwerke.de');

  // try to get responsible recipient
  $contacts = get_users(array(
    'role' => 'idea_contact'
  ));
  $toAdresses = [];
  foreach ($contacts as $contact) {
    $toAdresses[] = $contact->data->user_email;
  }
  if (empty($toAdresses)) {
    $toAdresses[] = 'beratung@next-kraftwerke.de';
  }
  foreach ($toAdresses as $toAdress) {
    $nextEmail->AddAddress($toAdress);
  }

  $nextEmail->isHTML(true);
  $nextEmail->Send();

  $lastid = $wpdb->insert('emaillog', array(
      'content' => json_encode($nextEmail),
      'templatename' => 'notification_form_angebot',
      'to' => implode(',', $toAdresses)
    )
  );
}

?>
