<?php
 
/*
Plugin Name: Tagcloud 2017
Description: 
Author: undefined development
Version: 1
Author URI: http://undev.de/
*/

require_once('settings_next.php');

class PostTagsWidget extends WP_Widget
{
  function PostTagsWidget()
  {
    $widget_ops = array('classname' => 'EnergieBlogWidget', 'description' => '' );
    $this->WP_Widget('EnergieBlogWidget', 'Next Post Tags', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {

    $posttags = get_the_tags();
    if ($posttags) {
      print('<div class="widget widget_text"><h2 class="widgettitle">'.$instance['title'].'</h2>');
      print('<ul>');

      foreach($posttags as $tag) {
        print('<li class="bullet">')     ;
        print('<a href="{{ data[ ("url" ~ i)] }}">HUND</a>');
        print('</li>');

        $all_tags[] = $tag->term_id;
      }
      print('</ul>');
    }

    print('</div>');

  }
 
}
