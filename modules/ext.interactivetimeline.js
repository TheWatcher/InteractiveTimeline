
;(function($) {

    $.InteractiveTimeline = function(el, options, data) {
        var base = this;

        // Access to jQuery and DOM versions of element
        base.$el = $(el);
        base.el = el;

        // Add a reverse reference to the DOM object
        base.$el.data("InteractiveTimeline", base);

        base.init = function(){
            base.options = $.extend({},$.InteractiveTimeline.defaultOptions, options);
            base.data = data;

            console.log("Got options " + base.options + " data " + base.data);
        };

        // Run initializer
        base.init();
    };

    $.InteractiveTimeline.defaultOptions = {
    };

    $.fn.interactiveTimeline = function(options, data) {
        return this.each(function(){
            (new $.InteractiveTimeline(this, options, data));
        });
    };

    $(document).ready(function() {
        $( '.itimeline' ).each(function() {
            (new $.InteractiveTimeline(this) );
        });
    });

})(jQuery);