<?php 


class unWidget extends WP_Widget
{
    function getWidgetInputDisplaySize($title,$id,$fieldname,$value)
    {
        $str='<p>'.
                $title.': '.
                '<select '.
                    'id="'.$id.'" '.
                    'name="'.$fieldname.'" '.
                    '>';

        $sel='';
        $str.='<option '.$sel.' value="">Alle Geräte</option>';

        if($value=="allbutphones")$sel=' selected="SELECTED" ';
        $str.='<option '.$sel.' value="allbutphones">Alles ausser Telefone</option>';

        // $sel=''; if($value=="phones")$sel=' selected="SELECTED" ';
        // $str.='<option '.$sel.' value="phones">Nur Telefone</option>';

        $str.='</select>'.
            '</p>';

        return $str;
    }


    function getWidgetInput($title,$id,$fieldname,$value)
    {
        $str='<p>'.
                $title.': '.
                '<input class="widefat" type="text" '.
                    'id="'.$id.'" '.
                    'name="'.$fieldname.'" '.
                    'value="'.$value.'" />'.
                '</label>'.
            '</p>';

        return $str;
    }

    function getWidgetInputArea($title,$id,$fieldname,$value)
    {
        $str='<p>'.
                $title.': '.
                '<textarea class="widefat" type="text" '.
                    'id="'.$id.'" '.
                    'name="'.$fieldname.'" '.
                     '>'.$value.'</textarea>'.
                '</label>'.
            '</p>';

        return $str;
    }

    function getWidgetMediaInput($title,$id,$fieldname,$value)
    {
        global $wpdb;

        $html='';
        $html.='<div>';
        $html.='<div class="wp-menu-image dashicons-before dashicons-admin-media" style="float:left;padding-right:20px;" onclick="selectImageWidget(\'#'.$fieldname.'\')"><br></div>';
        $html.='<input type="text" name="'.$fieldname.'" class="formimageinput" id="'.$fieldname.'" value="'.$value.'" class="unrow" style="float:left;width:80%;"/>';

        // $html.='<a onclick="selectImage(\'#'.$fieldname.'\')">select Image</a>';
        $html.='<div style="clear:both;"></div>';
        $html.='</div>';

        $html.='<br/><img src="'.$value.'" style="width:100px;border:5px solid white;" onclick="selectImageWidget(\'#'.$fieldname.'\')"/>';

        return $html;
    }

    function getDisplaySizeClasses($instance)
    {
        if($instance['displaysize']=='allbutphones') return 'hide-phone';
        return '';
    }


}



?>