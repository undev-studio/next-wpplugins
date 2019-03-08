<?php

require_once('libs/phpmailer/PHPMailerAutoload.php');
require_once('language.php');

error_reporting(E_ERROR | E_WARNING);
ini_set('display_errors', '1');

add_action('wp_ajax_nopriv_widgetforms', 'ajax_widgetforms');
add_action('wp_ajax_widgetforms', 'ajax_widgetforms');

function nltobr($str)
{
  return str_replace("\n", "<br/>", $str);
}


function getPLZEmail($plzValue)
{
  if (!is_numeric($plzValue)) return '';
  global $wpdb;

  // get email adress of consultant
  $q = 'SELECT * FROM erloes_plz WHERE start <= ' . $plzValue . ' AND end > ' . $plzValue;
  $res = $wpdb->get_results($q);

  if ($res[0]->email == '') {
    $obj = array();
    $headers =
      'MIME-Version: 1.0' . "\r\n" .
      'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
      $next_email_from . "\r\n" .
      'Reply-To: wordpress@next-kraftwerke.de' . "\r\n" .
      'X-Mailer: PHP/' . phpversion();

    wp_mail('tom-next@undev.de', 'erleosberechner / keine plz zuordnung', Util::umlaute('keine zuordnung gefunden fuer plz:' . $plzValue), '', '');

    $response['errorfail'] = "keine zuordnung gefunden!";
    return;
  }

  return $res[0]->email;
}

