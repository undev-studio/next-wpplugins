<?php

/**
 * function to return a custom field value.
 */
function hreflang_get_custom_field( $value )
{
    global $post;

    return get_post_meta( $post->ID, $value, true );
    // if ( !empty( $custom_field ) )
        // return is_array( $custom_field ) ? stripslashes_deep( $custom_field ) : stripslashes( wp_kses_decode_entities( $custom_field ) );

    // return false;
}

function insertHrefLang($post)
{
    
    $lang=get_post_meta( $post->ID, 'hreflang_lang', true );
    $country=get_post_meta( $post->ID, 'hreflang_country', true );
    $url=get_post_meta( $post->ID, 'hreflang_url', true );

    $num=0;
    $hrefurl=hreflang_get_custom_field('hreflang_url');

    if(isset($hrefurl) && is_array($hrefurl))
    {
        $num=sizeof($hrefurl);
    }
    
    for($i=0;$i<$num+1;$i++)
    {
        $str=$lang[$i];
        if($country[$i]!='')
        {
            $str.='-';
            $str.=$country[$i];
        }
        if(isset($url[$i])) echo '<link rel="alternate" hreflang="'.$str.'" href="'.$url[$i].'" />'."\n";
    }

}

/**
 * Register the Meta box
 */
function hreflang_add_custom_meta_box()
{
    add_meta_box( 'hreflang-meta-box', __( 'hreflang', 'hreflang' ), 'hrefLangMB_output', 'post', 'normal', 'high' );
    add_meta_box( 'hreflang-meta-box', __( 'hreflang', 'hreflang' ), 'hrefLangMB_output', 'page', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'hreflang_add_custom_meta_box' );


function hreflang_addLine($which)
{
    $countries=Array( 'x-default','at','de','fr','be','nl','pl','gb','en');

    echo '<p>';
    echo '    Sprache:';
    echo '    <select name="hreflang_lang[]">';

    foreach ($countries as $key => $value)
    {
        $sel='';
        if(hreflang_get_custom_field( 'hreflang_lang' )[$which]==$value)$sel="selected";
        echo '<option '.$sel.' value="'.$value.'">'.$value.'</option>';
    }
    echo '    </select>';

    echo '    Land:';
    echo '    <select name="hreflang_country[]">';
    echo '        <option value="">-</option>';

    $count=0;
    foreach ($countries as $key => $value)
    {
        if($count!=0)
        {
            $sel='';
            if(hreflang_get_custom_field( 'hreflang_country' )[$which]==$value)$sel="selected";
            echo '<option '.$sel.' value="'.$value.'">'.$value.'</option>';
        }
        $count++;
    }

    echo '    </select>';

    echo '    <label for="hreflang_url">URL: </label>';
    echo '    <input type="text" name="hreflang_url[]" id="hreflang_url" value="'.hreflang_get_custom_field( 'hreflang_url' )[$which].'" size="50" />';
    echo '</p>';

}

/**
 * Output the Meta box
 */
function hrefLangMB_output( $post )
{
    
    wp_nonce_field( 'my_hrefLangMB_nonce', 'hrefLangMB_nonce' );  // create a nonce field


    $num=sizeof(hreflang_get_custom_field('hreflang_url'));

    
    for($i=0;$i<$num+1;$i++)
    {
        if($i==$num) echo '<b>Create new:</b><br/>';
        hreflang_addLine($i);
    }

    echo '<input name="save" type="submit" class="button button-primary button-large" id="publish" value="Add">';
    
}


/**
 * Save the Meta box values
 */
function hrefLangMB_save( $post_id )
{
    // Stop the script when doing autosave
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // Verify the nonce. If insn't there, stop the script
    if( !isset( $_POST['hrefLangMB_nonce'] ) || !wp_verify_nonce( $_POST['hrefLangMB_nonce'], 'my_hrefLangMB_nonce' ) ) return;

    // Stop the script if the user does not have edit permissions
    if( !current_user_can( 'edit_post', get_the_id() ) ) return;

    
    // remove empty strings from arrays...
    $_POST['hreflang_url']=array_filter($_POST['hreflang_url']);
    $_POST['hreflang_country']=array_filter($_POST['hreflang_country']);
    $_POST['hreflang_lang']=array_filter($_POST['hreflang_lang']);

    // Save the textfield
    if( isset( $_POST['hreflang_url'] ) ) update_post_meta( $post_id, 'hreflang_url',  $_POST['hreflang_url']  );
    if( isset( $_POST['hreflang_country'] ) ) update_post_meta( $post_id, 'hreflang_country',  $_POST['hreflang_country']  );
    if( isset( $_POST['hreflang_lang'] ) ) update_post_meta( $post_id, 'hreflang_lang',  $_POST['hreflang_lang']  );



}
add_action( 'save_post', 'hrefLangMB_save' );


