<?php
/*
Plugin Name: Next - Icons Left Text Right
Description: 
Author: undefined development
Version: 1
Author URI: http://undev.de/
*/

require_once('widget_.php');


class nextIconLeftTextRight extends unWidget
{

    function nextIconLeftTextRight()
    {
        $widget_ops = array('classname' => 'nextIconLeftTextRight', 'description' => '' );
        $this->WP_Widget('nextIconLeftTextRight', 'Next Text - Icon Left, Text Right', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 
            'text1' => '',
            'text2' => '',
            'text3' => '',
            'image1' => '',
            'image2' => '',
            'image3' => ''
            ) );

        echo $this->getWidgetInputArea(
            'Text 1',
            $this->get_field_id('text1'),
            $this->get_field_name('text1'),
            $instance['text1']);
        
        echo $this->getWidgetMediaInput(
            'Image 1',
            $this->get_field_id('image1'),
            $this->get_field_name('image1'),
            $instance['image1']);

        echo $this->getWidgetInputArea(
            'Text 2',
            $this->get_field_id('text2'),
            $this->get_field_name('text2'),
            $instance['text2']);
        
        echo $this->getWidgetMediaInput(
            'Image 2',
            $this->get_field_id('image2'),
            $this->get_field_name('image2'),
            $instance['image2']);

        echo $this->getWidgetInputArea(
            'Text 3',
            $this->get_field_id('text3'),
            $this->get_field_name('text3'),
            $instance['text3']);
        
        echo $this->getWidgetMediaInput(
            'Image 3',
            $this->get_field_id('image3'),
            $this->get_field_name('image3'),
            $instance['image3']);

    }

    function update($new_instance, $old_instance)
    {
        // $instance = $old_instance;
        // $instance['title'] = $new_instance['title'];
        // $instance['text'] = $new_instance['text'];
        return $new_instance;
    }

    function widget($args, $instance)
    {
        global $post;

        // echo '<div class="bgbox '.$this->getDisplaySizeClasses($instance).'">';
        // echo '<h3>'.$instance['title'].'</h3>';
        // echo $instance['text'];
        // echo '</div>';

        echo '<div class="bgbox">';
        echo '';
        echo '    <div class="row">';
        echo '        <div class="cute-3-tablet" style="padding-left:0px;">';
        echo '            <img src="'.$instance['image1'].'" alt="{{data.altimage}}" class="responsive-img"/>';
        echo '        </div>';
        echo '        <div class="cute-9-tablet" style="padding-left:0px;">';
        echo '            <p class="nomargin">';
        echo '                '.$instance['text1'];
        echo '            </p>';
        echo '        </div>';
        echo '    </div>';
        echo '';
        echo '    <div class="row">';
        echo '        <div class="cute-3-tablet">';
        echo '            <img src="'.$instance['image2'].'" alt="{{data.altimage1}}" class="responsive-img"/>';
        echo '        </div>';
        echo '        <div class="cute-9-tablet" style="padding-left:0px;">';
        echo '            <p class="nomargin">';
        echo '                '.$instance['text2'];
        echo '            </p>';
        echo '        </div>';
        echo '    </div>';
        echo '';
        echo '    <div class="row">';
        echo '        <div class="cute-3-tablet">';
        echo '            <img src="'.$instance['image3'].'" alt="{{data.altimage2}}" class="responsive-img"/>';
        echo '        </div>';
        echo '        <div class="cute-9-tablet" style="padding-left:0px;">';
        echo '            <p class="nomargin">';
        echo '                '.$instance['text3'];
        echo '            </p>';
        echo '        </div>';
        echo '    </div>';
        echo '</div>';

    }
}



?>