<?php
/*
Plugin Name: next_content
Plugin URI: http://undev.de/
Description: next kraftwerke backend
Author: thomas kombuechen
Version: 1
Author URI: http://undev.de/
*/

error_reporting(E_ERROR|E_WARNING);
ini_set('display_errors', '1');

// error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
// ini_set('display_errors', '1');

require_once('widget_footernav.php');
add_action( 'widgets_init', create_function('', 'return register_widget("next_footernav");') );

require_once('widget_iconwidget.php');
add_action( 'widgets_init', create_function('', 'return register_widget("next_iconwidget");') );

require_once('widget_youtube.php');
add_action( 'widgets_init', create_function('', 'return register_widget("next_youtubewidget");') );

require_once('widget_references.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextReferenzWidget");') );

require_once('widget_jobs.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextJobsWidget");') );

require_once('widget_blog.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextBlogWidget");') );

require_once('widget_events.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextEventsWidget");') );

require_once('widget_image.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextImageWidget");') );

require_once('widget_image_special.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextImageSpecialWidget");') );

require_once('widget_news.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextNewsWidget");') );

require_once('widget_cleverreach.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextCleverreachWidget");') );

require_once('widget_text.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextTextWidget");') );

require_once('widget_kennzahlen.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextKennzahlenWidget");') );

require_once('widget_footerhomemenu.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextFooterHomeMenu");') );

require_once('widget_erloes.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextErloesWidget");') );





add_action('admin_menu', 'next_content_menu');


function next_content_menu() 
{
    $settings_page = add_menu_page( 
        'next_content/news.php',
        'NEXT Inhalte',
        0, 
        'next_content/news.php'
    );


    add_submenu_page( 'next_content/news.php', 'News', 'News', 'next', 'next_content/news.php' ); 
    add_submenu_page( 'next_content/news.php', 'Referenzen', 'Referenzen', 'next', 'next_content/referenzen.php' ); 
    add_submenu_page( 'next_content/news.php', 'Erloesrechner', 'Erloesrechner', 'next', 'next_content/erloesrechner.php' ); 
    add_submenu_page( 'next_content/news.php', 'EMaillog', 'EMaillog', 'next', 'next_content/emaillog.php' ); 
    add_submenu_page( 'next_content/news.php', 'Next Pool', 'Next Pool', 'next', 'next_content/nextpool.php' ); 
    add_submenu_page( 'next_content/news.php', 'Jobs', 'Jobs', 'next', 'next_content/jobs.php' ); 
    add_submenu_page( 'next_content/news.php', 'Termine', 'Termine', 'next', 'next_content/events.php' ); 
    add_submenu_page( 'next_content/news.php', 'Partner', 'Partner', 'next', 'next_content/partner.php' ); 


    // wp_enqueue_script('custom-script','/wp-content/themes/nextkraftwerke/js/jquery-sortable.js',array( 'jquery' ));

}



?>