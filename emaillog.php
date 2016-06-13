<div class="wrap">
<!-- <script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script> -->
    <script src="../wp-content/plugins/next_content/libs/Chart.min.js"></script>

<h2>EMail Log</h2>
<?php


  function prettyPrint( $json )
  {
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
  }





  error_reporting(E_ERROR|E_WARNING);
  ini_set('display_errors', '1');

  $filter="";
  if($_REQUEST["filter"]=="erloesrechner") $filter="WHERE templatename = 'erloesrechner'";


  if($_REQUEST["filter"]=="")print('<b>');
  print('<a href="?page=next_content/emaillog.php&filter=">Kein Filter</a> ');
  if($_REQUEST["filter"]=="")print('</b>');

  print(' | ');
  
  if($_REQUEST["filter"]=="erloesrechner")print('<b>');
  print('<a href="?page=next_content/emaillog.php&filter=erloesrechner">erloesrechner</a> ');
  if($_REQUEST["filter"]=="erloesrechner")print('</b>');



    echo '<div style="width: 50%"><canvas id="canvas" height="450" width="600"></canvas></div>';


//https://github.com/nnnick/Chart.js
// SELECT Month(datesend) AS month,Year(datesend) AS year,COUNT(*) AS mcount FROM `emaillog` WHERE templatename ='erloesrechner' GROUP BY datesend, Month(datesend),YEAR(datesend) ORDER BY datesend 

  global $wpdb;
  $emails = $wpdb->get_results("SELECT *,YEAR(datesend) AS year,MONTH(datesend) AS month FROM emaillog ".$filter." ORDER BY datesend DESC ;");


  print('<div class="wrap">');

  $stats=array();

  $lastTo="";
  $lastDate="";
  $count=0;
  $lastContent="";
  $html='';

  foreach ($emails as &$email) 
  {
    $date = new DateTime($email->datesend);
    $datestr=date("Ymd",date_timestamp_get($date));

    if($lastContent==$email->content )continue;
    // if($lastTo==$email->to && $lastDate==$datestr )continue;

    $count++;

    $lastTo=$email->to;
    $lastDate=$datestr;
    $lastContent=$email->content;

    $stats[$email->year][$email->month]['count']++;


    $html.='<tr>';
    $html.='  <td>'.$email->id.'</td>';
    $html.='  <td><a target="blankk" href="https://mandrillapp.com/activity/content?id='.$datestr."_".$email->mandrilid.'">'.$email->to.'</a></td>';
    $html.='  <td>'."". $email->datesend.'</a></td>';
    $html.='  <td>'.$email->templatename.'</td>';
    $html.='  <td onclick="jQuery(\'#emailid'.$email->mandrilid.'\').toggle();"><a>Inhalt</a></td>';

    $html.='<tr>';
    $html.='  <td colspan="3">';
    $html.='    <pre id="emailid'.$email->mandrilid.'" style="width:auto;display:none;" class="prettyprint">'.prettyPrint($email->content).'</pre>';
    $html.='  </td>';
    $html.='</tr>';

    $html.='</tr>';
  }







  
  print('<br/><table class="wp-list-table widefat " >');
  print('<thead>');
  print('<tr>');
  print('<th>ID</th>');
  print('<th>To</th>');
  print('<th>Date</th>');
  print('<th>Form</th>');
  print('<th>Content</th>');
  print('</tr>');
  print('</thead>');
  echo $html;

  print('</table>');
  print('</div>');
  echo $count;



?>





<?php

  $monthNumbers=array();
  $monthTitles=array();

  $months=["Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"];

  foreach($stats as $keyy =>$valy)
  {
      $yearstr=$keyy;
      foreach($valy as $keym =>$valm)
      {
        $monthTitles[]=$months[$keym-1].' '.$yearstr;
        $monthNumbers[]=$valm['count'];
      }
  }

  $monthTitles=array_reverse($monthTitles);
  $monthNumbers=array_reverse($monthNumbers);

?>

  <script>
  var randomScalingFactor = function(){ return Math.round(Math.random()*100)};

  var barChartData = {
    labels : [
    <?php foreach($monthTitles AS $n) echo '"'.$n.'",'; ?>
    ],
    datasets : [
      {
        fillColor : "rgba(150, 190, 15,0.5)",
        strokeColor : "rgba(150, 190, 15,1.0)",
        highlightFill: "rgba(150, 190, 15,1.0)",
        highlightStroke: "rgba(150, 190, 15,1)",
        data : [
          <?php foreach($monthNumbers AS $n) echo $n.','; ?>
        ]
      },
      // {
      //   fillColor : "rgba(151,187,205,0.5)",
      //   strokeColor : "rgba(151,187,205,0.8)",
      //   highlightFill : "rgba(151,187,205,0.75)",
      //   highlightStroke : "rgba(151,187,205,1)",
      //   data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
      // }
    ]

  }
  window.onload = function(){
    var ctx = document.getElementById("canvas").getContext("2d");
    window.myBar = new Chart(ctx).Bar(barChartData, {
      responsive : true
    });
  }

  </script>


</div>