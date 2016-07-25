<?php

$string = file_get_contents(dirname(__FILE__)."/lang_de.json");
$lang = json_decode($string, true);

function nextTranslate($key)
{

    if(isset($lang[$key])) return $lang[$key];
    else return '?_'.$key;
}

// echo nextTranslate('cds');


?>