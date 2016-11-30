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


//require_once('language.php');

require_once('ajax_formdvpv.php');
require_once('ajax_widget_forms.php');
require_once('ajax_gallery.php');
require_once('ajax_erloesrechner.php');
require_once('ajax_newsletter.php');
require_once('ajax_status.php');
require_once('ajax_language.php');





$nextLangAdmin=Array(

    'references' => 'References',

    'edit' => 'Edit',
    'save' => 'Save',
    'delete' => 'Delete',
    'new' => 'New Entry',

    'revenue_calculator' => 'Revenue Calculator',
    'postal_code_alloc' => 'Postal Code Mapping',
    'postal_code_alloc_email' => 'Postal Code Mapping for E-Mails',
    'testme'=>'Test me',
    'zipStart'=>'Postal Code<br/> starts with',
    'zipEnd'=>'Postal Code<br/> ends with',

    );


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

// require_once('widget_cleverreach.php');
// add_action( 'widgets_init', create_function('', 'return register_widget("nextCleverreachWidget");') );

require_once('widget_text.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextTextWidget");') );

require_once('widget_kennzahlen.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextKennzahlenWidget");') );

require_once('widget_footerhomemenu.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextFooterHomeMenu");') );

require_once('widget_prodselect.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextProdSelectWidget");') );

require_once('widget_erloes.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextErloesWidget");') );

require_once('widget_awards.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextAwardsWidget");') );

require_once('widget_iconslefttextright.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextIconLeftTextRight");') );

require_once('widget_linklist.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextLinkListWidget");') );

// require_once('widget_form_flexheft.php');
// add_action( 'widgets_init', create_function('', 'return register_widget("nextFormFlexheftWidget");') );

// require_once('widget_form_minierloes.php');
// add_action( 'widgets_init', create_function('', 'return register_widget("nextFormMiniErloesWidget");') );



require_once('next_widget_widget.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextWidget");') );

require_once('next_widget_random.php');
add_action( 'widgets_init', create_function('', 'return register_widget("nextWidgetRandom");') );




add_action('admin_menu', 'next_content_menu');

function next_content_menu()
{
    global $nextLangAdmin;

    $settings_page = add_menu_page(
        'next_content/news.php',
        'NEXT Content',
        0,
        'next_content/news.php'
    );

    $isAdmin=current_user_can( 'activate_plugins' );

    add_submenu_page( 'next_content/news.php', 
        'News', 'News','publish_posts', 
        'next_content/news.php' );
    
    if($isAdmin) add_submenu_page( 'next_content/news.php', 
        'Widgets', 'Widgets', 'publish_posts', 
        'next_content/next_widgets.php' );

    add_submenu_page( 'next_content/news.php', 
        $nextLangAdmin['references'], $nextLangAdmin['references'], 'publish_posts', 
        'next_content/referenzen.php' );
    
    if($isAdmin) add_submenu_page( 'next_content/news.php', 
        $nextLangAdmin['revenue_calculator'], $nextLangAdmin['revenue_calculator'], 'publish_posts', 
        'next_content/erloesrechner.php' );

    add_submenu_page( 'next_content/news.php', 'EMail Log', 'EMail Log',              'publish_posts', 'next_content/emaillog.php' );
    add_submenu_page( 'next_content/news.php', 'Next Pool', 'Next Pool',            'publish_posts', 'next_content/nextpool.php' );
    add_submenu_page( 'next_content/news.php', 'Jobs', 'Jobs',                      'publish_posts', 'next_content/jobs.php' );
    add_submenu_page( 'next_content/news.php', 'Events', 'Events',                'publish_posts', 'next_content/events.php' );
    add_submenu_page( 'next_content/news.php', 'Partner', 'Partner',                'publish_posts', 'next_content/partner.php' );
    if($isAdmin) add_submenu_page( 'next_content/news.php', 'Remit', 'Remit',                    'publish_posts', 'next_content/remit_info.php' );
    if($isAdmin) add_submenu_page( 'next_content/news.php', 'Translation', 'Translation','publish_posts', 'next_content/language-edit.php' );
    if($isAdmin) add_submenu_page( 'next_content/news.php', 'Status', 'Status','publish_posts', 'next_content/status.php' );

    // wp_enqueue_script('custom-script','/wp-content/themes/nextkraftwerke/js/jquery-sortable.js',array( 'jquery' ));
}


?>
