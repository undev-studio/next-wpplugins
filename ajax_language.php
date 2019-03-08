<?php

error_reporting(E_ERROR | E_WARNING);
ini_set('display_errors', '1');

add_action('wp_ajax_nopriv_getLanguageStructure', 'getLanguageStructure');
add_action('wp_ajax_getLanguageStructure', 'getLanguageStructure');

add_action('wp_ajax_nopriv_getLanguage', 'getLanguage');
add_action('wp_ajax_getLanguage', 'getLanguage');

add_action('wp_ajax_setLangTrans', 'setTranslation');

function getLanguageStructure()
{
  $filename = realpath(dirname(__FILE__)) . "/language.json";
  $json = file_get_contents($filename);
  echo $json;
  die();
}

function getLanguageText()
{
  $fn = 'lang_' . basename($_REQUEST['l']) . '.json';
  $filename = realpath(dirname(__FILE__)) . '/' . $fn;
  if (file_exists($filename)) {
    $json = file_get_contents($filename);
    return $json;
  }
  return null;
}

function getLanguage()
{
  echo getLanguageText();
  die();
}


function updateSimulationLanguage()
{

}

function setTranslation()
{
  // ?l=de&key=KEY&trans=328923

  $fn = 'lang_' . basename($_REQUEST['l']) . '.json';
  $filename = realpath(dirname(__FILE__)) . '/' . $fn;

  $str = getLanguageText();
  $lang = json_decode($str, true);

  $lang[$_REQUEST['key']] = urldecode($_REQUEST['trans']);

  $jsonStr = json_encode($lang);

  if ($jsonStr != null) {
    file_put_contents($filename, $jsonStr);
  }

  echo $jsonStr;

  die();


}

?>
