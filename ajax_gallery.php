<?php


    add_action( 'wp_ajax_nopriv_gallery', 'ajax_gallery' );
    add_action( 'wp_ajax_gallery', 'ajax_gallery' );


    function ajax_gallery()
    {
        // $cat=335;

        $query_images_args = array(
            'post_type' => 'attachment', 'post_mime_type' =>'image', 'post_status' => 'inherit', 'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'tax_query' => array( array('taxonomy' => 'media_category', 'field' => 'id', 'terms' => $_REQUEST['cat'] ) )
        );

        $query_images = new WP_Query( $query_images_args );

// var_dump($query_images);

        $images = array();
        foreach ( $query_images->posts as $image)
        {
            $images[]=array(
                'url'=> wp_get_attachment_url( $image->ID ),
                'descr'=>$image->post_excerpt ,
                'content'=>$image->post_content);
        }

        echo json_encode($images);
        // var_dump($images);
        die();
    }



?>
