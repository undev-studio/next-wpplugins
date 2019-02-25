<?php

require_once('widget_.php');

function upload_scripts()
{
    wp_enqueue_media();
}

add_action('admin_enqueue_scripts', 'upload_scripts');
// add_action('admin_enqueue_styles', array($this, 'upload_styles'));


class next_iconwidget extends unWidget
{

    function next_iconwidget()
    {
        $widget_ops = array('classname' => 'next_iconwidget', 'description' => '' );
        $this->WP_Widget('next_iconwidget', 'Next Icon Widget', $widget_ops);
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
            'displaysize' => '',
            'title' => '',
            'linktext' => '',
            'seo_linktitle' => ''

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

        echo $this->getWidgetInput(
            'linktext',
            $this->get_field_id('linktext'),
            $this->get_field_name('linktext'),
            $instance['linktext']);

        echo $this->getWidgetInput(
            'seo_linktitle',
            $this->get_field_id('seo_linktitle'),
            $this->get_field_name('seo_linktitle'),
            $instance['seo_linktitle']);

        


        echo '<a target="blank" href="http://fortawesome.github.io/Font-Awesome/icons/">Icons können hier angesehen werden.</a>';
        echo '<br/>Nach Klick auf das Icon den Code kopieren und hier einfügen:<br/> zB: f1ec ';

        echo $this->getWidgetInput(
            'icon',
            $this->get_field_id('icon'),
            $this->get_field_name('icon'),
            $instance['icon']);

        echo $this->getWidgetInputDisplaySize(
            'Geräte',
            $this->get_field_id('displaysize'),
            $this->get_field_name('displaysize'),
            $instance['displaysize']);

        echo $this->getWidgetInput(
            'linkURL',
            $this->get_field_id('linkURL'),
            $this->get_field_name('linkURL'),
            $instance['linkURL']);
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['icon'] = $new_instance['icon'];
        $instance['text'] = $new_instance['text'];
        $instance['linkURL'] = $new_instance['linkURL'];
        $instance['seo_linktitle'] = $new_instance['seo_linktitle'];
        $instance['displaysize'] = $new_instance['displaysize'];
        $instance['linktext'] = $new_instance['linktext'];
        $instance['seo_alttext'] = $new_instance['seo_alttext'];
        
        

        return $instance;
    }

    function widget($args, $instance)
    {
        global $post;

        // $image=$instance['image'];
        $icon='';
        $html='';
        
        if($instance['icon']) $instance['icon']='&#x'.$instance['icon'].';';

        $instance['classes']=$this->getDisplaySizeClasses($instance);

        $twig=initTwig();
        $template = $twig->loadTemplate('widget_iconwidget.html');
        $html.=$template->render(array(
            'data' => $instance,
            'classes' => $this->getDisplaySizeClasses($instance)
        ));

        echo $html;

    }
}



?>