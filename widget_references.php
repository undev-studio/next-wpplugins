<?php
/*
Plugin Name: Next Referenzen2
Description: 
Author: undefined development
Version: 1
Author URI: http://undev.de/
*/
 
require_once(__DIR__.'/../../themes/next2015/util/random_reference.php');

class nextReferenzWidget extends WP_Widget
{
  function nextReferenzWidget()
  {
    $widget_ops = array('classname' => 'nextReferenzWidget', 'description' => '' );
    $this->WP_Widget('nextReferenzWidget', 'Next Referenzen', $widget_ops);
  }
 
  function form($instance)
  {

  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    return $instance;
  }

  function widget($args, $instance)
  {
      $ref=getRandomReference();
      print('<li class="widget reference">');
      print('  <div class="referenzcontainer">');
      print('  <span class="h2">'.$ref->title.'</span>');

      print('    <a class="" href="'.$ref->permalink.'" title="'.$ref->longtitle.'">');
      print('<img src="'.$ref->image.'" class="responsive-img"/>');
      print('</a>');
      // print('<br/>'.$ref->longtitle);
      // print('    <br/><a class="readmore" href="'.$ref->permalink.'" title="'.$ref->longtitle.'">'.$ref->title.'</a>');
      print('  </div>');
      print('</li>');
  }
 
}

add_action( 'widgets_init', create_function('', 'return register_widget("nextReferenzWidget");') );


?>
