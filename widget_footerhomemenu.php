<?php


class nextFooterHomeMenu extends WP_Widget
{

    function nextFooterHomeMenu()
    {
        $widget_ops = array('classname' => 'nextFooterHomeMenu', 'title' => '' );
        $this->WP_Widget('nextFooterHomeMenu', 'Next Footer Home Menu', $widget_ops);
    }


    private function getMenuItems($menu_name)
    {
        $menu_items=array();

        if (( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ])) 
        {
            $menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
            $menu_items = wp_get_nav_menu_items($menu->term_id);
        }

        return $menu_items;
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '','pageid' => '' ) );
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['pageid'] = $new_instance['pageid'];
        return $instance;
    }



    function widget($args, $instance)
    {
        global $post;


        $pageid=$instance['pageid'];

        $pages=$this->getMenuItems('footer=home-menu');


        
        echo '<div class="footernav hide-phone hide-tablet">';
        echo '<b>';
        echo '<a href="'.get_permalink($pageid).'">';
        echo $instance['title'];
        echo '</a>';
        echo '</b>';

        $count=0;
        foreach ($pages as $key => $page) 
        {
            if($count==0)
                echo '<b><a href="'.$page->url.'" target="'.$page->target.'">'.$page->title.'</a></b>';
                else
                echo '<li><a href="'.$page->url.'" target="'.$page->target.'">'.$page->title.'</a></li>';
            $count++;
        }
        echo '<div class="clear"></div>';
        echo '</div>';
    }
}



?>