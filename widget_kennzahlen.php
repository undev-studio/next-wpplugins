<?php
/*
Plugin Name: Next Image Teaser
Description: 
Author: undefined development
Version: 1
Author URI: http://undev.de/
*/



require_once('widget_.php');



class nextKennzahlenWidget extends unWidget
{
    const ROWS = 10;

    function nextKennzahlenWidget()
    {
        $widget_ops = array('classname' => 'nextKennzahlenWidget', 'description' => '' );
        $this->WP_Widget('nextKennzahlenWidget', 'Next Kennzahlen', $widget_ops);
    }


    function form($instance)
    {

        $instance = wp_parse_args( (array) $instance);
     
        echo $this->getWidgetInput(
            'title',
            $this->get_field_id('title'),
            $this->get_field_name('title'),
            $instance['title']);

        echo '<hr/>';

        for ($i = 1; $i <= self::ROWS; $i++) {
            
            $rnd=rand();
            echo '<a onclick="jQuery(\'#kennzahleniconrow'.$i.$rnd.'\').slideToggle();" style="cursor:pointer">';
            echo 'Icon Reihe '.$i;
            echo '</a>';
            echo '<div id="kennzahleniconrow'.$i.$rnd.'" style="display:none;">';

            echo $this->getWidgetInput(
                'icon'. $i,
                $this->get_field_id('icon'. $i),
                $this->get_field_name('icon'. $i),
                $instance['icon'. $i]);

            echo $this->getWidgetInput(
                'title'. $i,
                $this->get_field_id('title'. $i),
                $this->get_field_name('title'. $i),
                $instance['title'. $i]);

            echo $this->getWidgetInput(
                'value'. $i,
                $this->get_field_id('value'. $i),
                $this->get_field_name('value'. $i),
                $instance['value'. $i]);

            echo $this->getWidgetInput(
                'url'. $i,
                $this->get_field_id('url'. $i),
                $this->get_field_name('url'. $i),
                $instance['url'. $i]);

            echo $this->getWidgetInput(
                'seo_linktitle'. $i,
                $this->get_field_id('seo_linktitle'. $i),
                $this->get_field_name('seo_linktitle'. $i),
                $instance['seo_linktitle'. $i]);

            echo '</div>';
            echo '<hr/>';
        }

        echo $this->getWidgetInput(
            'titlea',
            $this->get_field_id('titlea'),
            $this->get_field_name('titlea'),
            $instance['titlea']);

        echo $this->getWidgetInput(
            'valuea',
            $this->get_field_id('valuea'),
            $this->get_field_name('valuea'),
            $instance['valuea']);

        echo '<hr/>';

        echo $this->getWidgetInput(
            'titleb',
            $this->get_field_id('titleb'),
            $this->get_field_name('titleb'),
            $instance['titleb']);

        echo $this->getWidgetInput(
            'valueb',
            $this->get_field_id('valueb'),
            $this->get_field_name('valueb'),
            $instance['valueb']);
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
        $template = $twig->loadTemplate('widget_kennzahlen.html');
        $html .= $template->render(array(
            'data' => $instance,
            'classes' => $this->getDisplaySizeClasses($instance),
            'ROWS' => self::ROWS
        ));

        echo $html;
    }
}
?>