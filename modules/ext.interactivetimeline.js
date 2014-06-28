
;(function(mw, $) {

    $.InteractiveTimeline = function(el) {
        var base = this;

        // Access to jQuery and DOM versions of element
        base.$el = $(el);
        base.el = el;

        // Add a reverse reference to the DOM object
        base.$el.data("InteractiveTimeline", base);

        base.init = function() {
            base.data = JSON.parse(mw.config.get(base.$el.attr('id')));

            console.log("Got options " + base.data.json);
        };

        // Run initializer
        base.init();
    };

    $(document).ready(function() {
        $( '.itimeline' ).each(function() {
            (new $.InteractiveTimeline(this) );
        });
    });

})(mediaWiki, jQuery);