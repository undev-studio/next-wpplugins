<?php

error_reporting(E_ERROR | E_WARNING);
ini_set('display_errors', '1');

add_action('wp_ajax_nopriv_uptodate', 'ajax_uptodate_callback');
add_action('wp_ajax_uptodate', 'ajax_uptodate_callback');

require_once("settings_next.php");

function getEvents()
{
  global $wpdb;
  $sql = 'SELECT * FROM next_events WHERE DATEDIFF(date_start,NOW())>=0 OR DATEDIFF(date_end,NOW())>=0 ORDER BY DATEDIFF(date_start,NOW()) ASC LIMIT 0,4';
  $events = $wpdb->get_results($sql);

  if ($wpdb->last_error != '') echo($wpdb->last_error);


  if (!is_numeric($instance['textlength'])) $instance['textlength'] = 60;

  $args = [
    'post_type' => 'page',
    'fields' => 'ids',
    'nopaging' => true,
    'meta_key' => '_wp_page_template',
    'meta_value' => 'page-events.php'
  ];

  $pages = get_posts($args);
  foreach ($pages as $page)
    $eventPageUrl = get_permalink($page);
  // echo get_permalink($page) . '</br>';


  foreach ($events as $event) {
    $ds = strtotime($event->date_start);
    $de = strtotime($event->date_end);

    $day_start = date('d', $ds);
    $month_start = date('m', $ds);

    $day_end = date('d', $de);
    $month_end = date('m', $de);

    // start/end on same day!
    if ($day_end == $day_start && $month_start == $month_end) $event->date_readable = date('d.m.Y', $ds);
    else
      // start end in same month
      if ($month_start == $month_end) $event->date_readable = date('d.', $ds) . ' - ' . date('d.m.Y', $de);
      else
        // start end in same month
        $event->date_readable = date('d.m.', $ds) . ' - ' . date('d.m.Y', $de);
    $event->permalinkAllEvents = $eventPageUrl;


    $event->text = strip_tags($event->text);
    if (strlen($event->text) > $instance['textlength']) $event->text = truncateHtml($event->text, $instance['textlength'], '...');
  }

  return $events;
}

function getJobs()
{
  global $wpdb;

  $langSQL = '';
  if (function_exists('pll_current_language')) {
    $langSQL = ' AND  (language="' . pll_current_language() . '" OR language="" ) ';
  }

  $sql = 'SELECT * FROM next_jobs WHERE activated=1 ' . $langSQL . ' ORDER BY sort LIMIT 0,7';
  // echo $sql;
  $jobs = $wpdb->get_results($sql);

  if ($wpdb->last_error != '') echo($wpdb->last_error);

  // var_dump($jobs);

  $rJobs = Array();

  foreach ($jobs as $job) {
    $j = Array();

    $j['text'] = $job->text;
    $j['title'] = $job->title;
    $j['id'] = $job->id;
    $rJobs[] = $j;
  }

  return $rJobs;
}


function uptodateGetBlog()
{

  $cfg = nextSettings::load();


  $args = array(
    'numberposts' => 3,
    'category_name' => $cfg[nextSettings::POST_CAT_BLOG],
    'post_status' => 'publish'
  );

  $recent = wp_get_recent_posts($args);
  $data = array();

  foreach ($recent as $key => $p) {
    $recent[$key]['permalink'] = get_permalink($p['ID']);

    $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($p['ID']), "thumbnail");
    if ($thumb[0]) $recent[$key]['thumb'] = $thumb[0];
    $recent[$key]['date_readable'] = date('j.n.Y', strtotime($recent[$key]['post_date']));

  }

  return $recent;
}

function uptodateGetNews()
{
  global $wpdb;

  $lang = '';

  if (function_exists('pll_the_languages'))
    $langSQL = ' WHERE (language="' . pll_current_language() . '" OR language="" ) ';


  $sql = 'SELECT * FROM next_news ' . $langSQL . ' ORDER BY newsdate DESC LIMIT 0,4';


  $news = $wpdb->get_results($sql);

  if ($wpdb->last_error != '') echo($wpdb->last_error);

  $rNews = Array();

  foreach ($news as $n) {
    $ds = strtotime($n->newsdate);
    $n->date_readable = date('d.m.Y', $ds);

    $n->text = strip_tags($n->text);

    $nnews = Array();
    $nnews['id'] = $n->id;
    $nnews['date_readable'] = $n->date_readable;
    $nnews['text'] = $n->text;
    $nnews['linkurl'] = $n->linkurl;
    $nnews['linktitle'] = $n->linktitle;
    $nnews['title'] = $n->title;

    $rNews[] = $nnews;
  }
  return $rNews;

}

function ajax_uptodate_callback()
{
  $response = Array();

  $response['title_blog'] = "blog";
  $response['title_news'] = "news";
  $response['title_events'] = "events";
  $response['title_jobs'] = "jobs";

  $response['blog'] = uptodateGetBlog();
  $response['news'] = uptodateGetNews();
  $response['events'] = getEvents();
  $response['jobs'] = getJobs();


  echo json_encode($response);

  die();
}

?>