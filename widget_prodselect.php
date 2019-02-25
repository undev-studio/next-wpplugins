<?php

// function upload_scripts()
// {
//     wp_enqueue_media();
// }

require_once('twig.php');

add_action('admin_enqueue_scripts', 'upload_scripts');
// add_action('admin_enqueue_styles', array($this, 'upload_styles'));


class nextProdSelectWidget extends unWidget
{

    function nextProdSelectWidget()
    {
        $widget_ops = array('classname' => 'nextProdSelectWidget', 'description' => '' );
        $this->WP_Widget('nextProdSelectWidget', 'Next Product Select', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '',
            'title' => ''
            ) );

        echo $this->getWidgetInput(
            'title',
            $this->get_field_id('title'),
            $this->get_field_name('title'),
            $instance['title']);

        echo $this->getWidgetInput(
            'title1',
            $this->get_field_id('title1'),
            $this->get_field_name('title1'),
            $instance['title1']);

        echo $this->getWidgetInput(
            'title2',
            $this->get_field_id('title2'),
            $this->get_field_name('title2'),
            $instance['title2']);

        echo $this->getWidgetInput(
            'title3',
            $this->get_field_id('title3'),
            $this->get_field_name('title3'),
            $instance['title3']);

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
        $instance['title1'] = $new_instance['title1'];
        $instance['title2'] = $new_instance['title2'];
        $instance['title3'] = $new_instance['title3'];
        $instance['displaysize'] = $new_instance['displaysize'];
        return $instance;
    }

    function widget($args, $instance)
    {
        global $post;
        global $wpdb;
        // $sql='SELECT * FROM next_news ORDER BY newsdate DESC LIMIT 0,3';
        // $news=$wpdb->get_results( $sql );

        // if($wpdb->last_error!='') echo($wpdb->last_error); 

        $data=array();
        // $data['news']=$news;
        $data['text']=$instance['text'];
        $data['title']=$instance['title'];
        $data['title1']=$instance['title1'];
        $data['title2']=$instance['title2'];
        $data['title3']=$instance['title3'];

        // foreach( $news as $event)
        // {
        //     $ds = strtotime( $event->newsdate );
        //     $event->date_readable=date( 'd.m.Y', $ds );

        //     $event->text=strip_tags($event->text);
        //     // if(strlen($event->text)>180) $event->text=substr($event->text,0,180)."...";
        // }
       
        $twig=initTwig();
        $template = $twig->loadTemplate('widget_prodselect.html');
        $html.=$template->render(array(
            'data' => $data,
            'classes' => $this->getDisplaySizeClasses($instance),
            'wp' => $wp
        ));
        
        echo $html;


    }
}



?>