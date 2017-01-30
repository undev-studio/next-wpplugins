<?php

// if(PLL_INC) require_once( PLL_INC . '/api.php');


function getTLD()
{
    $server=$_SERVER['SERVER_NAME'];
    $urlParts=explode('.',$server);
    return $urlParts[count($urlParts)-1];
}

$langCode=getTLD();
$tld=$langCode;

if($langCode=='com')$langCode='en';
if($langCode=='ch')$langCode='de';

$langs=array();

global $post;

// echo pll_current_language();
if(function_exists('pll_the_languages') )
{
    global $langCode;
    $langCode=pll_current_language();
}
// // $langCode=$langCode."_en";
    // echo $langCode.'!!!';


$langData=null;

function loadLangData($langCode)
{
    if(file_exists( dirname(__FILE__)."/lang_".$langCode.".json" ))
    {
        global $langData;
        $filename=dirname(__FILE__)."/lang_".$langCode.".json";
        $string = file_get_contents($filename);
        // echo $filename;
        $langData = json_decode($string, true);
    }

    if($langData==null)
    {
        echo 'unknown lang file for '.$langCode;
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

function nextTranslate($key)
{
    global $langCode;
    
    if(function_exists('pll_the_languages') )
    {
        if($langCode!=pll_current_language())
        {
            $langCode=pll_current_language();
            loadLangData($langCode);
        }
    } 

    global $langData;
    
    if(isset($langData[$key])) return $langData[$key];
    else return '? ['.$langCode.'] '.$key;
}

// echo '<!-- lang debug';
// echo($langCode);
// echo '-----';
// var_dump($langData);
// echo '-->';


// var_dump($langCode);

// echo nextTranslate('language_other_language');
// echo '<!--'.nextTranslate('widget_form_minierloes_from_email').' ---  '.dirname(__FILE__)."/lang_".$tld.".json".'-->';

?>