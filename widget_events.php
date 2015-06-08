<?php
/*
Plugin Name: Next Events
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


class nextEventsWidget extends unWidget
{

    function nextEventsWidget()
    {
        $widget_ops = array('classname' => 'nextEventsWidget', 'description' => '' );
        $this->WP_Widget('nextEventsWidget', 'Next Events', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '',
            'icon' => '',
            'text' => '',
            'textlength' => '50',
            'displaysize' => '',
            'title' => 'Alle Termine'

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

        echo $this->getWidgetInputArea(
            'textlength',
            $this->get_field_id('textlength'),
            $this->get_field_name('textlength'),
            $instance['textlength']);

        echo $this->getWidgetInputArea(
            'linktext',
            $this->get_field_id('linktext'),
            $this->get_field_name('linktext'),
            $instance['linktext']);

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
        global $post;
        global $wpdb;
        $sql='SELECT * FROM next_events WHERE DATEDIFF(date_start,NOW())>=0 OR DATEDIFF(date_end,NOW())>=0 ORDER BY DATEDIFF(date_start,NOW()) ASC LIMIT 0,3';
        $events=$wpdb->get_results( $sql );

        if($wpdb->last_error!='') echo($wpdb->last_error); 

        $data=array();
        $data['events']=$events;
        $data['text']=$instance['text'];
        $data['title']=$instance['title'];
        $data['linktext']=$instance['linktext'];

        if(!is_numeric($instance['textlength']) )$instance['textlength']=60;


        foreach( $events as $event)
        {
            $ds = strtotime( $event->date_start );
            $de = strtotime( $event->date_end );

            $day_start=date( 'd', $ds );
            $month_start=date( 'm', $ds );

            $day_end=date( 'd', $de );
            $month_end=date( 'm', $de );

            // start/end on same day!
            if($day_end == $day_start && $month_start==$month_end) $event->date_readable=date( 'd.m.Y', $ds );
            else
            // start end in same month
            if($month_start==$month_end) $event->date_readable=date( 'd.', $ds ).' - '.date( 'd.m.Y', $de );
            else
            // start end in same month
            $event->date_readable=date( 'd.m.', $ds ).' - '.date( 'd.m.Y', $de );


            $event->text=strip_tags($event->text);
            if(strlen($event->text)>$instance['textlength']) $event->text=truncateHtml($event->text,$instance['textlength'],'...');
        }
       
        $twig=initTwig();
        $template = $twig->loadTemplate('widget_events.html');
        $html.=$template->render(array(
            'data' => $data,
            'classes' => $this->getDisplaySizeClasses($instance),
            'wp' => $wp
        ));
        
        echo $html;


    }
}


?>
