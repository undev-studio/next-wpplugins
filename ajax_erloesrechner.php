<?php

/*
Plugin Name: Next Erloesrechner AJAX Backend
Plugin URI: http://undev.de/
Description: Next Erloesrechner 
Author: undefined
Version: 1
Author URI: http://undev.de/
*/
 

// error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
// ini_set('display_errors', '1');

// require_once(__DIR__.'/../themes/next2015/util/next.php');


$hookName="erloesberechner";
add_action( 'wp_ajax_'.$hookName, 'the_action_function' );
add_action( 'wp_ajax_nopriv_'.$hookName, 'the_action_function' ); // need this to serve non logged in users
$erloesUserName='';
$debug='';

$plzValue='';

if(isset($_REQUEST['data']))
{
  $formData=json_decode($_REQUEST['data'], true);
  $response=array();
  $response['errors']=array();
  $response['values']=array();

  $emailHTML="";
  // $translation=array();
}

  $countryCode='';
  $countryExt='';
  if (strpos($_SERVER['HTTP_HOST'], '.at') !== false)
  {
    $countryCode='at';
    $countryExt='_at';
  }


function getIdData($id,$type)
{
  global $formData;
  global $debug;
  $idStr=$id;//'in_'.$id.'_'.$type;

  foreach ($formData as $val)
  {
    // $debug.="".$val['id'].'! ';
    if($val['id']==$idStr) 
    {
      // echo "FOUND!";
      return $val;
    }
  }
}

function erlEndsWith($haystack, $needle) 
{
  // search forward starting from end minus needle length characters
  return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}


function translateString($str)
{
  $lang='de';

  $arr=explode('-',$str);
  if(count($arr)>0){ $str=$arr[count($arr)-1]; }

  

  global $translation;
  if( isset($translation['root'][$lang][$str]) ) return $translation['root'][$lang][$str];
  else
    if(erlEndsWith($str,'_group')) return translateString(substr($str,0,strlen($str)-strlen('_group')));

  return '?'.$str;
}

function translateE($obj)
{
  $lang='de';
  global $translation;
  return translateString($obj['id']);
  // if( isset($translation['root'][$lang][$obj['id']]) ) return $translation['root'][$lang][$obj['id']];
  // if( isset($obj['trans']) && isset($translation['root'][$lang][$obj['trans']]) ) return $translation['root'][$lang][$obj['trans']];
  // return '?'.$obj['id'];
  // return "ja ";
}

function isGroupChecked($id)
{
  global $debug;
  if($id=="ihreDaten_group")return true;
  if($id=="erzeuger_group")return true;
  if($id=="speicher_group")return true;
  if($id=="stadtwerke_group")return true;
  if($id=="verbraucher_group")return true;
  if($id=="ihreMitteilungAnUns_group")return true;

  $idcheckbox=str_replace ( "_group" , "" , $id );
  // $debug.='<br/> is checked?['.$idcheckbox.'] ';

  $var=getIdData($idcheckbox,'checkbox');

  // $debug.=json_encode($var);

  if( $var['value']==true) return true;

  return false;
}


function parseGroup($group,$parentName,$parentRequired=true)
{
  global $debug;
  global $response;

  if(is_array($group))
  {
    foreach ($group as $input)
    {

      
      if(isset($input['id']) && $input['id']=='ihreDaten-ihrName')
      {
        $val=getIdData($input['id'],$input['type'])['value'];
        global $erloesUserName;
        $erloesUserName=$val;
      }

      if(isset($input['id']) && $input['id']=='ihreDaten-ihrePostleitzahl')
      {
        $val=getIdData($input['id'],$input['type'])['value'];
        global $plzValue;
        global $countryCode;
        $plzValue=$val;


        if($countryCode=='at')
        {
          if( !is_numeric($val) || strlen($val)<4)
          {
            $obj=array();
            $obj['id']=$input['id'];
            $response['errors'][]=$obj;
          }
        }
        else
        {
          if( !is_numeric($val) || strlen($val)<5)
          {
            $obj=array();
            $obj['id']=$input['id'];
            $response['errors'][]=$obj;
          }
        }
      }

      if(isset($input['type']) && $input['type']=='group' )
      {
        if(isGroupChecked($input['id']) )
        {
          parseGroup($input,$parentName.','.$input['id']);

          if(isset($input['contents']))
          {
            parseGroup($input['contents'],$parentName.','.$input['id']);
          }
        }
        else
        {
          parseGroup($input['contents'],$parentName.','.$input['id'],false);
        }
      }

      if($parentRequired)
      {
        $req=false;

        if(isset($input['required']) ) $req=$input['required']==true;
        if($req)
        {
          $debug.='testing '.$input['id'];
          $val=getIdData($input['id'],$input['type'])['value'];

          if($val=='')
          {
            $obj=array();
            $obj['id']=$input['id'];
            // $obj['htmlid']='in_'.$input['id'].'_'.$input['type'];
            $response['errors'][]=$obj;
          }
        }
      }


      if(isset($input['id']))
      {
        $val=getIdData($input['id'],$input['type'])['value'];

        if($val!=null && $val!='')
        {
          $obj=array();
          $obj['id']=$input['id'];
          $obj['value']=$val;
          $obj['type']=$input['type'];
          if(isset($input['trans'])) $obj['trans']=$input['trans'];
          $obj['parent']=$parentName;

          // $obj['htmlid']='in_'.$input['id'].'_'.$input['type'];
          $response['values'][]=$obj;
        }
      }
    }
  }
}

