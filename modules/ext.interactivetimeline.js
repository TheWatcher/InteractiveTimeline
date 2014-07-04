
;(function( mw, $ ) {

    $.InteractiveTimeline = function ( el ) {
        var base = this;

        // Access to jQuery and DOM versions of element
        base.$el = $( el );
        base.el = el;

        // Add a reverse reference to the DOM object
        base.$el.data( "InteractiveTimeline", base );

        base.init = function() {
            base.config = JSON.parse( mw.config.get( base.$el.attr( 'id' ) ) );

            // extract the data from the element
            base.data = buildData( base.$el );

            // Build the new timeline
            base.timeline = new links.Timeline(base.el, base.config);

            // Draw our timeline with the created data and options
            base.timeline.draw(base.data);
        };

        // Run initializer
        base.init();
    };

    function buildData( container ) {
        // Get the list of itl-events defined for this timeline container
        var events = container.children('div.itl-event');
        var data = [];

        // Process each event into an object timeline can understand.
        events.each( function( index, elem ) {
                         var jElem = $(elem);
                         var startdate = jElem.children('div.itl-start').get(0);
                         var enddate = jElem.children('div.itl-end').get(0);
                         var body = jElem.children('div.itl-body').get(0);

                         var event = { 'start': new Date(startdate.innerText),
                                       'content': body.innerHTML
                                     };
                         if(enddate) {
                             event['end'] = new Date(enddate.innerText)
                         }

                         data.push(event);
                     });

        return data;
    };


    $(document).ready(function() {
        $( '.itimeline' ).each( function() {
            (new $.InteractiveTimeline(this) );
        });
    });

})( mediaWiki, jQuery );