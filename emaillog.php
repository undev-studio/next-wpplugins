<div class="wrap">
<!-- <script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script> -->
    <script src="../wp-content/plugins/next_content/libs/Chart.min.js"></script>





<h2>EMail Log</h2>
<?php
add_thickbox(); 


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
  
  if($_REQUEST["filter"]=="revenuecalc") $filter="WHERE templatename = 'erloesrechner' ";
  if($_REQUEST["filter"]=="erloesrechner") $filter="WHERE templatename = 'erloesrechner' OR templatename LIKE 'widget_%' ";
  if($_REQUEST["filter"]=="form_dvpv") $filter="WHERE templatename = 'form_dvpv'";
  if($_REQUEST["filter"]=="widgets") $filter="WHERE templatename LIKE 'widget%'";


  if($_REQUEST["filter"]=="")print('<b>');
  print('<a href="?page=next_content/emaillog.php&filter=">No Filter</a> ');
  if($_REQUEST["filter"]=="")print('</b>');



  print(' | ');
  
  if($_REQUEST["filter"]=="erloesrechner")print('<b>');
  print('<a href="?page=next_content/emaillog.php&filter=erloesrechner">ALL Revenue Calculator</a> ');
  if($_REQUEST["filter"]=="erloesrechner")print('</b>');

  print(' | ');
  
  if($_REQUEST["filter"]=="erloesrechner")print('<b>');
  print('<a href="?page=next_content/emaillog.php&filter=form_dvpv">Form DVPV</a> ');
  if($_REQUEST["filter"]=="erloesrechner")print('</b>');

  print(' | ');
  
  if($_REQUEST["filter"]=="erloesrechner")print('<b>');
  print('<a href="?page=next_content/emaillog.php&filter=widgets">Widget Calculators</a> ');
  if($_REQUEST["filter"]=="erloesrechner")print('</b>');

  print(' | ');
  
  if($_REQUEST["filter"]=="erloesrechner")print('<b>');
  print('<a href="?page=next_content/emaillog.php&filter=revenuecalc">Revenue Calculator</a> ');
  if($_REQUEST["filter"]=="erloesrechner")print('</b>');




  echo '<div style="width: 50%"><canvas id="chartcanvas" height="450" width="600"></canvas></div>';


//https://github.com/nnnick/Chart.js
// SELECT Month(datesend) AS month,Year(datesend) AS year,COUNT(*) AS mcount FROM `emaillog` WHERE templatename ='erloesrechner' GROUP BY datesend, Month(datesend),YEAR(datesend) ORDER BY datesend 

  global $wpdb;

  $sql="SELECT *,YEAR(datesend) AS year,MONTH(datesend) AS month FROM emaillog ".$filter." ORDER BY datesend DESC LIMIT 0,3000;";

  // echo $sql;
  $emails = $wpdb->get_results($sql);


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

    // if($lastContent==$email->content )continue;
    // if($lastTo==$email->to && $lastDate==$datestr )continue;

    $count++;

    $lastTo=$email->to;
    $lastDate=$datestr;
    // $lastContent=''.$email->content;

    $stats[$email->year][$email->month]['count']++;


    $html.='<tr>';
    $html.='  <td>'.$email->id.'</td>';
    $html.='  <td>'.$email->to.'</td>';
    $html.='  <td>'."". $email->datesend.'</a></td>';
    $html.='  <td>'.$email->templatename.'</td>';
    $html.='  <td><a href="#TB_inline?width=800&height=640&inlineId=modalContent'.$email->id.'" class="thickbox">Content</a></td>';
    $html.='  <div id="modalContent'.$email->id.'" style="display:none;">';
    $html.='    <pre id="emailid'.$email->id.'" style="display:inline-block;width:100%;height:100%overflow:scroll;" class="prettyprint">'.prettyPrint($email->content).'</pre>';
    $html.='  </div>';
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

function init(ctx)
{
    window.myBar = new Chart(ctx).Bar(
      barChartData,
      {
        responsive : true
      });
}

function preInit()
{
  var ctx=document.getElementById("chartcanvas").getContext("2d")
  if(!ctx)
  {
    console.log('wait for ctx...');
    setTimeout(preInit,200);
  }
  else init(ctx);

}

window.onload=preInit;
  document.addEventListener("DOMContentLoaded", function(event)
  {
    // preInit();
  });

  </script>


</div>