function ajax_widgetforms()
{
  $jsonArr = Array();
  $jsonArr['errors'] = Array();
  $jsonArr['navsteps'] = Array();
  // var_dump($_REQUEST["formdata"]);

  $form = $_REQUEST["formdata"];

  if ($form['name'] == '') $jsonArr['errors'][] = 'name';
  if ($form['phone'] == '') $jsonArr['errors'][] = 'phone';
  if ($form['email'] == '') $jsonArr['errors'][] = 'email';
  if ($form['zip'] == '') $jsonArr['errors'][] = 'zip';
  if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) $jsonArr['errors'][] = 'email';

  if ($form['form'] == 'Flexheft') {
    if ($form['street'] == '') $jsonArr['errors'][] = 'street';
    if ($form['city'] == '') $jsonArr['errors'][] = 'city';
  }

  if ($form['form'] == 'Biogas' ||
    $form['form'] == 'Solar' ||
    $form['form'] == 'Wind' ||
    $form['form'] == 'Wasserkraft' ||
    $form['form'] == 'BHKW_KWK' ||
    $form['form'] == 'Notstrom' ||
    $form['form'] == 'Verbraucher' ||
    $form['form'] == 'Stromhandelsdienstleistungen' ||
    $form['form'] == 'Kraftwerke'
  ) {
    if ($form['leistung'] == '') $jsonArr['errors'][] = 'leistung';
  }

  if (count($jsonArr['errors']) == 0) {
    if ($form['form'] == 'Flexheft') {
      $email = new PHPMailer();
      $email->CharSet = 'utf-8';
      $email->From = 'beratung@next-kraftwerke.de';
      $email->FromName = 'Next Kraftwerke';
      $email->Subject = 'Flex-Heft Bestellung';
      $email->Body = 'Vielen Dank, dass Sie unser Flex-Heft bestellt haben! <br/><br/>Es sollte spätestens in zwei Wochen bei Ihnen ankommen. <br/>Sollte sich etwas verzögern, melden Sie sich bitte telefonisch unter 0221 820085 0.';

      $email->AddAddress($form['email']);
      $email->addBCC('tom-next@undev.de');
      $email->addBCC('beratung@next-kraftwerke.de');
      $email->addBCC('presse@next-kraftwerke.de');
      $email->isHTML(true);

      $email->Send();

      global $wpdb;
      $wpdb->insert('emaillog', array(
          'content' => json_encode($email) . json_encode($form),
          'templatename' => 'widget_' . $form['form'],
          'to' => $form['email']
        )
      );
    } else {
      $email = new PHPMailer();
      $email->CharSet = 'utf-8';
      $email->From = nextTranslate('widget_form_minierloes_mail_from_email');;
      $email->FromName = nextTranslate('widget_form_minierloes_mail_from_name');
      $email->Subject = nextTranslate('widget_form_minierloes_mail_subject');
      $email->Body = nltobr(html_entity_decode(nextTranslate('widget_form_minierloes_mail_body')));

      $email->AddAddress($form['email']);
      // $email->addBCC('tom-next@undev.de');
      // $email->addBCC('beratung@next-kraftwerke.de');
      // $email->addBCC('presse@next-kraftwerke.de');
      $email->isHTML(true);

      $email->Send();

      global $wpdb;
      $wpdb->insert('emaillog', array(
          'content' => json_encode($email) . json_encode($form),
          'templatename' => 'widget_' . $form['form'],
          'to' => $form['email']
        )
      );

    }

    $email = new PHPMailer();
    $email->CharSet = 'utf-8';
    $email->From = 'beratung@next-kraftwerke.de';
    $email->FromName = 'Next Kraftwerke';
    $email->Subject = 'Widget Formular ' . $form['form'];

    $email->Body = 'Das Formular "' . $form['form'] . '" wurde ausgefüllt:<br/><br/>';
    $email->Body .= '<b>Name:</b> ' . $form['name'] . ' <br/>';
    $email->Body .= '<b>EMail:</b> ' . $form['email'] . ' <br/>';

    if ($form['form'] == 'Flexheft') {
      $email->Body .= '<b>Firma:</b> ' . $form['company'] . ' <br/>';
      $email->Body .= '<b>Strasse:</b> ' . $form['street'] . ' <br/>';
    }


    $email->Body .= '<b>PLZ/Ort:</b> ' . $form['zip'] . ' ' . $form['city'] . ' <br/>';
    $email->Body .= '<b>Telefonnummer:</b> ' . $form['phone'] . ' <br/>';
    $email->Body .= '<b>Nachricht:</b> ' . $form['msg'] . ' <br/>';

    $email->Body .= '<br/>';

    if ($form['form'] == 'Biogas' ||
      $form['form'] == 'Solar' ||
      $form['form'] == 'Wind' ||
      $form['form'] == 'Wasserkraft' ||
      $form['form'] == 'BHKW_KWK' ||
      $form['form'] == 'Notstrom' ||
      $form['form'] == 'Kraftwerke'
    ) {
      $email->Body .= '<b>Leistung:</b> ' . $form['leistung'] . ' kW' . '<br/>';
    }

    if ($form['form'] == 'Verbraucher') {
      $email->Body .= '<b>Stromverbrauch pro Jahr:</b> ' . $form['leistung'] . ' kWh' . '<br/>';
    }

    if ($form['form'] == 'Stromhandelsdienstleistungen') {
      $email->Body .= '<b>Entnommene Jahresarbeit:</b> ' . $form['leistung'] . ' kW' . '<br/>';
      $email->Body .= '<b>Anzahl Entnahmestellen:</b> ' . $form['entnahmestellen'] . '<br/>';
    }

    $email->Body .= '<br/>';
    $email->Body .= '<b>URL:</b> ' . $form['form_url'] . '<br/>';


    if ($form['form'] != 'Flexheft') {
      $consultEmail = getPLZEmail($form['zip']);
      if ($consultEmail != "") $email->AddAddress($consultEmail);
      // $email->Body     .= '<b>consulter:</b> '.$consultEmail.'<br/>';
    }

    $email->AddAddress('beratung@next-kraftwerke.de');
    $email->addCC('tom-next@undev.de');
    $email->addCC('presse@next-kraftwerke.de');
    $email->isHTML(true);
    $email->Send();

    $jsonArr['mailsent'] = true;

    global $wpdb;
    $lastid = $wpdb->insert('emaillog', array(
        'content' => json_encode($email) . json_encode($form),
        'templatename' => 'widget_' . $form['form'],
        'to' => $form['beratung@next-kraftwerke.de']
      )
    );


  }

  echo json_encode($jsonArr);

  die();
}


?>
