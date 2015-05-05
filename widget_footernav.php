<?php
/*
Plugin Name: Next Foover Nav
Description: 
Author: undefined development
Version: 1
Author URI: http://undev.de/
*/


class next_footernav extends WP_Widget
{

    function next_footernav()
    {
        $widget_ops = array('classname' => 'next_footernav', 'title' => '' );
        $this->WP_Widget('next_footernav', 'Next Footer Nav', $widget_ops);
    }



function getWidgetInput($title,$id,$fieldname,$value)
{
    $str='<p>'.
            $title.': '.
            '<input class="widefat" type="text" '.
                'id="'.$id.'" '.
                'name="'.$fieldname.'" '.
                'value="'.$value.'" />'.
            '</label>'.
        '</p>';

    return $str;
}


    function getPageInput($title,$id,$fieldname,$value)
    {
        global $wpdb;

        $args=array();
        $pages=get_pages($args);

        $str='<p>'.
        $title.': ';

        $str.='<select name="'.$fieldname.'">';

        foreach ($pages as &$page)
        {
            $sel="";
            if($page->ID==$value)$sel=' selected="selected" ';

            $str.='<option value="'.$page->ID.'" '.$sel.'>'.$page->post_title.'</option>';
        }

        $str.='</select>';
        $str.='</p>';
        return $str;
    }


    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '','pageid' => '' ) );

        echo $this->getPageInput(
            'pageid',
            $this->get_field_id('pageid'),
            $this->get_field_name('pageid'),
            $instance['pageid']);

        echo $this->getWidgetInput(
            'title',
            $this->get_field_id('title'),
            $this->get_field_name('title'),
            $instance['title']);


    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['pageid'] = $new_instance['pageid'];
        return $instance;
    }


    function getDepth()
    {
        global $wp_query;
        $object = $wp_query->get_queried_object();
        $parent_id  = $object->post_parent;
        $depth = 0;
        while ($parent_id > 0) {
            $page = get_page($parent_id);
            $parent_id = $page->post_parent;
            $depth++;
        }
        return $depth;
    }

    function widget($args, $instance)
    {
        global $post;

        $pageid=$instance['pageid'];

        // $pg=get_page ( $pageid ); 


        $depth=$this->getDepth();
        $title="NEXT POOL";

        $children = wp_list_pages('title_li=&child_of='.$pageid.'&echo=0&depth=1');
        
        echo '<div class="footernav hide-phone hide-tablet">';
        echo '<b>';
        echo '<a href="'.get_permalink($pageid).'">';
        echo $instance['title'];
        echo '</a>';
        echo '</b>';

        if(sizeof($children)>0)
        {
            print($children);
        }
        echo '<div class="clear"></div>';
        echo '</div>';
    }
}



?>