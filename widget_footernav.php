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
        $str.='<option value="" '.$sel.'>-- None</option>';

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
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '','pageid' => '',
            'title2' => '','pageid2' => '',
            'title3' => '','pageid3' => '' ) );

        echo $this->getPageInput(
            'Page',
            $this->get_field_id('pageid'),
            $this->get_field_name('pageid'),
            $instance['pageid']);

        echo $this->getWidgetInput(
            'Title',
            $this->get_field_id('title'),
            $this->get_field_name('title'),
            $instance['title']);


        echo $this->getPageInput(
            'Page 2',
            $this->get_field_id('pageid1'),
            $this->get_field_name('pageid1'),
            $instance['pageid1']);

        echo $this->getWidgetInput(
            'Title 1',
            $this->get_field_id('title1'),
            $this->get_field_name('title1'),
            $instance['title1']);


        echo $this->getPageInput(
            'Page 3',
            $this->get_field_id('pageid2'),
            $this->get_field_name('pageid2'),
            $instance['pageid2']);

        echo $this->getWidgetInput(
            'Title 2',
            $this->get_field_id('title2'),
            $this->get_field_name('title2'),
            $instance['title2']);


    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['pageid'] = $new_instance['pageid'];

        $instance['title1'] = $new_instance['title1'];
        $instance['pageid1'] = $new_instance['pageid1'];

        $instance['title2'] = $new_instance['title2'];
        $instance['pageid2'] = $new_instance['pageid2'];
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


    function printNav($pageid,$title,$children)
    {
        echo '<b>';
        echo '<a href="'.get_permalink($pageid).'">';
        echo $title;
        echo '</a>';
        echo '</b>';

        if(sizeof($children)>0)
        {
            print($children);
        }
        // echo '<div class="clear"></div>';
        echo '<br/>';
    }


    function widget($args, $instance)
    {
        global $post;

        echo '<div class="footernav hide-phone hide-tablet">';

        $children = wp_list_pages('title_li=&child_of='.$instance['pageid'].'&echo=0&depth=1');
        $this->printNav($instance['pageid'],$instance['title'],$children);

        if($instance['pageid1']!='')
        {
            $children = wp_list_pages('title_li=&child_of='.$instance['pageid1'].'&echo=0&depth=1');
            $this->printNav($instance['pageid1'],$instance['title1'],$children);
        }

        if($instance['pageid2']!='')
        {
            $children = wp_list_pages('title_li=&child_of='.$instance['pageid2'].'&echo=0&depth=1');
            $this->printNav($instance['pageid2'],$instance['title2'],$children);
        }

        echo '</div>';        
    }
}



?>