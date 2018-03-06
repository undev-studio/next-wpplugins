<?php


class unWidget extends WP_Widget
{


    function getWidgetIconSelect($title,$id,$fieldname,$value)
    {
        $html='<p>'.$title.': ';

        $icons = array();

        $icons[] = array("Telefon","f10b");
        $icons[] = array("E-Mail","f003");
        $icons[] = array("Ihr neuer Stromtarif","f153");
        $icons[] = array("FAQs","f129");
        $icons[] = array("Mitmachen","f090");
        $icons[] = array("Nützliche Downloads","f019");
        $icons[] = array("Hilfreiche Links","f0c1");
        $icons[] = array("Redneranfrage","f0e6");
        $icons[] = array("Kennzahlen","f03a");
        $icons[] = array("Treffen Sie uns","f073");
        $icons[] = array("Mini-Erlösrechner","f1ec");
        $icons[] = array("Flex-Heft bestellen","f07a");
        $icons[] = array("Social Twitter","f099");
        $icons[] = array("Social Facebook","f082");
        $icons[] = array("Social Google+","f0d5");
        $icons[] = array("List Arrow","f0a9");


        $html.='<select name="'.$fieldname.'" id="'.$id.'" >';
        $html.='<option value=""> - </option>';

        foreach ($icons as $icon)
        {
            $sel="";
            if($icon[1]==$value)$sel=' selected="SELECTED" ';
            $html.='<option value="'.$icon[1].'" '.$sel.'>'.$icon[0].'</option>';
        }
        $html.='</select>';
        $html.='</p>';
        return $html;
    }



    function getWidgetMediaCategory($title,$id,$fieldname,$value)
    {
        $html='<p>'.
                $title.': ';

        $categories = get_terms( 'media_category', 'orderby=count&hide_empty=0' );

        $html.='<select name="'.$fieldname.'" id="'.$id.'" >';
        $html.='<option value=""> - </option>';

        foreach ($categories as $cat)
        {
            $sel="";
            if($cat->term_id==$value)$sel=' selected="SELECTED" ';
            $html.='<option value="'.$cat->term_id.'" '.$sel.'>'.$cat->name.'</option>';
        }
        $html.='</select>';
        $html.='</p>';
        return $html;
    }


    function getWidgetInputDisplaySize($title,$id,$fieldname,$value)
    {
        $str='<p>'.
                $title.': '.
                '<select '.
                    'id="'.$id.'" '.
                    'name="'.$fieldname.'" '.
                    '>';

        $sel='';
        $str.='<option '.$sel.' value="">Alle Devices</option>';

        if($value=="allbutphones")$sel=' selected="SELECTED" ';
        $str.='<option '.$sel.' value="allbutphones">All but Phones</option>';

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
