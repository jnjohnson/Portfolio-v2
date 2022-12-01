jQuery('.page-template-template-home nav a, .page-template-template-home footer .menu a').click(function(e) {
    e.preventDefault();
    const href = jQuery(this).attr('href');
    const hash = '#' + href.split('#')[1];
    const section = jQuery(hash);
    history.pushState(null,null,hash);
    if (jQuery(window).width() <= 1040) {
        jQuery('html').animate({scrollTop: section.offset().top - 78}, 800);
    } else {
        jQuery('html').animate({scrollTop: section.offset().top}, 800);
    }
});

jQuery('nav .mobile-nav-button').click(function() {
    jQuery('nav').toggleClass('open');
    jQuery('body').toggleClass('menu-open');
});

jQuery('nav #menu-header a').click(function() {
    if (jQuery(window).width() <= 800) {
        jQuery('nav .mobile-nav-button').click();
    }
});