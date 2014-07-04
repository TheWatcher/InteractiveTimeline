/**
 * Main javascript file for the Interactive Timeline extension. This file
 * is needed to instantiate links.Timeline objects wherever itimeline
 * divs are found on a page, converting the list of events in the div
 * into a format Timeline can understand.
 *
 * @author Chris Page <chris@starforge.co.uk>
 */
// Note the leading ; is deliberate.
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

    /**
     * Convert the itl-event children of the specified container to an
     * array of event objects suitable to pass to links.Timeline.
     *
     * @param container The jQuery element containing the event data.
     * @return An array of event objects.
     */
    function buildData( container ) {
        // Get the list of itl-events defined for this timeline container
        var events = container.children('div.itl-event');
        var data = [];

        // Process each event into an object timeline can understand.
        events.each( function( index, elem ) {
                         var jElem = $(elem);
                         // Fetch the elements that contain the start, end, and body if possible
                         var startdate = jElem.children('div.itl-start').get(0);
                         var enddate = jElem.children('div.itl-end').get(0);
                         var body = jElem.children('div.itl-body').get(0);

                         // Must have a start date and body element.
                         if ( startdate && body ) {
                             var event = { 'start': new Date(startdate.innerText),
                                           'content': body.innerHTML
                                         };

                             // If an end date has be set, store that too.
                             if(enddate) {
                                 event['end'] = new Date(enddate.innerText)
                             }

                             data.push(event);
                         }
                     });

        return data;
    };

    // convert all itimeline div elements to timelines.
    $(document).ready(function() {
        $( '.itimeline' ).each( function() {
            (new $.InteractiveTimeline(this) );
        });
    });

})( mediaWiki, jQuery );