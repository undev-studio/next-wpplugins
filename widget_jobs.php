<?php
/*
Plugin Name: Next Jobs Widget
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

add_action('admin_enqueue_scripts', 'upload_scripts');
// add_action('admin_enqueue_styles', array($this, 'upload_styles'));

class nextJobsWidget extends unWidget
{

    function nextJobsWidget()
    {
        $widget_ops = array('classname' => 'nextJobsWidget', 'description' => '' );
        $this->WP_Widget('nextJobsWidget', 'Next Jobs Widget', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '',
            'icon' => '',
            'text' => '',
            'title' => '',
            'displaysize' => ''
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


        echo $this->getWidgetInput(
            'button_title',
            $this->get_field_id('button_title'),
            $this->get_field_name('button_title'),
            $instance['button_title']);

        echo $this->getWidgetInput(
            'button_link',
            $this->get_field_id('button_link'),
            $this->get_field_name('button_link'),
            $instance['button_link']);
        
        echo $this->getWidgetInputDisplaySize(
            'Geräte',
            $this->get_field_id('displaysize'),
            $this->get_field_name('displaysize'),
            $instance['displaysize']);


    }

    function update($new_instance, $old_instance)
    {
        return $new_instance;
    }

    function widget($args, $instance)
    {
        global $post;
        global $wpdb;
        $sql='SELECT * FROM next_jobs WHERE activated =1 ORDER BY sort ';
        $jobs=$wpdb->get_results( $sql );

        if($wpdb->last_error!='') printError($wpdb->last_error); 


        $data=array();
        $data['jobs']=$jobs;
        $data['text']=$instance['text'];
        $data['title']=$instance['title'];
        $data['displaysize']=$instance['displaysize'];
        $data['button_title']=$instance['button_title'];
        $data['button_link']=$instance['button_link'];

        foreach( $jobs as $job)
        {
            $job->text=strip_tags($job->text);
            if(strlen($job->text)>190) $job->text=substr($job->text,0,190)."...";
        }



        $classes='';
        if($instance['displaysize']=='allbutphones') $classes='hide-phone';

        $twig=initTwig();
        $template = $twig->loadTemplate('widget_jobs.html');
        $html.=$template->render(array(
            'data' => $data,
            'wp' => $wp,
            'displayClasses' => $classes
        ));
        
        echo $html;


    }
}


?>