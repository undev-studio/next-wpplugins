<?php

require_once(__DIR__.'/../theme/util/random_reference.php');

class nextReferenzWidget extends WP_Widget
{
  function nextReferenzWidget()
  {
    $widget_ops = array('classname' => 'nextReferenzWidget', 'reference' => '' );
    $this->WP_Widget('nextReferenzWidget', 'Next Referenzen', $widget_ops);
  }


    function getDropdown($title,$id,$fieldname,$value)
    {
        $str='<p>'.
                $title.': '.
                '<select class="widefat" '.
                    'id="'.$id.'" '.
                    'name="'.$fieldname.'" '.
                    '>';

        $sel='';
        if($value=='')$sel=' SELECTED ';

        $str.='<option '.$sel.' value="">Random Reference</option>';

        global $wpdb;

        $sql='SELECT * FROM next_references';
        $refs = $wpdb->get_results($sql);

        foreach ($refs as &$ref)
        {
            $sel='';
            if($value==$ref->id)$sel=' SELECTED ';

            $str.='<option '.$sel.' value="'.$ref->id.'">'.$ref->title.'</option>';
        }


        $str.='</select>'.
            '</p>';

        return $str;
    }
 
  function form($instance)
  {
        echo $this->getDropdown(
            'Reference',
            $this->get_field_id('reference'),
            $this->get_field_name('reference'),
            $instance['reference']);
  }

  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['reference'] = $new_instance['reference'];

    return $instance;
  }

  function widget($args, $instance)
  {
      if($instance['reference']=='')
      {
          $ref=getRandomReference();
      }
      else 
      {
          global $wpdb;

          $sql='SELECT * FROM next_references WHERE id='.$instance['reference'];
          $ref = $wpdb->get_results($sql)[0];

          $ref->permalink = get_permalink($ref->page);
      }

      print('<li class="widget reference">');
      print('  <div class="referenzcontainer">');
      print('    <span class="h3">'.$ref->title.'</span>');
      print('      <a class="" href="'.$ref->permalink.'" title="'.$ref->longtitle.'">');
      print('        <img src="'.$ref->image.'" class="responsive-img"/>');
      print('      </a>');
      print('  </div>');
      print('</li>');
  }
 
}

add_action( 'widgets_init', create_function('', 'return register_widget("nextReferenzWidget");') );


?>
