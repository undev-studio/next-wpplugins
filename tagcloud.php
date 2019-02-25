<?php
 
/*
Plugin Name: Tagcloud 2017
Description: 
Author: undefined development
Version: 1
Author URI: http://undev.de/
*/

require_once('settings_next.php');

class TagCloudWidgetNew extends WP_Widget
{
  function TagCloudWidgetNew()
  {
    $widget_ops = array('classname' => 'EnergieBlogWidget', 'description' => '' );
    $this->WP_Widget('EnergieBlogWidget', 'Next Tagcloud', $widget_ops);
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

    print('<div class="widget widget_text"><h2 class="widgettitle">'.$instance['title'].'</h2>');

//print('<h2 class="widgettitle">'.$instance['title'].'</h2>');


    $cfg=nextSettings::load();



    $custom_query = new WP_Query('posts_per_page=-1&cat='.$cfg[nextSettings::POST_CAT_BLOG]);
    if ($custom_query->have_posts()) :
      while ($custom_query->have_posts()) : $custom_query->the_post();


        $posttags = get_the_tags();
        if ($posttags) {
          foreach($posttags as $tag) {
            $all_tags[] = $tag->term_id;
          }
        }
      endwhile;
    endif;




    $tags_arr = array_unique($all_tags);
    $tags_str = implode(",", $tags_arr);

    $args = array(
    'smallest'  => 12,
    'largest'   => 20,
    'unit'      => 'px',
    'number'    => 0,
    'format'    => 'array',
    'include'   => $tags_str
    );

    print('<div class="tagcloud">');
    $tags=wp_tag_cloud($args);
    foreach($tags as $t) 
    {
      $t = str_replace("' class", "?cat=energie-blog' class", $t);
      print($t." ");
    }


    print('</div></div>');

  }
 
}
