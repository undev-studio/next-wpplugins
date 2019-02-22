<div class="wrap">
    <!-- <script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script> -->
    <script src="../wp-content/plugins/next_content/libs/Chart.min.js"></script>


    <h2>EMail Log</h2>
  <?php
  add_thickbox();


  function prettyPrint($json)
  {
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen($json);

    for ($i = 0; $i < $json_length; $i++) {
      $char = $json[$i];
      $new_line_level = NULL;
      $post = "";
      if ($ends_line_level !== NULL) {
        $new_line_level = $ends_line_level;
        $ends_line_level = NULL;
      }
      if ($in_escape) {
        $in_escape = false;
      } else if ($char === '"') {
        $in_quotes = !$in_quotes;
      } else if (!$in_quotes) {
        switch ($char) {
          case '}':
          case ']':
            $level--;
            $ends_line_level = NULL;
            $new_line_level = $level;
            break;

          case '{':
          case '[':
            $level++;
          case ',':
            $ends_line_level = $level;
            break;

          case ':':
            $post = " ";
            break;

          case " ":
          case "\t":
          case "\n":
          case "\r":
            $char = "";
            $ends_line_level = $new_line_level;
            $new_line_level = NULL;
            break;
        }
      } else if ($char === '\\') {
        $in_escape = true;
      }
      if ($new_line_level !== NULL) {
        $result .= "\n" . str_repeat("\t", $new_line_level);
      }
      $result .= $char . $post;
    }

    return $result;
  }


  error_reporting(E_ERROR | E_WARNING);
  ini_set('display_errors', '1');

  $filter = "";

  if ($_REQUEST["filter"] == "revenuecalc") $filter = "WHERE templatename = 'erloesrechner' ";
  if ($_REQUEST["filter"] == "erloesrechner") $filter = "WHERE templatename = 'erloesrechner' OR templatename LIKE 'widget_%' OR templatename LIKE 'forms %' OR templatename = 'form_dvpv'";
  if ($_REQUEST["filter"] == "form_dvpv") $filter = "WHERE templatename = 'form_dvpv'";
  if ($_REQUEST["filter"] == "widgets") $filter = "WHERE templatename LIKE 'widget%'";
  if ($_REQUEST["filter"] == "forms") $filter = "WHERE templatename LIKE 'forms %'";


  if ($_REQUEST["filter"] == "") print('<b>');
  print('<a href="?page=next_content/emaillog.php&filter=">No Filter</a> ');
  if ($_REQUEST["filter"] == "") print('</b>');


  print(' | ');

  if ($_REQUEST["filter"] == "erloesrechner") print('<b>');
  print('<a href="?page=next_content/emaillog.php&filter=erloesrechner">ALL Revenue Calculator</a> ');
  if ($_REQUEST["filter"] == "erloesrechner") print('</b>');

  print(' | ');

  if ($_REQUEST["filter"] == "form_dvpv") print('<b>');
  print('<a href="?page=next_content/emaillog.php&filter=form_dvpv">Form DVPV</a> ');
  if ($_REQUEST["filter"] == "form_dvpv") print('</b>');

  print(' | ');

  if ($_REQUEST["filter"] == "forms") print('<b>');
  print('<a href="?page=next_content/emaillog.php&filter=forms">Other Forms</a> ');
  if ($_REQUEST["filter"] == "forms") print('</b>');

  print(' | ');

  if ($_REQUEST["filter"] == "widgets") print('<b>');
  print('<a href="?page=next_content/emaillog.php&filter=widgets">Widget Calculators</a> ');
  if ($_REQUEST["filter"] == "widgets") print('</b>');

  print(' | ');

  if ($_REQUEST["filter"] == "revenuecalc") print('<b>');
  print('<a href="?page=next_content/emaillog.php&filter=revenuecalc">Revenue Calculator</a> ');
  if ($_REQUEST["filter"] == "revenuecalc") print('</b>');


  echo '<div style="width: 50%"><canvas id="chartcanvas" height="450" width="600"></canvas></div>';


  //https://github.com/nnnick/Chart.js
  // SELECT Month(datesend) AS month,Year(datesend) AS year,COUNT(*) AS mcount FROM `emaillog` WHERE templatename ='erloesrechner' GROUP BY datesend, Month(datesend),YEAR(datesend) ORDER BY datesend

  global $wpdb;

  $sql = "SELECT *,YEAR(datesend) AS year,MONTH(datesend) AS month FROM emaillog " . $filter . " ORDER BY datesend DESC LIMIT 0,3000;";

  // echo $sql;
  $emails = $wpdb->get_results($sql);


  print('<div class="wrap">');

  $stats = array();

  $lastTo = "";
  $lastDate = "";
  $count = 0;
  $lastContent = "";
  $html = '';

  foreach ($emails as &$email) {
    $date = new DateTime($email->datesend);
    $datestr = date("Ymd", date_timestamp_get($date));

    // if($lastContent==$email->content )continue;
    // if($lastTo==$email->to && $lastDate==$datestr )continue;

    $count++;

    $lastTo = $email->to;
    $lastDate = $datestr;
    // $lastContent=''.$email->content;

    $allTemplates[$email->templatename] = true;
    $allYears[$email->year] = true;
    $stats[$email->templatename][$email->year][$email->month]['count']++;


    $html .= '<tr>';
    $html .= '  <td>' . $email->id . '</td>';
    $html .= '  <td>' . $email->to . '</td>';
    $html .= '  <td>' . "" . $email->datesend . '</a></td>';
    $html .= '  <td>' . $email->templatename . '</td>';
    $html .= '  <td><a href="#TB_inline?width=800&height=640&inlineId=modalContent' . $email->id . '" class="thickbox">Content</a></td>';
    $html .= '  <div id="modalContent' . $email->id . '" style="display:none;">';
    $html .= '    <pre id="emailid' . $email->id . '" style="display:inline-block;width:100%;height:100%overflow:scroll;" class="prettyprint">' . prettyPrint($email->content) . '</pre>';
    $html .= '  </div>';
    $html .= '</tr>';
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

  // fill up stats with zero values
  $currentMonth = date('n');
  $currentYear = date('Y');
  foreach($allTemplates as $t => $v) {
	if(!array_key_exists($t, $stats)) $stats[$t] = array();
  	foreach($allYears as $y => $v) {
		if(!array_key_exists($y, $stats[$t])) $stats[$t][$y] = array();
		for($i = 1; $i <= 12; $i++) {
			if($currentYear == $y && $i > $currentMonth) continue;
			if(!array_key_exists($i, $stats[$t][$y])) $stats[$t][$y][$i] = array('count' => 0);
		}
		ksort($stats[$t][$y]);
  	}
	ksort($stats[$t]);
  }
  ksort($stats);
  $monthNumbers = array();
  $monthTitles = array();

  $months = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];

  $datasets = [];
  foreach($stats as $templatename => $data) {
	$dataset = new \stdClass();
	$dataset->label = $templatename;
	$dataset->data = [];
  	foreach ($data as $keyy => $valy) {
  	  $yearstr = $keyy;
  	  foreach ($valy as $keym => $valm) {
	    $dataset->data[] = $valm['count'];
            $monthTitle = $months[$keym - 1] . ' ' . $yearstr;
  	    $monthTitles[$monthTitle] = true;
  	  }
	}
	$datasets[] = $dataset;
  }

  // $monthTitles = array_reverse($monthTitles);
  // $monthNumbers = array_reverse($monthNumbers);

  ?>

    <script>
      var randomScalingFactor = function () {
        return Math.round(Math.random() * 100)
      };

