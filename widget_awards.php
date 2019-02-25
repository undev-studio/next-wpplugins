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

class nextAwardsWidget extends unWidget
{

    function nextAwardsWidget()
    {
        $widget_ops = array('classname' => 'nextAwardsWidget', 'description' => '' );
        $this->WP_Widget('nextAwardsWidget', 'Next Awards Widget', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array(
            'title' => '',
            'text' => '',
            'mediaCategory' => '',
            ));

        echo $this->getWidgetInput(
            'title',
            $this->get_field_id('title'),
            $this->get_field_name('title'),
            $instance['title']);

        echo $this->getWidgetInput(
            'text',
            $this->get_field_id('text'),
            $this->get_field_name('text'),
            $instance['text']);

        echo $this->getWidgetMediaCategory(
            'media',
            $this->get_field_id('mediaCategory'),
            $this->get_field_name('mediaCategory'),
            $instance['mediaCategory']);


    }

    function update($new_instance, $old_instance)
    {
        return $new_instance;
    }

    function widget($args, $instance)
    {
        global $post;

        $data=array();

        $style='padding-bottom:0px;';
        if($instance['text']=='')$style.='padding-top:0px; !important';

        echo'<div class="bgbox" style="'.$style.'">';
        echo'<h3>'.$instance['title'].'</h3>';
        if($instance['text']!='') echo'<p class="space-bottom">'.$instance['text'].'</p>';

        echo'<div id="next_awards" data-catid="'.$instance['mediaCategory'].'"></div>';
        echo'</div>';



    }
}


?>
