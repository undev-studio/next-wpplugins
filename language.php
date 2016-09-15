<?php


function getTLD()
{
    $server=$_SERVER['SERVER_NAME'];
    $urlParts=explode('.',$server);
    return $urlParts[count($urlParts)-1];
}

$tld=getTLD();
if($tld=='de' || $tld=='at')
{
    $filename=dirname(__FILE__)."/lang_".$tld.".json";
    $string = file_get_contents($filename);
    $lang = json_decode($string, true);


}

// echo $tld;


function nextTranslate($key)
{
    global $tld;
    global $lang;
    if(isset($lang[$key])) return $lang[$key];
    else return '?_'.$tld.'_'.$key;
}

// echo nextTranslate('cds');
 // echo '<!--'.nextTranslate('widget_form_minierloes_from_email').' ---  '.dirname(__FILE__)."/lang_".$tld.".json".'-->';

?>