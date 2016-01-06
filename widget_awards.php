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
            'title' => ''
            ));

        echo $this->getWidgetInput(
            'title',
            $this->get_field_id('title'),
            $this->get_field_name('title'),
            $instance['title']);


    }

    function update($new_instance, $old_instance)
    {
        return $new_instance;
    }

    function widget($args, $instance)
    {
        global $post;

        $data=array();
        $data['title']=$instance['title'];

        echo'<div class="bgbox">';
        echo'<div id="next_awards">awards...</div>';
        echo'</div>';



    }
}


?>
