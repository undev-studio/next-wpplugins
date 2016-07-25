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
    $string = file_get_contents(dirname(__FILE__)."/lang_".$tld.".json");
    $lang = json_decode($string, true);

}


function nextTranslate($key)
{

    if(isset($lang[$key])) return $tld.'_'.$lang[$key];
    else return '?_'.$key;
}

// echo nextTranslate('cds');


?>