function hashCode(str) { // java String#hashCode
    var hash = 0;
    for (var i = 0; i < str.length; i++) {
       hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }
    return hash;
} 

function intToRGB(i){
    var c = (i & 0x00FFFFFF)
        .toString(16)
        .toUpperCase();

    return "00000".substring(0, 6 - c.length) + c;
}

var datasets = <?php echo json_encode($datasets) ?>;
for(var i = 0; i < datasets.length; i++) {
  datasets[i].backgroundColor = '#' + intToRGB(hashCode(datasets[i].label));
}

      var barChartData = {
        labels: <?php echo json_encode(array_keys($monthTitles)); ?>,
        datasets: datasets
      }

      function init(ctx) {
        window.myBar = new Chart(ctx,{type: 'bar', data: barChartData, options: { 
					tooltips: {
						mode: 'index',
						intersect: false,
						filter: function(item, data) { 
							return item.yLabel > 0;
						}
					},
            responsive: true,
scales: {
						xAxes: [{
							stacked: true,
						}],
						yAxes: [{
							stacked: true
						}]
					}
          }});
      }

      function preInit() {
        var ctx = document.getElementById("chartcanvas").getContext("2d")
        if (!ctx) {
          console.log('wait for ctx...');
          setTimeout(preInit, 200);
        } else init(ctx);

      }

      window.onload = preInit;
      document.addEventListener("DOMContentLoaded", function (event) {
        // preInit();
      });

    </script>


</div>
