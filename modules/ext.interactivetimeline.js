
;(function($) {

    $.InteractiveTimeline = function(el, options) {
        var base = this;

        // Access to jQuery and DOM versions of element
        base.$el = $(el);
        base.el = el;

        // Add a reverse reference to the DOM object
        base.$el.data("InteractiveTimeline", base);

        base.init = function(){
            base.options = $.extend({},$.InteractiveTimeline.defaultOptions, options);
        
        };


        // Run initializer
        base.init();
    };

    $.InteractiveTimeline.defaultOptions = {
    };

    $.fn.interactiveTimeline = function(options) {
        return this.each(function(){
            (new $.InteractiveTimeline(this, options));
        });
    };

})(jQuery);