<?php
/*
Plugin Name: Next Widget
Description: 
Author: undefined development
Version: 1
Author URI: http://undev.de/
*/

require_once('twig.php');
require_once('widget_.php');

add_action('admin_enqueue_scripts', 'upload_scripts');



class nextWidget extends unWidget
{

    function nextWidget()
    {
        $widget_ops = array('classname' => 'netWidget', 'description' => '' );
        $this->WP_Widget('netWidget', 'Next Widgets', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 
            'module' => '',
            'displaysize' => ''
            ));


        global $wpdb;
        $sql='SELECT * FROM next_widgets ORDER BY title ';
        $jobs=$wpdb->get_results( $sql );

        if($wpdb->last_error!='') printError($wpdb->last_error);

        $widgetTitle='Next Widget';

        echo '<select name="'.$this->get_field_name('module').'" style="max-width: 390px;">';
        foreach( $jobs as $job)
        {
            $sel='';
            if($job->id==$instance['module'])
            {
                $widgetTitle='Next Widget: '.$job->title;
                $sel="SELECTED";
            }

            echo '<option '.$sel.' value="'.$job->id.'">'.$job->title.'</option>';
        }
        
        echo '</select>';

        echo $this->getWidgetInputDisplaySize(
            'Devices',
            $this->get_field_id('displaysize'),
            $this->get_field_name('displaysize'),
            $instance['displaysize']);

        // echo '!!! '.$this->get_field_id('displaysize');
        // echo '<script>console.log( jQuery(\'#'.$this->get_field_id('displaysize').'\').parent().parent().parent().parent().parent().find("h3").html() );</script>';
        echo '<script>jQuery(\'#'.$this->get_field_id('displaysize').'\').parent().parent().parent().parent().parent().find("h3").html(\''.$widgetTitle.'\'); </script>';

        // $this->name="test 1234";
        // $instance['title']='test 1234';

    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['module'] = $new_instance['module'];
        $instance['displaysize'] = $new_instance['displaysize'];
        return $instance;
    }

    function widget($args, $instance)
    {
        global $post;
        global $wpdb;
        $sql='SELECT * FROM next_widgets WHERE id='.$instance['module'];
        $entry=$wpdb->get_results( $sql );
        $entry=$entry[0];



        // $data=array();

        // foreach($entry as $key => $p )
        // {

        //     $newKey=str_replace( 'widget_' , '' , $key );
        //     $data[$newKey]=$p;
        //     // $recent[$key]['permalink']=get_permalink($p['ID']);

        //     // $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($p['ID']) , "thumbnail" );
        //     // if($thumb[0])$recent[$key]['thumb']=$thumb[0];
        // }

        // $data['title']=$instance['title'];
        // $data['recent']=$recent;


        global $twig;
        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem(__DIR__.'/templates_widgets/');
        $twig = new Twig_Environment($loader, array());

        $tmplFile=str_replace( '.json' , '.html' , $entry->rowfile );

        if(isset($tmplFile) && $tmplFile!='')
        {
            // echo $entry->rowdata;
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