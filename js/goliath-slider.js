/**
 * Script for the Goliath slider gallery plugin
 */

jQuery( function( $ ) {

    $('.slick-gallery').slick({
            dots: true,
            adaptiveHeight: true,
            prevArrow:'<i class="icon-arrow-left slick-prev"></i>',
            nextArrow:'<i class="icon-arrow-right slick-next"></i>'
        });

    $(window).on('slickRefresh', function(){

        $('.slick-gallery').slick('setOption','', '', true);

    });


});