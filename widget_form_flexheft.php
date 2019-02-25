<?php
/*
Plugin Name: Next Form Flexheft
Description: 
Author: undefined development
Version: 1
Author URI: http://undev.de/
*/

require_once('widget_.php');

class nextFormFlexheftWidget extends unWidget
{
    function nextFormFlexheftWidget()
    {
        $widget_ops = array('classname' => 'nextFormFlexheftWidget', 'description' => '' );
        $this->WP_Widget('nextFormFlexheftWidget', 'Next Form Flexheft', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '',
            'displaysize' => '',
            'text' => ''
            ));

        echo $this->getWidgetInput(
            'title',
            $this->get_field_id('title'),
            $this->get_field_name('title'),
            $instance['title']);

        echo $this->getWidgetInputArea(
            'Text',
            $this->get_field_id('text'),
            $this->get_field_name('text'),
            $instance['text']);

        echo $this->getWidgetInputArea(
            'Text Bottom',
            $this->get_field_id('textBottom'),
            $this->get_field_name('textBottom'),
            $instance['textBottom']);

        echo $this->getWidgetInputArea(
            'Text Success',
            $this->get_field_id('textSuccess'),
            $this->get_field_name('textSuccess'),
            $instance['textSuccess']);

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
        $instance['textBottom'] = $new_instance['textBottom'];
        $instance['textSuccess'] = $new_instance['textSuccess'];
        $instance['displaysize'] = $new_instance['displaysize'];
        return $instance;
    }

    function widget($args, $instance)
    {
        global $post;

        $classes='';
        if($instance['displaysize']=='allbutphones') $classes='hide-phone';

        $twig=initTwig();
        $template = $twig->loadTemplate('widget_form_flexheft.html');
        $html.=$template->render(array(
            'data' => $instance,
            'wp' => $wp,
            'displayClasses' => $classes
        ));
        
        echo $html;
    }
}


?>