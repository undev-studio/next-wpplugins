<div class="wrap">
<h2>Erl&ouml;srechner</h2>


<?php


$user_reg="";



global $wpdb;
$wpdb->get_results("DELETE FROM erloes_plz WHERE start = 0 AND end = 0;");



//  error_reporting(E_ERROR|E_WARNING);
//  ini_set('display_errors', '1');


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
if($user_reg!="")print('<div style="padding:5px;border:1px solid #aaa;">Datensatz wurde gespeichert </div>');

?>
<br/>
<h2>PLZ/E-Mail Zordnungen</h2>


<table>
<tr>
  <td>PLZ Start:</td>
  <td>PLZ Ende:</td>
  <td>E-Mail:</td>
</tr>

<?php

  $allPLZ = PlzRangedb::loadAll();
  //print_r($all);


/*
$plznotfound=0;
for($i=0;$i<100000;$i++)
{
  $found=false;
  foreach ($allPLZ as &$plz)
  {
    if($plz->start<=$i && $plz->end>$vi)
    {
      $found=true;

    }
  }
  if(!$found)
  {
    $plznotfound++;
  }

}
*/


  $sum=0;
  foreach ($allPLZ as &$plz)
  {  
    $sum+=($plz->end - $plz->start);
  }


if($sum!=100000)
{
  print('<div class="error" style="padding:10px;">Summe aller Postleitzahlen: '.$sum.' <br/><b>Diese Summe sollte 100000 ergeben!</b> </div>');
}




  foreach ($allPLZ as &$plz)
  {
    print('<tr>');
    print('<form method="post" action=""><input type="hidden" name="action" value="update" />');
    print('<input type="hidden" name="pkey" value="'.$plz->pkey.'" />');
    print('<td><input type="text" value="'.$plz->start.'" name="plz_start"/></td>');
    print('<td><input type="text" value="'.$plz->end.'" name="plz_end"/></td>');
    print('<td><input type="text" value="'.$plz->email.'" name="plz_email"/></td>');
    print('<td><input type="submit" class="button-primary" value="Speichern" /></td>');
    print('<td><a href="admin.php?page=next_content%2Ferloesrechner.php&delete='.$plz->pkey.'">L&ouml;schen</a></td>');
    print('</form>');
    print('</tr>');
  }
  print('</table><br/><hr/><table>');
  print('<tr><td colspan="5"><br/><h2>Neuer Eintrag</h2></td></tr>');

  print('<form method="post" action=""><input type="hidden" name="action" value="create" />');
  print('<tr>');
  print('<td><input type="text" value="0" name="plz_start"/></td>');
  print('<td><input type="text" value="1000" name="plz_end"/></td>');
  print('<td><input type="text" value="beispiel@next-kraftwerke.de" name="plz_email"/></td>');
  print('<td><input type="submit" class="button-primary" value="Neu Erstellen" /></td>');
  print('</tr>');
  print('</form>');

?>

</table>

<br/>
<hr/>
<h2>Testen</h2>

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

    if($res[0]->email=='') print("<h3>Keine Zuordnung gefunden!</h3>");
      else print("<h3>Ergebnis: ".$res[0]->email.'</h3>');
  }

?>

</form>
</p>
</div>
