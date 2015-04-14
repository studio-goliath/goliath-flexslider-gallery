<?php
/*
 * Plugin Name: Goliath Flexslider gallery
 * Description: Display post gallery whith flexslider
 * Version: 0.1
 * Author: Studio Goliath
 * Author URI: http://www.studio-goliath.fr
 * License: GPLv2 or later
 */


/**
 *
 * Register Flaxslider CSS and JS
 *
 */
function gfg_register_script(){

    wp_register_style( 'gfg_flexslider_css',  plugins_url( "css/flexslider.css", __FILE__ ) );

    wp_register_script( 'gfg_flexslider_js',  plugins_url( "js/jquery.flexslider-min.js", __FILE__ ), array( 'jquery'), '2.2.2', true );
    wp_register_script( 'gfg_goliath_flexslider_js',  plugins_url( "js/goliath-flexslider.js", __FILE__ ), array( 'jquery', 'gfg_flexslider_js'), '0.1', true );
}

add_action('wp_enqueue_scripts', 'gfg_register_script');


/**
 *
 * Filter the gallery output to display the flexslider
 * @param  array $attr Attributes of the gallery shortcode.
 * @return string
 *
 */
function gfg_post_gallery_filter( $output, $attr ){


    if( isset( $attr['type'] ) && 'flexslider' == $attr['type'] ){

        $atts = shortcode_atts( array(
            'size'       => 'large',
            'include'    => '',
        ), $attr, 'gallery' );

        $attachments = get_posts(
            array(
                'include'           => $atts['include'],
                'post_status'       => 'inherit',
                'post_type'         => 'attachment',
                'post_mime_type'    => 'image',
                'orderby'           => 'post__in',
                'order'             => 'ASC',
                )
            );

        if( $attachments ){

            $output = '<div class="flexslider">';
            $output .= '<ul class="slides">';

            foreach ( $attachments as $key => $attachment) {
                $output .= '<li>' . wp_get_attachment_image( $attachment->ID, $atts['size'] ) . '</li>';
            }
            $output .= '</ul>';
            $output .= '</div>';

            wp_enqueue_style( 'gfg_flexslider_css' );
            wp_enqueue_script( 'gfg_flexslider_js' );
            wp_enqueue_script( 'gfg_goliath_flexslider_js' );
        }

    }

    return $output;

}
add_filter( 'post_gallery', 'gfg_post_gallery_filter', 10, 2 );


/**
 * Outputs a view template which can be used with wp.media.template
 */
function gfg_print_media_templates() {

    // We add the gallery type setting
    $default_gallery_type = apply_filters( 'gfg_default_gallery_type', 'flexslider' );

    $gallery_types = array(
        'default'       => 'Default',
        'flexslider'    => 'Flexslider'
        );

    ?>
    <script type="text/html" id="tmpl-gfg-settings">
        <label class="setting">
            <span><?php _e( 'Type', 'flexslider' ); ?></span>
            <select class="type" name="type" data-setting="type">
                <?php foreach ( $gallery_types as $value => $caption ) : ?>
                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $default_gallery_type ); ?>><?php echo esc_html( $caption ); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </script>
    <?php


}
function gfg_custom_wp_admin_style( $hook ){

    wp_enqueue_script( 'gfg-admin-scripts', plugins_url( 'js/admin-scripts.js', __FILE__ ), array( 'media-views' ), '20121225' );

}


function gfg_add_jetpack_type_gallery( $types ){

    $types['flexslider'] = 'Flexslider';

    return $types;
}

function gfg_add_gallery_setting(){

    if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'tiled-gallery' ) ) {

        add_filter( 'jetpack_gallery_types', function ( $types ){
            $types['flexslider'] = 'Flexslider';
            return $types;
        } );
        add_filter( 'jetpack_default_gallery_type', function (){ return apply_filters( 'gfg_default_gallery_type', 'flexslider' ); } );

    } else {

        add_action( 'wp_enqueue_media', 'gfg_custom_wp_admin_style' );
        add_action( 'print_media_templates', 'gfg_print_media_templates' );

    }
}
add_action( 'plugins_loaded', 'gfg_add_gallery_setting' );
