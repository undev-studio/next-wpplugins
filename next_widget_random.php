<?php
/*
Plugin Name: Next Widgets Random Widget
Description: 
Author: undefined development
Version: 1
Author URI: http://undev.de/
*/

require_once('twig.php');
require_once('widget_.php');

add_action('admin_enqueue_scripts', 'upload_scripts');


class nextWidgetRandom extends unWidget
{

    function nextWidgetRandom()
    {
        $widget_ops = array('classname' => 'nextWidgetRandom', 'description' => '' );
        $this->WP_Widget('nextWidgetRandom', 'Next Widgets Random', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 
            'widgetString' => '',
            'displaysize' => ''
            ));

        global $wpdb;
        $sql='SELECT * FROM next_widgets WHERE title LIKE "%'.$instance['widgetString'].'%" ';
        $jobs=$wpdb->get_results( $sql );

        if($wpdb->last_error!='') printError($wpdb->last_error);

        $widgetTitle='Next Widget';

        echo 'All Widgets that contain the following String will be shown randomized.<br/>';
        echo '('.sizeOf($jobs).' Entries)<br/>';
        echo '<input name="'.$this->get_field_name('widgetString').'" value="'.$instance['widgetString'].'"/>';

        echo $this->getWidgetInputDisplaySize(
            'Devices',
            $this->get_field_id('displaysize'),
            $this->get_field_name('displaysize'),
            $instance['displaysize']);
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['widgetString'] = $new_instance['widgetString'];
        $instance['displaysize'] = $new_instance['displaysize'];
        return $instance;
    }

    function widget($args, $instance)
    {
        global $post;
        global $wpdb;
        $sql='SELECT * FROM next_widgets WHERE title LIKE "%'.$instance['widgetString'].'%" ORDER BY RAND()';
        $entry=$wpdb->get_results( $sql );
        $entry=$entry[0];

        global $twig;
        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem(__DIR__.'/templates_widgets/');
        $twig = new Twig_Environment($loader, array());

        $tmplFile=str_replace( '.json' , '.html' , $entry->rowfile );

        if(isset($tmplFile) && $tmplFile!='')
        {
            $data=json_decode($entry->rowdata,true);

            $template = $twig->loadTemplate($tmplFile);
            $html.=$template->render(array(
                'data' => $data,
                'classes' => $this->getDisplaySizeClasses($instance)
            ));

            echo $html;
        }
        else
        {
            // echo 'err';
        }


    }
}



?>