
/*** CREDITS
This code is based on https://github.com/getgrav/grav-theme-antimatter/blob/develop/js/antimatter.js from Rocket Team's Antimatter theme
 ***/

// The selector to apply the effect
var selector = ".m-navbar";
// The value, when reached, activates the effect
var scrolledAt = 200;

var isTouch = window.DocumentTouch && document instanceof DocumentTouch;
function scrollHeader() {
    // Has scrolled class on header
    var zvalue = $(document).scrollTop();
    if ( zvalue > scrolledAt ) {
        $(selector).addClass("scrolled");
    }
    else {
        $(selector).removeClass("scrolled");
    }

    if (!$(selector).hasClass("transparent") && !$(selector).hasClass("transparent-placeholder")) {
        return;
    }
    if ( zvalue > scrolledAt ) {
        $(selector).removeClass("transparent").addClass("transparent-placeholder");
    }
    else {
        $(selector).removeClass("transparent-placeholder").addClass("transparent");
    }
}

jQuery(document).ready(function($) {

    // ON SCROLL EVENTS
    if (!isTouch){
        $(document).scroll(function() {
            scrollHeader();
        });
    };

    // TOUCH SCROLL
    $(document).on({
        'touchmove': function(e) {
            scrollHeader(); // Replace this with your code.
        }
    });
});
