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
			base.config = fetchConfig(base.$el);

			// Fix up options that need to be in Date form
			convertDateOptions(base.config);

			// extract the data from the element
			base.data = buildData( base.$el );

			// Build the new timeline
			base.timeline = new links.Timeline( base.el, base.config );

			// Draw our timeline with the created data and options
			base.timeline.draw( base.data );
		};

		// Run initializer
		base.init();
	};

	/**
	 * Convert the itl-event children of the specified container to an
	 * array of event objects suitable to pass to links.Timeline.
	 *
	 * @param {Object} container The jQuery element containing the event data.
	 * @return {Array} An array of event objects.
	 */
	function buildData( container ) {
		// Get the list of itl-events defined for this timeline container
		var events = container.children( 'div.itl-event' );
		var data = [];

		// Process each event into an object timeline can understand.
		events.each( function( index, elem ) {
						 var jElem = $( elem );
						 // Fetch the elements that contain the start, end, and body if possible
						 var startdate = jElem.children( 'div.itl-start' ).text();
						 var enddate = jElem.children( 'div.itl-end' ).text();
						 var body = jElem.children( 'div.itl-body' ).html();
						 var group = jElem.children( 'div.itl-group' ).html();

						 // Must have a start date and body element.
						 if ( startdate && body ) {
							 var event = { 'start': new Date( startdate ),
										   'content': body,
                                           'group': group
										 };

							 // If an end date has be set, store that too.
							 if ( enddate) {
								 event.end = new Date( enddate )
							 }

							 data.push( event );
						 }
					 });

		return data;
	};

    /** Read the configuration for the timeline from the itl-config div.
     *
     */
    function fetchConfig( element ) {
		var rawconfig = element.children( 'div.itl-config' ).text();

        if ( rawconfig ) {
            return JSON.parse( rawconfig );
        }

        return { };
    }

	/** Given an objet containing Timeline options, convert any options that
	 *  should be Date objects from the string representation used in the
	 *  serialised options into Date objects.
	 *
	 * @param {Object} options The object containing the options to process.
	 */
	function convertDateOptions( options ) {
		// The names of the options that should be Date objects
		var dateOptions = ['min', 'max', 'start','end'];

		// Options are optional, so only process the object if any are set.
		if ( options ) {
			// Convert the arguments if needed
			dateOptions.forEach( function( opt ) {
				if ( options[opt] != null && typeof options[opt] !== "Date" ) {
					options[opt] = new Date( options[opt] );
				}
			});
		}
	};

	// convert all itimeline div elements to timelines.
	$( document ).ready( function() {
		$( '.itimeline' ).each( function() {
			new $.InteractiveTimeline( this );
		});
	});

})( mediaWiki, jQuery );
