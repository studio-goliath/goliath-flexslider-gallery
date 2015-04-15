/**
 * Goliath Flexslider Gallery Settings
 */
(function($) {
    var media = wp.media;

    // Wrap the render() function to append controls.
    media.view.Settings.Gallery = media.view.Settings.Gallery.extend({
        render: function() {
            var $el = this.$el;

            media.view.Settings.prototype.render.apply( this, arguments );

            // Append the type template and update the settings.
            if( media.template( 'gfg-type-settings' ) ){
                $el.append( media.template( 'gfg-type-settings' ) );
                media.gallery.defaults.type = 'default'; // lil hack that lets media know there's a type attribute.
                this.update.apply( this, ['type'] );
            }

            $el.append( media.template( 'gfg-animation-settings' ) );
            this.update.apply( this, ['animation'] );

            // Hide the Columns setting for all types except Default
            $el.find( 'select[name=type]' ).on( 'change', function () {
                var columnSetting = $el.find( 'select[name=columns]' ).closest( 'label.setting' );
                var linkToSetting = $el.find( 'select[data-setting=link]' ).closest( 'label.setting' );
                var flexsliderSetting = $el.find( 'label.setting-flexslider' );

                if ( 'default' === $( this ).val() ) {
                    columnSetting.show();
                    linkToSetting.show();
                } else {
                    columnSetting.hide();
                    linkToSetting.hide();
                }

                if ( 'flexslider' === $( this ).val() ) {
                    flexsliderSetting.show();
                } else {
                    flexsliderSetting.hide();
                }

            } ).change();

            return this;
        }
    });
})(jQuery);