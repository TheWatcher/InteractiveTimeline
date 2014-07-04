
;(function( mw, $ ) {

    $.InteractiveTimeline = function( el ) {
        var base = this;

        // Access to jQuery and DOM versions of element
        base.$el = $( el );
        base.el = el;

        // Add a reverse reference to the DOM object
        base.$el.data( "InteractiveTimeline", base );

        base.init = function() {
            base.config = JSON.parse( mw.config.get( base.$el.attr( 'id' ) ) );

            //
            base.data = buildData( base.$el );
        };

        // Run initializer
        base.init();
    };

    function buildData( container ) {
        var events = container.children('div.itl-event');
        var data = { };

        events.each( function( index, elem ) {
            var start = elem.children('div.itl-start')[0];

                         });
    };


    $(document).ready(function() {
        $( '.itimeline' ).each( function() {
            (new $.InteractiveTimeline(this) );
        });
    });

})( mediaWiki, jQuery );