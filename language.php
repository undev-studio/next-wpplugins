<?php

// if(PLL_INC) require_once( PLL_INC . '/api.php');


function getTLD()
{
  $server = $_SERVER['SERVER_NAME'];
  $urlParts = explode('.', $server);
  return $urlParts[count($urlParts) - 1];
}

$langCode = getTLD();
$tld = $langCode;

if ($langCode == 'com') $langCode = 'en';
if ($langCode == 'ch') $langCode = 'de';

$langs = array();

global $post;

if (function_exists('pll_the_languages')) {
  global $langCode;
  $langCode = pll_current_language();
}
// // $langCode=$langCode."_en";
// echo $langCode.'!!!';


$langData = null;

function loadLangData($langCode)
{
  if (!file_exists(dirname(__FILE__) . "/lang_" . $langCode . ".json")) $langCode = 'en';

  if (file_exists(dirname(__FILE__) . "/lang_" . $langCode . ".json")) {
    global $langData;
    $filename = dirname(__FILE__) . "/lang_" . $langCode . ".json";
    $string = file_get_contents($filename);
    // echo $filename;
    $langData = json_decode($string, true);
  }

  if ($langData == null) {
    echo 'unknown lang file for ' . $langCode;
  }

}

loadLangData($langCode);

// echo $langCode;

function nextLang()
{
  global $langData;
  return $langData;
}

function nextLangg()
{
  global $langData;
  return $langData;
}

function nextTranslate($key, $createMissing = false)
{
  global $langCode;

  if (function_exists('pll_the_languages')) {
    if ($langCode != pll_current_language()) {
      $langCode = pll_current_language();
      loadLangData($langCode);
    }
  }

  global $langData;

  if (isset($langData[$key])) {
    return $langData[$key];
  } else if ($createMissing || Util::startsWith($key, 'numbersfacts_')) {
    // check if key already exists in language file
    $filename = realpath(dirname(__FILE__)) . "/language.json";
    $langJson = file_get_contents($filename);
    $langKeys = json_decode($langJson);
    $numbersfacts = array_filter($langKeys->content, function ($e) {
      return $e->name == 'numbersfacts';
    });
    $first_key = key($numbersfacts);
    $suffix = preg_replace('/^numbersfacts_/', '', $key);
    $keyValues = array_filter($numbersfacts[$first_key]->childs, function ($e) use ($suffix) {
      return $e->name == $suffix;
    });
    if (count($keyValues) == 0) {
      addTranslation($key);
      loadLangData($langCode);
    }
    return $langData[$key];
  } else {
    return '? [' . $langCode . '] ' . $key;
  }
}

function nextTranslateStartsWith($s)
{
  global $langData;
  $result = array();

  foreach ($langData as $key => $d) {
    if (Util::startsWith($key, $s)) {
      $result[$key] = $d;

    }
  }


  return $result;
}
