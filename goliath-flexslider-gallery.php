<?php
/*
 * Plugin Name: Goliath slider gallery
 * Description: Display post gallery whith slider
 * Version: 0.1
 * Author: Studio Goliath
 * Author URI: http://www.studio-goliath.fr
 * License: GPLv2 or later
 */


/**
 *
 * Register Slider CSS and JS
 *
 */
function gfg_register_script(){

    wp_register_style( 'gfg_slider_css',  plugins_url( "css/slick.css", __FILE__ ) );
    wp_register_script( 'gfg_slider_js',  plugins_url( "js/slick.min.js", __FILE__ ), array( 'jquery'), '3.0', true );
    wp_register_script( 'gfg_goliath_slider_js',  plugins_url( "js/goliath-slider.js", __FILE__ ), array( 'jquery', 'gfg_slider_js'), '0.1', true );
}

add_action('wp_enqueue_scripts', 'gfg_register_script');


/**
 *
 * Filter the gallery output to display the slider
 * @param  array $attr Attributes of the gallery shortcode.
 * @return string
 *
 */
function gfg_post_gallery_filter( $output, $attr ){


    if( isset( $attr['type'] ) && 'slider' == $attr['type'] ){

        $atts = shortcode_atts( array(
            'size'       => 'large',
            'include'    => '',
            'animation'  => 'slide',
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

            if('fade'== $atts['animation']){
                $output .= "<ul class='slick-gallery' data-slick='{\"fade\": true }'>";
            }else{
                $output .= "<ul class='slick-gallery'>";
            }

            foreach ( $attachments as $key => $attachment) {
                $output .= '<li class="gallery-item">';
                $output .= wp_get_attachment_image( $attachment->ID, $atts['size'] ) ;

                if( ! empty ( $attachment->post_excerpt ) ){
                    $output .= "<p class='gallery-caption wp-caption-text'>{$attachment->post_excerpt}</p>";
                }

                $output .= '</li>';
            }
            $output .= '</ul>';

            wp_enqueue_style( 'gfg_slider_css' );
            wp_enqueue_script( 'gfg_slider_js' );
            wp_enqueue_script( 'gfg_goliath_slider_js' );
        }

    }

    return $output;

}
add_filter( 'post_gallery', 'gfg_post_gallery_filter', 10, 2 );


/**
 * Outputs a view template which can be used with wp.media.template
 */
function gfg_print_media_temple_type_gallery() {

    // We add the gallery type setting
    $default_gallery_type = apply_filters( 'gfg_default_gallery_type', 'slider' );

    $gallery_types = array(
        'default'       => 'Default',
        'slider'    => 'Slider'
        );

    ?>
    <script type="text/html" id="tmpl-gfg-type-settings">
        <label class="setting">
            <span><?php _e( 'Type', 'slider' ); ?></span>
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

    wp_enqueue_script( 'gfg-admin-scripts', plugins_url( 'js/admin-scripts.js', __FILE__ ), array( 'media-views' ), '20160909' );

}
add_action( 'wp_enqueue_media', 'gfg_custom_wp_admin_style' );



function gfg_print_media_templates(){
    ?>
    <script type="text/html" id="tmpl-gfg-animation-settings">
        <label class="setting setting-slider">
            <span><?php _e( 'Animation', 'slider' ); ?></span>
            <select class="type" name="animation" data-setting="animation">

                <option value="fade" selected="selected">Fade</option>
                <option value="slide" >Slide</option>

            </select>
        </label>
    </script>
    <?php
}
add_action( 'print_media_templates', 'gfg_print_media_templates' );


function gfg_add_jetpack_type_gallery( $types ){

    $types['slider'] = 'slider';

    return $types;
}

function gfg_add_gallery_setting(){

    if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'tiled-gallery' ) ) {

        add_filter( 'jetpack_gallery_types', function ( $types ){
            $types['slider'] = 'slider';
            return $types;
        } );
        add_filter( 'jetpack_default_gallery_type', function (){ return apply_filters( 'gfg_default_gallery_type', 'slider' ); } );

    } else {

        add_action( 'print_media_templates', 'gfg_print_media_temple_type_gallery' );

    }
}
add_action( 'plugins_loaded', 'gfg_add_gallery_setting' );
