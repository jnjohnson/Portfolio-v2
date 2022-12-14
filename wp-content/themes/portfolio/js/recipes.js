var interval = 0;

jQuery('#search').submit(function(e) {
    e.preventDefault();
    var data = jQuery('#search').serializeArray();
    var queryIndex = window.location.href.indexOf("?");
    var href;
    var url;
    if (queryIndex != -1) {
        href = window.location.href.substring(0, queryIndex);
    } else {
        href = window.location.href;
    }

    url = new URL(href);
    jQuery.each(data, function (i, val) {
        if (val.value != "") {
            if (!url.searchParams.get(val.name)) {
                url.searchParams.set(val.name, val.value);
            } else {
                const lastValue = url.searchParams.get(val.name);
                url.searchParams.set(val.name, `${lastValue},${val.value}`);
            }
        }
    });

    history.pushState("", document.title, url);

    do_query(url);
});

// AJAX
// - Stops the previous query if one exists
// - Retrieves the HTML from the page generated by the new URL
var last_query;
function do_query(url, append = false) {
    if (last_query) {
        last_query.abort();
    }
    jQuery('.recipes').addClass('loading');
    setTimeout(function() {
        if (append) {
            last_query = jQuery.get(
                url,
                {},
                function (html) {
                    jQuery('.next-page').remove();
                    var start = html.indexOf('<div class="recipes recipes-start">') + '<div class="recipes recipes-start">'.length;
                    var end = html.indexOf('<div class="recipes-end"></div>');
                    var recipes = html.substring(start, end);
                    jQuery('.recipes').append(recipes);
                    loadMoreInterval();
                },
                "html"
            );
        } else {
            jQuery("body,html").animate({
                scrollTop: jQuery('.recipes-start').offset().top - 200
            }, 500);
    
            last_query = jQuery.get(
                url,
                {},
                function (html) {
                    var start = html.indexOf('<div class="recipes recipes-start">') + '<div class="recipes recipes-start">'.length;
                    var end = html.indexOf('<div class="recipes-end"></div>');
                    var recipes = html.substring(start, end);
                    
                    jQuery('.recipes').html(recipes);

                },
                "html"
            );
        }
        jQuery('.recipes').removeClass('loading');
    }, 750);
}

jQuery('label input').change(function() {
    jQuery('#search').submit();
});

function loadMoreInterval() {
    interval = setInterval(function() {
        if (jQuery('.next-page').length != 0) {
            const windowTop = jQuery(document).scrollTop();
            const windowBottom = windowTop + jQuery(window).height();
            const nextPagePos = jQuery('.next-page').offset().top;
    
            if (nextPagePos >= windowTop && nextPagePos <= windowBottom) {
                clearInterval(interval);
                url = jQuery('.next-page').attr('href');
                do_query(url, true);
            }
        }
    }, 300);
}

// FILTER DROPDOWN
jQuery('.filter p').click(function() {
    var height = 0;
    var filter = jQuery(this).parent();
    var div = jQuery(this).siblings('div');
    filter.toggleClass('open');
    if (filter.hasClass('open')) {
        height = div.children().outerHeight(true);
    }

    div.animate({height: height});
});

loadMoreInterval();