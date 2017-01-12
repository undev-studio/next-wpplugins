<?php

    error_reporting(E_ERROR|E_WARNING);
    ini_set('display_errors', '1');


    require_once( realpath(dirname(__FILE__)). '/settings_next.php');


    // ------------

    if( $_REQUEST['savenextsettings'])
    {
        $cfg=array();
        
        $cfg[nextSettings::USE_MEGA_MENU]=(bool)$_REQUEST[nextSettings::USE_MEGA_MENU];

        $cfg=nextSettings::save($cfg);

        echo '<br/>';
        echo 'settings saved!';
        echo '<br/>';
    }






    $cfg=nextSettings::load();



    require_once( realpath(dirname(__FILE__)). '/language.php');


    echo '<form method="POST">';
    echo '<input type="hidden" name="savenextsettings" value="true"/>';
 
    echo '<div class="wrap">';
    echo '<h2>Settings</h2>';

    echo '<table class="form-table"><tbody>';


    // -----------------------------


    echo '<tr>';
    echo '<th scope="row">Megamenu</th>';
    echo '<td><fieldset><legend class="screen-reader-text"></legend>';

    $checked='';
    if($cfg[nextSettings::USE_MEGA_MENU]) $checked='checked="checked"';
    echo '<input name="'.nextSettings::USE_MEGA_MENU.'" type="checkbox" id="'.nextSettings::USE_MEGA_MENU.'" value="true" '.$checked.'>';
    echo '<label for="'.nextSettings::USE_MEGA_MENU.'">';
    echo 'Use Megamenu<br>';
    echo '</label>';

    echo '<br/>';

    echo '</fieldset></td>';
    echo '</tr>';

    // -----------------------------


    // -----------------------------



    echo '</table>';

    echo '<input type="submit" value="Save"/>';

    echo '</div>';

    echo '</form>';



echo "<pre>";
var_dump($cfg);
echo "</pre>";


?>