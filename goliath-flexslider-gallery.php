<?php
/*
 * Plugin Name: Goliath Flexslider gallery
 * Description: Display post gallery whith slider
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

    wp_register_style( 'gfg_slider_css',  plugins_url( "css/slick.css", __FILE__ ) );
    wp_register_script( 'gfg_slider_js',  plugins_url( "js/slick.min.js", __FILE__ ), array( 'jquery'), '1.8.1', true );
    wp_register_script( 'gfg_goliath_slider_js',  plugins_url( "js/goliath-slider.js", __FILE__ ), array( 'jquery', 'gfg_slider_js'), '0.2', true );
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
            'animation'  => 'fade',
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

            $slick_option = array(
                'dots'              => true,
                'adaptiveHeight'    => true,
                'fade'              => $atts['animation'] === 'fade'
            );

            $slick_option = apply_filters( 'gfg_gallery_options', $slick_option, $atts );

            $slick_option = wp_json_encode( $slick_option );

            $output .= '<div class="slick-gallery" data-slick="' . esc_attr( $slick_option ) .'">';

            foreach ( $attachments as $key => $attachment) {

                // Has caption
                if( ! empty ( $attachment->post_excerpt ) ){
                    $output .= '<figure class="gallery-item">';
                    $output .= wp_get_attachment_image( $attachment->ID, $atts['size'] ) ;
                    $output .= "<figcaption class='gallery-caption wp-caption-text'>{$attachment->post_excerpt}</figcaption>";
                    $output .= '</figure>';

                } else {
                    $output .= '<div class="gallery-item">';
                    $output .= wp_get_attachment_image( $attachment->ID, $atts['size'] ) ;
                    $output .= '</div>';

                }

            }
            $output .= '</div>';

            wp_enqueue_style( 'gfg_slider_css' );
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
        'default'   => __( 'Default', 'goliath-flexslider-gallery' ),
        'slider'    => __( 'Slider', 'goliath-flexslider-gallery' )
        );

    ?>
    <script type="text/html" id="tmpl-gfg-type-settings">
        <label class="setting">
            <span><?php _e( 'Type', 'goliath-flexslider-gallery' ); ?></span>
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
            <span><?php esc_attr_e( 'Animation', 'goliath-flexslider-gallery' ); ?></span>
            <select class="type" name="animation" data-setting="animation">

                <option value="fade" selected="selected"><?php esc_attr_e( 'Fade', 'goliath-flexslider-gallery' ); ?></option>
                <option value="slide" ><?php esc_attr_e( 'Slide', 'goliath-flexslider-gallery' ); ?></option>

            </select>
        </label>
    </script>
    <?php
}
add_action( 'print_media_templates', 'gfg_print_media_templates' );


function gfg_add_jetpack_type_gallery( $types ){

    $types['slider'] = __( 'Slider', 'goliath-flexslider-gallery' );

    return $types;
}

function gfg_add_gallery_setting(){

    if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'tiled-gallery' ) ) {

        add_filter( 'jetpack_gallery_types', function ( $types ){
            $types['slider'] = 'Slider';
            return $types;
        } );
        add_filter( 'jetpack_default_gallery_type', function (){ return apply_filters( 'gfg_default_gallery_type', 'slider' ); } );

    } else {

        add_action( 'print_media_templates', 'gfg_print_media_temple_type_gallery' );

    }
}
add_action( 'plugins_loaded', 'gfg_add_gallery_setting' );
