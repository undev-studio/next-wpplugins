<?php

//  error_reporting(E_ERROR|E_WARNING);
//  ini_set('display_errors', '1');

  global $nextLangAdmin;

  ?>
<div class="wrap">
<h2><? echo $nextLangAdmin['revenue_calculator']; ?></h2>

<?php

$user_reg="";


global $wpdb;
$wpdb->get_results("DELETE FROM erloes_plz WHERE start = 0 AND end = 0;");

class PlzRangedb
{ 
    public $pkey=-1;
    public $start = 0;
    public $end = 1000;
    public $email = "test@next-kraftwerke.de";
 

    public static function loadAll()
    {
      global $wpdb;
      $all = $wpdb->get_results('SELECT * FROM erloes_plz ORDER BY start;');
      return $all;
    }

    function delete()
    {
      global $wpdb;
      //$wpdb->show_errors();
      $wpdb->query('DELETE from erloes_plz WHERE pkey='.(int)$this->pkey.' ');
      //$wpdb->print_error(); 
    }

    function save()
    {
      global $wpdb;

      //print("dsdsds ___".$pkey);
      //$wpdb->show_errors();

      if($this->pkey==-1)
      {
        $users = $wpdb->query('INSERT INTO erloes_plz (start ,end ,email ) VALUES ( '.(int)$this->start.',  '.(int)$this->end.',  \''.$wpdb->escape($this->email).'\');');
      }
      else
      {
        $users = $wpdb->query('UPDATE erloes_plz SET start='.(int)$this->start.', end='.(int)$this->end.', email=\''.$wpdb->escape($this->email).'\' WHERE pkey='.(int)$this->pkey.';');
      }        

      $wpdb->print_error(); 
    }
} 


if($_REQUEST['delete']!='')
{
  $range = new PlzRangedb; 
  $range->pkey=(int)$_REQUEST['delete'];
  $range->delete();
}

if($_REQUEST['action']=='create' || $_REQUEST['action']=='update' )
{
  $range = new PlzRangedb; 
  if($_REQUEST['action']=='update' ) $range->pkey=(int)$_REQUEST['pkey'];
  $range->start=$_REQUEST['plz_start'];
  $range->end=$_REQUEST['plz_end'];
  $range->email=$_REQUEST['plz_email'];
  $range->save();
}


?>




<?php wp_nonce_field('update-options'); ?>


<?php
global $user_reg;
//global $user_details
//print_r($user_details);
//if($user_details)print('<div style="padding:5px;border:1px solid #aaa;">Benutzer existiert schon: '.$_REQUEST['login']."</div>");
if($user_reg!="")print('<div style="padding:5px;border:1px solid #aaa;">Entry was saved</div>');

?>
<br/>
<h2><?php echo $nextLangAdmin['postal_code_alloc']; ?></h2>


<table>
<tr>
  <td><?php echo $nextLangAdmin['zipStart']; ?></td>
  <td><?php echo $nextLangAdmin['zipEnd']; ?></td>
  <td>E-Mail:</td>
</tr>

<?php

  $allPLZ = PlzRangedb::loadAll();


  $sum=0;
  foreach ($allPLZ as &$plz)
  {  
    $sum+=($plz->end - $plz->start);
  }

  if($sum!=100000)
  {
    print('<div class="error" style="padding:10px;">Sum of all Postal Codes: '.$sum.' <br/><b>This should be 100000 !</b> </div>');
  }



  foreach ($allPLZ as &$plz)
  {
    print('<tr>');
    print('<form method="post" action=""><input type="hidden" name="action" value="update" />');
    print('<input type="hidden" name="pkey" value="'.$plz->pkey.'" />');
    print('<td><input type="text" value="'.$plz->start.'" name="plz_start" style="width:100px"/></td>');
    print('<td><input type="text" value="'.$plz->end.'" name="plz_end" style="width:100px"/></td>');
    print('<td><input type="text" value="'.$plz->email.'" name="plz_email" style="width:300px"/></td>');
    print('<td><input type="submit" class="button-primary" value="'.$nextLangAdmin['save'].'" /></td>');
    print('<td><a href="admin.php?page=next_content%2Ferloesrechner.php&delete='.$plz->pkey.'">'.$nextLangAdmin['delete'].'</a></td>');
    print('</form>');
    print('</tr>');
  }
  print('</table><br/><hr/><table>');
  print('<tr><td colspan="5"><br/><h2>'.$nextLangAdmin['delete'].'</h2></td></tr>');

  print('<form method="post" action=""><input type="hidden" name="action" value="create" />');
  print('<tr>');
  print('<td><input type="text" value="0" name="plz_start"/></td>');
  print('<td><input type="text" value="1000" name="plz_end"/></td>');
  print('<td><input type="text" value="beispiel@next-kraftwerke.de" name="plz_email"/></td>');
  print('<td><input type="submit" class="button-primary" value="'.$nextLangAdmin['new'].'" /></td>');
  print('</tr>');
  print('</form>');

?>

</table>


<br/> 
<hr/>
<h2><?php echo $nextLangAdmin['testme']; ?></h2>

<form method="post" action="">
  <input type="hidden" name="action" value="test" />
  <input type="text" value="<?php print($_REQUEST['plz']); ?>" name="plz"/>
  <input type="submit"/>
</form>

<?php

  if($_REQUEST['action']=='test')
  {
    $val=(int)$_REQUEST['plz'];

    global $wpdb;
    $q='SELECT * FROM erloes_plz WHERE start <= '.$val.' AND end > '.$val;
    $res = $wpdb->get_results($q);

    if($res[0]->email=='') print("<h3>No mapping found!</h3>");
      else print("<h3>Result: ".$res[0]->email.'</h3>');
  }

?>

</form>
</p>
</div>
