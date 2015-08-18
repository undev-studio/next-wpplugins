<?php
/*
Plugin Name: Next Text - Grey Box
Description: 
Author: undefined development
Version: 1
Author URI: http://undev.de/
*/

require_once('widget_.php');


class nextTextWidget extends unWidget
{

    function nextTextWidget()
    {
        $widget_ops = array('classname' => 'nextTextWidget', 'description' => '' );
        $this->WP_Widget('nextTextWidget', 'Next Text - Grey Box', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '',
            'displaysize' => '',
            'text' => ''
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
        $instance['displaysize'] = $new_instance['displaysize'];
        return $instance;
    }

    function widget($args, $instance)
    {
        global $post;

        echo '<div class="bgbox '.$this->getDisplaySizeClasses($instance).'">';
        echo '<h3>'.$instance['title'].'</h3>';
        echo $instance['text'];
        echo '</div>';
    }
}



?>