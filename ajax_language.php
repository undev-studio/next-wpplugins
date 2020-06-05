<?php

error_reporting(E_ERROR | E_WARNING);
ini_set('display_errors', '1');

add_action('wp_ajax_nopriv_getLanguageStructure', 'getLanguageStructure');
add_action('wp_ajax_getLanguageStructure', 'getLanguageStructure');

add_action('wp_ajax_nopriv_getLanguage', 'getLanguage');
add_action('wp_ajax_getLanguage', 'getLanguage');

add_action('wp_ajax_setLangTrans', 'setTranslation');


function getLanguageJson() {
  $filename = realpath(dirname(__FILE__)) . "/language.json";
  return file_get_contents($filename);
}

function getLanguageStructure()
{
  echo getLanguageJson();
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

function addTranslation($key, $value = "") {
  $json = getLanguageJson();
  $lang = json_decode($json, true);

  $keys = explode('_', $key);
  $group = $keys[0];
  array_shift($keys);
  $name = join('_', $keys);

  foreach ($lang['content'] as &$langGroup) {
    if($langGroup['name'] == $group) {
      $newChild = array();
      $newChild['name'] = $name;
      $newChild['default'] = "";
      $langGroup['childs'][] = $newChild;
    }
  }

  $jsonStr = json_encode($lang);
  if ($jsonStr != null) {
    file_put_contents(realpath(dirname(__FILE__)) . "/language.json", $jsonStr);
  }
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
