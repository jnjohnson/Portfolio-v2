async function heroAnimation() {
    var prevScrollPos = 0;
    var scrolling = false;
    var htmlString = '<section class="hero">\r</section>\r<h1>Jimmy Johnson</h1>\r<h2>Web Developer</h2>\r<a class="down-arrow"></a>';
    htmlString = htmlString.replace(/  +/g, '');
    var array = htmlString.split(/\r\n|\r|\n/g);
    var ide = jQuery('.hero .ide-screen');

    jQuery('.hero .down-arrow').click(async function() {
        bypassAnimation();
        if (jQuery(window).width() <= 1040) {
            jQuery('html').animate({scrollTop: jQuery('.about').offset().top - 78}, 800);
        } else {
            jQuery('html').animate({scrollTop: jQuery('.about').offset().top}, 800);
        }
    });

    // Write the section div on screen
    await sleep(2000);
    await outputLines(array, 0, 2);

    // Animate the dark version of the IDE sliding down
    await sleep(100);
    jQuery('.ide-screen').animate({height: '100%'}, 800);
    await sleep(800);

    // Move the cursor to the beginning of the line
    // Insert a new line after line 0
    // Move the cursor into the new row 1
    await sleep(400);
    await moveCursorToPosition(1, 0);
    await sleep(100);
    ide.find('.line').first().after('<div class="line"></div>');
    await moveCursorToRow(1);
    
    // Output the text content to be shown
    await sleep(100);
    await outputLines(array, 2, array.length, true);

    // Move cursor to row 0
    // Move the cursor to just inside the closing carat
    // Output the text for the background style
    await moveCursorToRow(0);
    await moveCursorToPosition(0, array[0].length - 1);
    await typeOutBackgroundStyle();

    await sleep(600);
    jQuery('.hero .hero-image').animate({opacity: 1}, 1000);
    await sleep(2000);
    jQuery('nav').addClass('show');

    async function moveCursorToPosition(line, finish) {
        const cursor = jQuery('.ide-screen .line .cursor').clone();
        const start = jQuery('.ide-screen .line .cursor').index();
        line += 1;
        if (start < finish) {
            for (let i = start; i <= finish; i++) {
                let promise = new Promise((resolve, reject) => {
                    setTimeout(async _ => {
                        jQuery('.ide-screen .line .cursor').remove();
                        jQuery('.ide-screen .line:nth-of-type('+line+') > span:nth-child('+i+')').after(cursor);
                        resolve(1);
                    }, 100);
                });
                await promise;
            }
        } else if (start > finish) {
            for (let i = start; i > finish; i--) {
                let promise = new Promise((resolve, reject) => {
                    setTimeout(async _ => {
                        jQuery('.ide-screen .line .cursor').remove();
                        jQuery('.ide-screen .line:nth-of-type('+line+') > span:nth-child('+i+')').before(cursor);
                        resolve(1);
                    }, 100);
                });
                await promise;
            }
        }
    }
    async function moveCursorToRow(finish) {
        const cursor = jQuery('.ide-screen .line .cursor').clone();
        const space = jQuery('.ide-screen .line .cursor').index();
        const start = jQuery('.ide-screen .line .cursor').parent().index();

        if (start < finish) {
            for (let i = start; i < finish; i++) {
                let promise = new Promise((resolve, reject) => {
                    setTimeout(async _ => {
                        let tempSpace = space;
                        if (i != 1 && i != array.length - 1) {
                            tempSpace -= 1;
                        }
                        if (jQuery('.ide-screen .line:nth-child('+i+') > span').length < tempSpace) {
                            tempSpace = jQuery('.ide-screen .line:nth-child('+i+') > span').length;
                        }
                        jQuery('.ide-screen .line .cursor').remove();
                        if (jQuery('.ide-screen .line:nth-child('+i+') > span').length == 0) {
                            jQuery('.ide-screen .line:nth-child('+i+')').append(cursor);
                        } else {
                            jQuery('.ide-screen .line:nth-child('+i+') > span:nth-child('+tempSpace+')').after(cursor);
                        }
                        resolve(1);
                    }, 750);
                });
                await promise;
            }
        } else if (finish < start) {
            for (let i = start; i > finish; i--) {
                let promise = new Promise((resolve, reject) => {
                    setTimeout(async _ => {
                        let tempSpace = space;
                        if (i != 1 && i != jQuery('.ide-screen .line').length - 1) {
                            tempSpace -= 1;
                        }
                        if (jQuery('.ide-screen .line:nth-child('+i+') > span').length < tempSpace) {
                            tempSpace = jQuery('.ide-screen .line:nth-child('+i+') > span').length;
                        }
                        if (tempSpace == 0) {
                            tempSpace = 1;
                        }
                        jQuery('.ide-screen .line .cursor').remove();
                        if (jQuery('.ide-screen .line:nth-child('+i+') > span').length == 0) {
                            jQuery('.ide-screen .line:nth-child('+i+')').append(cursor);
                        } else {
                            jQuery('.ide-screen .line:nth-child('+i+') > span:nth-child('+tempSpace+')').after(cursor);
                        }
                        resolve(1);
                    }, 1000);
                });
                await promise;
            }
        }
    }
    async function typeOutBackgroundStyle() {
        var string = ' style="background-image: url(../images/hero-gradient.jpg)"';
        var lineArr = string.match(/.{1,1}/g);
        
        await outputChars(lineArr);
    }
    async function outputLines(array, startIndex = 0, stopIndex = array.length, addTabs = false) {
        for (let i = startIndex; i < stopIndex; i++) {
            const row = ide.find('.cursor').parent();
            const cursor = ide.find('.cursor').clone();
            
            if (addTabs) {
                ide.find('.cursor').before('<span class="tab"></span>');
            }
            var lineArr = array[i].match(/.{1,1}/g);
            await outputChars(lineArr);
            if (i === 2) {
                jQuery('#name-desktop, #name-mobile').addClass('show');
                await sleep(2500);
            }
            if (i === 3) {
                jQuery('#web-dev-desktop, #web-dev-mobile').addClass('show');
                await sleep(2500);
            }
            if (i === 4) {
                jQuery('.hero div.down-arrow').addClass('show');
            }
            if (i != stopIndex - 1) {
                row.after('<div class="line"></div>');
                ide.find('.cursor').remove();
                row.next().append(cursor);
            }
        }
    }
    async function outputChars(lineArr) {
        for (let i = 0; i < lineArr.length; i++) {
            let promise = new Promise((resolve, reject) => {
                setTimeout(() => {
                    var span = jQuery('<span></span>').text(lineArr[i]);
                    span.insertBefore(ide.find('.line .cursor'));
                    copyIDE();
                    resolve(1);
                }, 50); // 100
            });
            await promise;
        }
    }
    function copyIDE() {
        var html = jQuery('.ide-screen').html();
        jQuery('.ide-screen-clone').html(html);
    }
}
function bypassAnimation() {
    jQuery('.hero .hero-image').css('opacity', 1);
    jQuery('#name-desktop, #name-mobile').addClass('show');
    jQuery('#web-dev-desktop, #web-dev-mobile').addClass('show');
    jQuery('.hero div.down-arrow').addClass('show');
    jQuery('nav').addClass('show');
}

// pause execution for ms milliseconds
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// init
jQuery(function() {
    heroAnimation();

    jQuery('.show-more').click(function() {
        const button = jQuery(this);
        const thoughts = jQuery(this).parent().siblings('.thoughts');
        var height = 0;

        button.toggleClass('open');

        if (button.hasClass('open')) {
            height = thoughts.find('div').outerHeight(true);
            button.text('Show Less');
        } else {
            button.text('Learn More');
        }
        thoughts.stop().animate({'height': height});
    });

    if (window.location.hash != '') {
        bypassAnimation();
    }
});