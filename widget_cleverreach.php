<?php
/*
Plugin Name: Next Cleverreach
Description: 
Author: undefined development
Version: 1
Author URI: http://undev.de/
*/

// function upload_scripts()
// {
//     wp_enqueue_media();
// }

add_action('admin_enqueue_scripts', 'upload_scripts');
// add_action('admin_enqueue_styles', array($this, 'upload_styles'));



function next_cleverreach_ajax_callback() 
{
    $apiKey = "0592af3841157578d4907311b5ebce38-2";
    $wsdl_url="http://api.cleverreach.com/soap/interface_v5.1.php?wsdl";
    $listId = "526774";
    $formId="156538";




    global $wpdb; // this is how you get access to the database
    // $whatever = intval( $_POST['whatever'] );


    $api = new SoapClient($wsdl_url);

     $user = array(
         "email" => $_REQUEST['email'],
         "registered" => time(),
         // "activated" => time(),
         "source" => "Next Website "

     );
     
    $result = $api->receiverAdd($apiKey, $listId, $user);
    // echo '-------------';
    // print_r($result);


    $doidata = array(
        "user_ip" => $_SERVER['REMOTE_ADDR'], //the IP of the user who registered. not yours!
        "user_agent" => $_SERVER['HTTP_USER_AGENT'],
        "referer" => "https://www.nextkraftwerke.de",
        "postdata" => "firtsname:bruce,lastname:whayne,nickname:Batman" //just an example. any txt format will do.
    );

    $result = $api->formsSendActivationMail($apiKey, $formId, $_REQUEST['email'],$doidata);

    // echo '-------------';
    // print_r($result);

    if($result->status=="SUCCESS"){                 //successfull list call
        echo "success";
    }else{              
        echo "fail";
    }
    


    wp_die(); 
}

add_action( 'wp_ajax_next_cleverreach', 'next_cleverreach_ajax_callback' );


class nextCleverreachWidget extends WP_Widget
{

    function nextCleverreachWidget()
    {
        $widget_ops = array('classname' => 'nextCleverreachWidget', 'description' => '' );
        $this->WP_Widget('nextCleverreachWidget', 'Next Cleverreach', $widget_ops);
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


    function getMediaInput($title,$id,$fieldname,$value)
    {
        global $wpdb;

        $html='';
        $html.='<div>';
        $html.='<div class="wp-menu-image dashicons-before dashicons-admin-media" style="float:left;padding-right:20px;" onclick="selectImageWidget(\'#'.$fieldName.'\')"><br></div>';
        $html.='<input type="text" name="'.$fieldname.'" class="formimageinput" id="'.$fieldname.'" value="'.$value.'" class="unrow" style="float:left;width:80%;"/>';

        // $html.='<a onclick="selectImage(\'#'.$fieldname.'\')">select Image</a>';
        $html.='<div style="clear:both;"></div>';
        $html.='</div>';

        $html.='<br/><img src="'.$value.'" style="width:100px;border:5px solid white;" onclick="selectImageWidget(\'#'.$fieldname.'\')"/>';


        return $html;
    }


    function form($instance)
    {
        echo '<script>';
        echo 'function selectImageWidget(id)';
        echo '{';
        echo '    custom_uploader = wp.media.frames.file_frame = wp.media({';
        echo '        title: "Choose Image",';
        echo '        button: {';
        echo '            text: "Choose Image"';
        echo '        },';
        echo '        multiple: false';
        echo '    });';

        echo '    custom_uploader.on("select", function()';
        echo '    {';
        echo '        attachment = custom_uploader.state().get("selection").first().toJSON();';
        echo '        jQuery(".formimageinput").val(attachment.url);';
        echo '    });';

        echo '    custom_uploader.open();';
        echo '}';
        echo '</script>';

        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '',
            'icon' => '',
            'text' => '',
            'text_ok' => '',
            'text_fail' => '',
            'title' => ''

            ) );

        echo $this->getWidgetInputArea(
            'text',
            $this->get_field_id('text'),
            $this->get_field_name('text'),
            $instance['text']);

        echo $this->getWidgetInput(
            'title',
            $this->get_field_id('title'),
            $this->get_field_name('title'),
            $instance['title']);

        echo '<a target="blank" href="http://fortawesome.github.io/Font-Awesome/icons/">Icons können hier angesehen werden.</a>';
        echo '<br/>Nach Klick auf das Icon den Code kopieren und hier einfügen:<br/> zB: f1ec ';


        echo $this->getWidgetInput(
            'icon',
            $this->get_field_id('icon'),
            $this->get_field_name('icon'),
            $instance['icon']);


        echo $this->getWidgetInput(
            'text_ok',
            $this->get_field_id('text_ok'),
            $this->get_field_name('text_ok'),
            $instance['text_ok']);

        echo $this->getWidgetInput(
            'text_fail',
            $this->get_field_id('text_fail'),
            $this->get_field_name('text_fail'),
            $instance['text_fail']);


    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['icon'] = $new_instance['icon'];
        $instance['text'] = $new_instance['text'];
        $instance['text_ok'] = $new_instance['text_ok'];
        $instance['text_fail'] = $new_instance['text_fail'];
        return $instance;
    }

    function widget($args, $instance)
    {
        global $post;

        $image=$instance['image'];
        $icon='';
        if($instance['icon']) $icon='&#x'.$instance['icon'];

        echo '<div class="bgbox nextwidget widgetwithicon" >';


        if($icon) echo '<div class="left bigGreenIcon" style="margin-right:10px;">'.$icon.'</div>';
echo '  <h3>'.$instance['title'].'</h3>';

        echo '<div id="cleverreach_form" class="space-top">';

        
        echo $instance['text'];

        echo '  <br/><br/>';
        echo '  <input id="cleverreachemail" placeholder="E-Mail Adresse" type="text" />';
        echo '  <br/>';
        echo '  <input type="submit" value="Anmelden" class="button" onclick="testajax();"/>';

        echo '</div>';


        echo '<div id="cleverreach_ok" class="hide">';
        echo $instance['text_ok'];
        echo '  <br/><br/>';
        echo '</div>';

        echo '<div id="cleverreach_fail" class="hide space-top">';
        echo $instance['text_fail'];
        echo '</div>';


        echo '</div>';
    }
}



?>