<?php

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
      print('<div class="widget"><h2 class="widgettitle">'.$instance['title'].'</h2>');
      print('<ul>');

      foreach($posttags as $tag) {
        print('<li>')     ;
        print('<a class="readmore" href="/tag/'.$tag->slug.'">'.$tag->name.'</a>');
        print('</li>');

        $all_tags[] = $tag->term_id;
      }
      print('</ul>');
    }

    print('</div>');

  }
 
}
