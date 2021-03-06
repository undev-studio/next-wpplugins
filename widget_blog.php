<?php
/*
Plugin Name: Next Blog
Description: 
Author: undefined development
Version: 1
Author URI: http://undev.de/
*/

// function upload_scripts()
// {
//     wp_enqueue_media();
// }

require_once('twig.php');
require_once('widget_.php');
require_once('settings_next.php');

add_action('admin_enqueue_scripts', 'upload_scripts');

class nextBlogWidget extends unWidget
{

    function nextBlogWidget()
    {
        $widget_ops = array('classname' => 'nextBlogWidget', 'description' => '' );
        $this->WP_Widget('nextBlogWidget', 'Next Blog', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '',
            'displaysize' => ''
            ));

        echo $this->getWidgetInput(
            'title',
            $this->get_field_id('title'),
            $this->get_field_name('title'),
            $instance['title']);

        echo $this->getWidgetInputDisplaySize(
            'Devices',
            $this->get_field_id('displaysize'),
            $this->get_field_name('displaysize'),
            $instance['displaysize']);

        echo $this->getWidgetInput(
            'Link Title',
            $this->get_field_id('linktitle'),
            $this->get_field_name('linktitle'),
            $instance['linktitle']);

        echo $this->getWidgetInput(
            'Link URL',
            $this->get_field_id('linkurl'),
            $this->get_field_name('linkurl'),
            $instance['linkurl']);

    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['linktitle'] = $new_instance['linktitle'];
        $instance['linkurl'] = $new_instance['linkurl'];
        return $instance;
    }

    function widget($args, $instance)
    {
        global $post;
        global $wpdb;

        $instance['numberposts']=3;

        $cfg=nextSettings::load();

        $args=array(
                'numberposts' => $instance['numberposts'],
                'category_name' => $cfg[nextSettings::POST_CAT_BLOG],
                'post_status' => 'publish'
        );

        $recent= wp_get_recent_posts($args);
        $data=array();

        foreach($recent as $key => $p )
        {
            $recent[$key]['permalink']=get_permalink($p['ID']);

            $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($p['ID']) , "thumbnail" );
            if($thumb[0])$recent[$key]['thumb']=$thumb[0];
        }

        $data['title']=$instance['title'];
        $data['recent']=$recent;
        $data['linktitle']=$instance['linktitle'];
        $data['linkurl']=$instance['linkurl'];

        $twig=initTwig();
        $template = $twig->loadTemplate('widget_blog.html');
        $html.=$template->render(array(
            'data' => $data,
            'classes' => $this->getDisplaySizeClasses($instance)
        ));
        
        echo $html;


    }
}



?>