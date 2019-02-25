<?php
/*
Plugin Name: Next Erloes Widget
Description: 
Author: undefined development
Version: 1
Author URI: http://undev.de/
*/

// function upload_scripts()
// {
//     wp_enqueue_media();
// }

require_once('widget_.php');
require_once('twig.php');
require_once('truncatehtml.php');



add_action('admin_enqueue_scripts', 'upload_scripts');
// add_action('admin_enqueue_styles', array($this, 'upload_styles'));


class nextErloesWidget extends unWidget
{

    function nextErloesWidget()
    {
        $widget_ops = array('classname' => 'nextErloesWidget', 'description' => '' );
        $this->WP_Widget('nextErloesWidget', 'Next Erloes', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => 'Erlösberechnung',
            'icon' => '',
            'text' => 'Welche Mehrerlöse können Sie mit Ihrer Anlage erzielen?',
            'displaysize' => '',

            ) );

        echo $this->getWidgetInput(
            'title',
            $this->get_field_id('title'),
            $this->get_field_name('title'),
            $instance['title']);

        echo $this->getWidgetInputArea(
            'text',
            $this->get_field_id('text'),
            $this->get_field_name('text'),
            $instance['text']);


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
        $instance['text'] = $new_instance['text'];
        $instance['textlength'] = $new_instance['textlength'];
        $instance['displaysize'] = $new_instance['displaysize'];
        $instance['linktext'] = $new_instance['linktext'];
        return $instance;
    }

    function widget($args, $instance)
    {
        $data=array();
        $data['text']=$instance['text'];
        $data['title']=$instance['title'];


       
        $twig=initTwig();
        $template = $twig->loadTemplate('widget_erloes.html');
        $html.=$template->render(array(
            'data' => $data,
            'classes' => $this->getDisplaySizeClasses($instance),
            'wp' => $wp
        ));
        
        echo $html;


    }
}


?>
