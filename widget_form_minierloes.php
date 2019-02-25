<?php

require_once('widget_.php');

class nextFormMiniErloesWidget extends unWidget
{
    function nextFormMiniErloesWidget()
    {
        $widget_ops = array('classname' => 'nextFormMiniErloesWidget', 'description' => '' );
        $this->WP_Widget('nextFormMiniErloesWidget', 'Next Form MiniErloes', $widget_ops);
    }

    function getWidgetMiniErloesType($title,$id,$fieldname,$value)
    {
        $html='<p>'.$title.': ';

        $icons = array();

        $icons[] = array("Biogas","Biogas");
        $icons[] = array("Solar","Solar");
        $icons[] = array("Wind","Wind");
        $icons[] = array("Wasserkraft","Wasserkraft");
        $icons[] = array("BHKW_KWK","BHKW_KWK");
        $icons[] = array("Kraftwerke","BHKW_KWK");
        
        $icons[] = array("Notstrom","Notstrom");
        $icons[] = array("Verbraucher","Verbraucher");
        $icons[] = array("Stromhandelsdienstleistungen","Stromhandelsdienstleistungen");

        $html.='<select name="'.$fieldname.'" id="'.$id.'" >';

        foreach ($icons as $icon)
        {
            $sel="";
            if($icon[1]==$value)$sel=' selected="SELECTED" ';
            $html.='<option value="'.$icon[1].'" '.$sel.'>'.$icon[0].'</option>';
        }
        $html.='</select>';
        $html.='</p>';
        return $html;
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '',
            'displaysize' => '',
            'text' => ''
            ));

        echo $this->getWidgetInput(
            'title',
            $this->get_field_id('title'),
            $this->get_field_name('title'),
            $instance['title']);

        echo $this->getWidgetInputArea(
            'Text',
            $this->get_field_id('text'),
            $this->get_field_name('text'),
            $instance['text']);

        echo $this->getWidgetMiniErloesType(
            'Type',
            $this->get_field_id('type'),
            $this->get_field_name('type'),
            $instance['type']);






        echo $this->getWidgetInputArea(
            'Text Bottom',
            $this->get_field_id('textBottom'),
            $this->get_field_name('textBottom'),
            $instance['textBottom']);

        echo $this->getWidgetInputArea(
            'Text Success',
            $this->get_field_id('textSuccess'),
            $this->get_field_name('textSuccess'),
            $instance['textSuccess']);

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
        $instance['text'] = $new_instance['text'];
        $instance['textBottom'] = $new_instance['textBottom'];
        $instance['textSuccess'] = $new_instance['textSuccess'];
        $instance['type'] = $new_instance['type'];
        $instance['displaysize'] = $new_instance['displaysize'];
        return $instance;
    }

    function widget($args, $instance)
    {
        global $post;

        $classes='';
        if($instance['displaysize']=='allbutphones') $classes='hide-phone';


        $twig=initTwig();
        $template = $twig->loadTemplate('widget_form_minierloes.html');
        $html.=$template->render(array(
            'data' => $instance,
            'wp' => $wp,
            'displayClasses' => $classes
        ));
        
        echo $html;
    }
}


?>