/**
 * Script for the Goliath flexslider gallery plugin
 */

jQuery(window).load(function() {

    jQuery('.flexslider').each(function( i, el ){

        var animation = jQuery(el).data('animation') || 'fade';

        jQuery(el).flexslider({
            animation : animation,
            controlNav : false
        });
    });
});