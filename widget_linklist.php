<?php

require_once('widget_.php');



class nextLinkListWidget extends unWidget
{
    const ROWS = 6;

    function nextLinkListWidget()
    {
        $widget_ops = array('classname' => 'nextLinkListWidget', 'description' => '' );
        $this->WP_Widget('nextLinkListWidget', 'Next LinkList', $widget_ops);
    }


    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance);
     
        echo $this->getWidgetInput(
            'Title',
            $this->get_field_id('title'),
            $this->get_field_name('title'),
            $instance['title']);

        echo $this->getWidgetInput(
            'Text',
            $this->get_field_id('text'),
            $this->get_field_name('text'),
            $instance['text']);

        echo $this->getWidgetIconSelect(
            'Title Icon',
            $this->get_field_id('icon'),
            $this->get_field_name('icon'),
            $instance['icon']);

        echo '<hr/>';

        for ($i = 1; $i <= self::ROWS; $i++)
        {
            $rnd=rand();
            echo '<a onclick="jQuery(\'#LinkListiconrow'.$i.$rnd.'\').slideToggle();" style="cursor:pointer">';
            echo 'Link '.$i;
            echo '</a>';
            echo '<div id="LinkListiconrow'.$i.$rnd.'" style="display:none;">';

            echo $this->getWidgetIconSelect(
                'Icon '. $i,
                $this->get_field_id('icon'. $i),
                $this->get_field_name('icon'. $i),
                $instance['icon'. $i]);

            echo $this->getWidgetInput(
                'Title '. $i,
                $this->get_field_id('title'. $i),
                $this->get_field_name('title'. $i),
                $instance['title'. $i]);


            echo $this->getWidgetInput(
                'URL '. $i,
                $this->get_field_id('url'. $i),
                $this->get_field_name('url'. $i),
                $instance['url'. $i]);


            echo '</div>';
            echo '<hr/>';
        }


    }

    function update($new_instance, $old_instance)
    {
        //$instance = $old_instance;
        $instance = $new_instance;

        return $instance;
    }

    function widget($args, $instance)
    {
        global $post;
        $instance['classes']=$this->getDisplaySizeClasses($instance);

        $twig = initTwig();
        $template = $twig->loadTemplate('widget_linklist.html');
        $html .= $template->render(array(
            'data' => $instance,
            'classes' => $this->getDisplaySizeClasses($instance),
            'ROWS' => self::ROWS
        ));

        echo $html;
    }
}
?>