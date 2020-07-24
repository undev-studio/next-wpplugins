<?php

require_once('widget_.php');

add_action('admin_enqueue_scripts', 'wp_enqueue_media');

class next_youtubewidget extends unWidget
{
  function next_youtubewidget()
  {
    $widget_ops = array('classname' => 'next_youtubewidget', 'description' => '');
    $this->WP_Widget('next_youtubewidget', 'Next Youtube Widget', $widget_ops);
  }

  function getMediaInput($title, $id, $fieldname, $value)
  {
    global $wpdb;

    $html = '';
    $html .= '<div>';
    $html .= '<div class="wp-menu-image dashicons-before dashicons-admin-media" style="float:left;padding-right:20px;" onclick="selectImageWidget(\'#' . $fieldName . '\')"><br></div>';
    $html .= '<input type="text" name="' . $fieldname . '" class="formimageinput" id="' . $fieldname . '" value="' . $value . '" class="unrow" style="float:left;width:80%;"/>';

    // $html.='<a onclick="selectImage(\'#'.$fieldname.'\')">select Image</a>';
    $html .= '<div style="clear:both;"></div>';
    $html .= '</div>';

    $html .= '<br/><img src="' . $value . '" style="width:100px;border:5px solid white;" onclick="selectImageWidget(\'#' . $fieldname . '\')"/>';


    return $html;
  }

  function form($instance)
  {
    echo '<script>';
    echo 'function selectImageWidget(id)';
    echo '{';
    echo '    custom_uploader = wp.media.frames.file_frame = wp.media({';
    echo '        title: "Choose Image",';
    echo '        button: {';
    echo '            text: "Choose Image"';
    echo '        },';
    echo '        multiple: false';
    echo '    });';

    echo '    custom_uploader.on("select", function()';
    echo '    {';
    echo '        attachment = custom_uploader.state().get("selection").first().toJSON();';
    echo '        jQuery(".formimageinput").val(attachment.url);';
    echo '    });';

    echo '    custom_uploader.open();';
    echo '}';
    echo '</script>';

    $instance = wp_parse_args((array)$instance, array(
      'title' => '',
      'youtubeId' => '',
      'thumbnail' => ''
    ));

    echo $this->getWidgetInput(
      'title',
      $this->get_field_id('title'),
      $this->get_field_name('title'),
      $instance['title']);

    echo $this->getWidgetInput(
      'youtubeId',
      $this->get_field_id('youtubeId'),
      $this->get_field_name('youtubeId'),
      $instance['youtubeId']);

    echo $this->getWidgetMediaInput(
      'thumbnail',
      $this->get_field_id('thumbnail'),
      $this->get_field_name('thumbnail'),
      $instance['thumbnail']);
  }

  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    $instance['youtubeId'] = $new_instance['youtubeId'];
    $instance['thumbnail'] = $new_instance['thumbnail'];

    return $instance;
  }

  function widget($args, $instance)
  {
    global $post;

    $image = $instance['image'];

    //$instance['classes']=$this->getDisplaySizeClasses($instance);

    $twig = initTwig();
    $template = $twig->loadTemplate('widget_youtube.html');
    $html .= $template->render(array(
      'data' => $instance,
      'classes' => $this->getDisplaySizeClasses($instance)
    ));

    echo $html;
  }
}

?>