function transParent($str)
{
  $string="";
  $arr=explode(',',$str);
  $count=0;
  foreach ($arr as $s)
  {
    if($s!='root')
    {
      if($count>0)$string.=' - ';
      $string.=translateString($s)." ";

      $count++;
    }
  }

  return $string;
}

function printEmailValue($root)
{
  $endl='<br/>';
  $lastParent="";
  global $emailHTML;

  foreach ($root as $input)
  {
    if($input['parent']!=$lastParent)
    {
      $lastParent=$input['parent'];
      $emailHTML.=$endl.'<h3>'.transParent($lastParent).'</h3>';
    }

    if($input['type']=='checkbox' && $input['value']) $input['value']="Ja";
    $emailHTML.=translateE($input).': <b>'.$input['value'].'</b>'.$endl;
  }
}

function buildEMail()
{
  global $countryCode;
  global $response,$emailHTML;
  $emailHTML='<h3>Der Erlösberechner '.$countryCode.' wurde ausgefüllt:</h3>';
  printEmailValue($response['values']);
}


function sendErloesMail($message)
{
    global $formData;
    global $wpdb;
    global $plzValue;
    global $wpdb;
    global $erloesUserName;

    // get email adress of consultant
    $q='SELECT * FROM erloes_plz WHERE start <= '.$plzValue.' AND end > '.$plzValue;
    // echo $q;
    $res = $wpdb->get_results($q);

    if($res[0]->email=='')
    {
      $obj=array();


    $headers = 
        'MIME-Version: 1.0' . "\r\n".
        'Content-type: text/html; charset=iso-8859-1'."\r\n" .
        $next_email_from. "\r\n" .
        'Reply-To: wordpress@next-kraftwerke.de' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
      wp_mail('tom@undev.de', 'erleosberechner / keine plz zuordnung', Util::umlaute('keine zuordnung gefunden fuer plz:'.$plzValue) , '','' );


      $response['errorfail']="keine zuordnung gefunden!";
      return;
    }

    $lastid = $wpdb->insert('emaillog', array(
      'content' => json_encode($formData),
      'templatename' => 'erloesrechner',
      'to' => $res[0]->email
      )
    );

    // compose email
    $username=getIdData("ihreDaten-ihrName","text")["value"];

    $subject = 'Neue Nachricht von '.$erloesUserName;
    $headers = 
        'MIME-Version: 1.0' . "\r\n".
        'Content-type: text/html; charset=iso-8859-1'."\r\n" .
        $next_email_from. "\r\n" .
        'Reply-To: wordpress@next-kraftwerke.de' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    // send email
    $attachments='';
    wp_mail($res[0]->email, $subject, Util::umlaute($message) , $headers, $attachments );
    wp_mail('presse@next-kraftwerke.de', $subject, Util::umlaute($message) , $headers, $attachments );
    wp_mail('tom@undev.de', $subject, Util::umlaute($message) , $headers, $attachments );
}


function the_action_function() 
{
  global $countryExt;
  
  $string = file_get_contents(__DIR__."/../theme/json/erloesberechner".$countryExt.".json");
  $structure = json_decode($string, true);
  parseGroup($structure['root'],'root');

  global $translation;
  $transStr = file_get_contents(__DIR__."/../theme/json/erloesberechner_trans".$countryExt.".json");
  $translation = json_decode($transStr, true);

  global $response;
  global $debug,$emailHTML;
  
  buildEMail();
  $response['mail']=$emailHTML;

  $response['debug']=$debug;

  if(!$response['errors'] || $_REQUEST['force']==true)
  {
    sendErloesMail($emailHTML);
    // all is ok, send email!
    // die("all ok! send email!".$plzValue );
  }

  echo json_encode($response, JSON_PRETTY_PRINT);


  die();
}



?>