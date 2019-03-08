<?php

// function upload_scripts()
// {
//     wp_enqueue_media();
// }

require_once('twig.php');

add_action('admin_enqueue_scripts', 'upload_scripts');

// add_action('admin_enqueue_styles', array($this, 'upload_styles'));


class nextNewsWidget extends unWidget
{

  function nextNewsWidget()
  {
    $widget_ops = array('classname' => 'nextNewsWidget', 'description' => '');
    $this->WP_Widget('nextNewsWidget', 'Next News', $widget_ops);
  }

  function form($instance)
  {
    $instance = wp_parse_args((array)$instance, array(
      'title' => '',
      'title' => ''
    ));

    echo $this->getWidgetInput(
      'title',
      $this->get_field_id('title'),
      $this->get_field_name('title'),
      $instance['title']);


    echo $this->getWidgetInput(
      'button_title',
      $this->get_field_id('button_title'),
      $this->get_field_name('button_title'),
      $instance['button_title']);

    echo $this->getWidgetInput(
      'button_link',
      $this->get_field_id('button_link'),
      $this->get_field_name('button_link'),
      $instance['button_link']);

    echo $this->getWidgetInputDisplaySize(
      'Geräte',
      $this->get_field_id('displaysize'),
      $this->get_field_name('displaysize'),
      $instance['displaysize']);
  }

  function update($new_instance, $old_instance)
  {
    return $new_instance;
    // $instance = $new_instance;
    // $instance['title'] = $new_instance['title'];
    // $instance['displaysize'] = $new_instance['displaysize'];
    // return $instance;
  }

  function widget($args, $instance)
  {
    global $post;
    global $wpdb;

    $lang = '';

    if (function_exists('pll_the_languages')) $lang = 'WHERE language="' . pll_current_language() . '" ';

    $sql = 'SELECT * FROM next_news  ' . $lang . ' ORDER BY newsdate DESC LIMIT 0,3';
    // echo $sql;
    $news = $wpdb->get_results($sql);

    if ($wpdb->last_error != '') echo($wpdb->last_error);

    $data = array();
    $data['news'] = $news;
    $data['text'] = $instance['text'];
    $data['title'] = $instance['title'];
    $data['button_title'] = $instance['button_title'];
    $data['button_link'] = $instance['button_link'];

    foreach ($news as $event) {
      $ds = strtotime($event->newsdate);
      $event->date_readable = date('d.m.Y', $ds);

      $event->text = strip_tags($event->text);
      // if(strlen($event->text)>180) $event->text=substr($event->text,0,180)."...";
    }

    $twig = initTwig();
    $template = $twig->loadTemplate('widget_news.html');
    $html .= $template->render(array(
      'data' => $data,
      'classes' => $this->getDisplaySizeClasses($instance),
      'wp' => $wp
    ));

    echo $html;


  }
}


?>