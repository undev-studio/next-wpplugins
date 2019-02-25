<?php

require_once('widget_.php');
/*''
function nitupload_scripts()
{
    wp_enqueue_media();
}
*/
add_action('admin_enqueue_scripts', 'wp_enqueue_media');

class nextImageSpecialWidget extends unWidget
{

    function nextImageSpecialWidget()
    {
        $widget_ops = array('classname' => 'nextImageSpecial', 'description' => '' );
        $this->WP_Widget('nextImageSpecial', 'Next Image Teaser Special', $widget_ops);
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
            'url' => '',
            'displaysize' => '',
            'buttontext' => '',
            'title' => ''
        ));

        echo $this->getWidgetInput(
            'title',
            $this->get_field_id('title'),
            $this->get_field_name('title'),
            $instance['title']);

        echo $this->getWidgetMediaInput(
            'image',
            $this->get_field_id('image'),
            $this->get_field_name('image'),
            $instance['image']);

        echo $this->getWidgetInputArea(
            'text',
            $this->get_field_id('text'),
            $this->get_field_name('text'),
            $instance['text']);

        echo $this->getWidgetInput(
            'url',
            $this->get_field_id('url'),
            $this->get_field_name('url'),
            $instance['url']);


        echo $this->getWidgetInput(
            'seo_linktitle',
            $this->get_field_id('seo_linktitle'),
            $this->get_field_name('seo_linktitle'),
            $instance['seo_linktitle']);

        echo $this->getWidgetInput(
            'seo_alttext',
            $this->get_field_id('seo_alttext'),
            $this->get_field_name('seo_alttext'),
            $instance['seo_alttext']);


        echo $this->getWidgetInput(
            'buttontext',
            $this->get_field_id('buttontext'),
            $this->get_field_name('buttontext'),
            $instance['buttontext']);

        echo $this->getWidgetInputDisplaySize(
            'Geräte',
            $this->get_field_id('displaysize'),
            $this->get_field_name('displaysize'),
            $instance['displaysize']);
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['image'] = $new_instance['image'];
        $instance['text'] = $new_instance['text'];
        $instance['url'] = $new_instance['url'];
        $instance['displaysize'] = $new_instance['displaysize'];
        $instance['buttontext'] = $new_instance['buttontext'];
        $instance['seo_linktitle'] = $new_instance['seo_linktitle'];
        $instance['seo_alttext'] = $new_instance['seo_alttext'];
        
        return $instance;
    }

    function widget($args, $instance)
    {
        global $post;

        $html='';
        $instance['classes'] = $this->getDisplaySizeClasses($instance);

        $twig = initTwig();
        $template = $twig->loadTemplate('widget_image_special.html');
        $html .= $template->render(array(
            'data' => $instance,
            'classes' => $this->getDisplaySizeClasses($instance)
        ));

        echo $html;
    }
}



?>