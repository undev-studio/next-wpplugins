<?php
/*
Plugin Name: Next Forms
Description: 
Author: undefined development
Version: 1
Author URI: http://undev.de/
*/

require_once('widget_.php');

class nextFormsWidget extends unWidget
{
    function nextFormsWidget()
    {
        $widget_ops = array('classname' => 'nextFormsWidget', 'description' => '' );
        $this->WP_Widget('nextFormsWidget', 'Next Forms', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '',
            'displaysize' => '',
            'text' => ''
            ));

        $html='<p>Form: ';

        global $wpdb;
        $rows = $wpdb->get_results("SELECT * FROM next_forms ORDER BY title" );

        var_dump($instance);


        $html.='<select name="'.$this->get_field_name('form').'" id="'.$this->get_field_id('form').'" >';
        $html.='<option value=""> - </option>';

        foreach ($rows as $f)
        {
            $sel="";
            if($f->id==$instance['form'] )$sel=' selected="SELECTED" ';
            $html.='<option value="'.$f->id.'" '.$sel.'>'.$f->title.'</option>';
        }

        $html.='</select>';
        $html.='</p>';
        echo $html;


        // echo $this->getWidgetInput(
        //     'HALLO',
        //     $this->get_field_id('title'),
        //     $this->get_field_name('title'),
        //     $instance['title']);

 
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['form'] = $new_instance['form'];
        return $instance;
    }

    function widget($args, $instance)
    {
        global $post;

        $classes='';
        if($instance['displaysize']=='allbutphones') $classes='hide-phone';

        $twig=initTwig();
        $html='';
        global $wpdb;

        $form = $wpdb->get_results('SELECT * FROM next_forms WHERE id='.(int)$instance['form'].';');


        $json= $form[0]->rowdata;
        $id=uniqid();

        // var_dump($form);
        echo '<div class="bgbox {{classes}}">';
        echo '<h3 class="clickable accordion-header closed" style="margin:0px;padding:0px;" data-amount="1">'.$form[0]->title.'</h3>';
        echo '<div class="erloescontainer forms" style="border:none;">';
        echo '<div id="formerrors'.$form[0]->id.'" class="errors"></div>';
        echo '<table id="formwidget'.$id.'"></table>';
        echo '</div>';
        echo '<script>';
        echo '  var nextFormData=nextFormData||[];';
        echo '  var data='.$json.';';
        echo '  data.htmlId="#formwidget'.$id.'";';
        echo '  data.formId='.$form[0]->id.';';
        echo '  data.errorId="#formerrors'.$form[0]->id.'";';
        echo '  nextFormData.push(data);';
        echo '</script>';
      
        echo $html;
        echo '</div>';
    }
}


?>