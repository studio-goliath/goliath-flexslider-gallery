/**
 * Script for the Goliath slider gallery plugin
 */

jQuery(window).load(function() {

    jQuery('.slick-gallery').slick({
            dots: true,
            adaptiveHeight: true,
            prevArrow:'<i class="icon-arrow-left"></i>',
            nextArrow:'<i class="icon-arrow-right"></i>',
        });
});