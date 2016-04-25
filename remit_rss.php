<?php

error_reporting(E_ERROR|E_WARNING);
ini_set('display_errors', '1');

$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

include 'libs/feedwriter/Item.php';
include 'libs/feedwriter/Feed.php';
include 'libs/feedwriter/RSS2.php';

date_default_timezone_set('UTC');

use \FeedWriter\RSS2;


$feed = new RSS2();
$feed->setTitle('Next Kraftwerke REMIT');
$feed->setLink('https://www.next-kraftwerke.de/');
$feed->setDescription('REMIT Data');
$feed->setChannelElement('language', 'de-DE');
$feed->setDate(date(DATE_RSS, time()));


$posts = $wpdb->get_results('SELECT * FROM next_remit_info ORDER BY pubdate DESC');
foreach ($posts as &$item)
{
    $newItem = $feed->createNewItem();

    $newItem->setTitle($item->title);
    $newItem->setLink('https://www.next-kraftwerke.de/remit');
    $newItem->setDescription(
        ''.$item->comment
        .'<br/>'
        .'<br/>Date of occasion: '.date('j.n.Y',strtotime($item->date))
        .'<br/>Company: '.$item->company
        );

    $newItem->setDate($item->pubdate);
    $newItem->setId('https://www.next-kraftwerke.de/remit?id='.$item->id, true);

    $feed->addItem($newItem);
}

$myFeed = $feed->generateFeed();


header('Content-Type: application/xml');

$feed->printFeed();


?>