<?php

    error_reporting(E_ERROR|E_WARNING);
    ini_set('display_errors', '1');

    require_once( realpath(dirname(__FILE__)). '/settings_next.php');

    // ------------

    if( $_REQUEST['savenextsettings'])
    {
        $cfg=array();
        
        $cfg[nextSettings::USE_MEGA_MENU]=(bool)$_REQUEST[nextSettings::USE_MEGA_MENU];
        $cfg[nextSettings::POST_CAT_BLOG]=$_REQUEST[nextSettings::POST_CAT_BLOG];
        $cfg[nextSettings::POST_CAT_WE]=$_REQUEST[nextSettings::POST_CAT_WE];
        $cfg[nextSettings::POST_CAT_OTHERS]=$_REQUEST[nextSettings::POST_CAT_OTHERS];

        $cfg=nextSettings::save($cfg);

        echo '<br/>';
        echo 'settings saved!';
        echo '<br/>';
    }

    $cfg=nextSettings::load();

    require_once( realpath(dirname(__FILE__)). '/language.php');

    if(is_admin())
    {
        echo '<form method="POST">';
        echo '<input type="hidden" name="savenextsettings" value="true"/>';
     
        echo '<div class="wrap">';
        echo '<h2>Settings</h2>';

        echo '<table class="form-table" style="width:50%;"><tbody>';

        // -----------------------------

        echo '<tr>';
        echo '<th scope="row">Megamenu</th>';
        echo '<td><fieldset><legend class="screen-reader-text"></legend>';
        echo 'Use Megamenu';
        echo '</td><td>';
        $checked='';
        if($cfg[nextSettings::USE_MEGA_MENU]) $checked='checked="checked"';
        echo '<input name="'.nextSettings::USE_MEGA_MENU.'" type="checkbox" id="'.nextSettings::USE_MEGA_MENU.'" value="true" '.$checked.'>';
        echo '<label for="'.nextSettings::USE_MEGA_MENU.'">';
        echo '</label>';


        echo '<br/>';

        echo '</fieldset></td>';
        echo '</tr>';

        // -----------------------------

        echo '<tr>';
        echo '<th scope="row">Post Categories</th>';
        echo '<td><fieldset><legend class="screen-reader-text"></legend>';


        echo '<label for="'.nextSettings::POST_CAT_BLOG.'">';
        echo 'Blog: ';
        echo '</label>';
        echo '</td><td>';
        echo '<input name="'.nextSettings::POST_CAT_BLOG.'" type="text" id="'.nextSettings::POST_CAT_BLOG.'" value="'.$cfg[nextSettings::POST_CAT_BLOG].'" >';

        echo '<br/>';

        echo '</fieldset></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"></th>';
        echo '<td><fieldset><legend class="screen-reader-text"></legend>';
        echo '<label for="'.nextSettings::POST_CAT_WE.'">';
        echo 'We about us: ';
        echo '</label>';
        echo '</td><td>';
        echo '<input name="'.nextSettings::POST_CAT_WE.'" type="text" id="'.nextSettings::POST_CAT_WE.'" value="'.$cfg[nextSettings::POST_CAT_WE].'" >';
        echo '</fieldset></td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th scope="row"></th>';
        echo '<td><fieldset><legend class="screen-reader-text"></legend>';
        echo '<label for="'.nextSettings::POST_CAT_OTHERS.'">';
        echo 'Others about us: ';
        echo '</label>';
        echo '</td><td>';
        echo '<input name="'.nextSettings::POST_CAT_OTHERS.'" type="text" id="'.nextSettings::POST_CAT_OTHERS.'" value="'.$cfg[nextSettings::POST_CAT_OTHERS].'" >';
        echo '</fieldset></td>';
        echo '</tr>';

        // -----------------------------

        echo '<tr>';
        echo '<td></td><td></td><td>';
        echo '<input type="submit" value="Save"/>';

        echo '</td></tr>';
        echo '</table>';

        echo '</div>';
        echo '</form>';

        echo "<pre>";
        // var_dump($cfg);
        echo "</pre>";
    }

?>