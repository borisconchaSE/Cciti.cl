jQuery.fn.animateWidth = function(action, width, fnBefore, fnComplete) {

    if (action=="show" || action=="hide") {

        collapsed = $(this).attr('data-collapsed');
        element = $(this);

        if (action=="show" && (!(collapsed) || collapsed==1)) {
            
            if (fnBefore) {
                fnBefore();
            }

            // SHOW
            // Resetear primero
            $(this).addClass('hide');
            $(this).css('width', '0px');
            $(this).removeClass('hide');
            $(this).attr('data-collapsed', 0);

            $(this).animate({width: width}, function() {
                if (fnComplete) {
                    fnComplete();
                }
            });

        } 
        else if (action == "hide" && collapsed==0) {
            // HIDE
            // Resetear primero
            $(this).removeClass('hide');
            $(this).css('width', width);
            $(this).attr('data-collapsed', 1);

            $(this).animate(
                {width: '0px'}, 
                400,
                function() {
                    $(element).addClass('hide');

                    if (fnComplete) {
                        fnComplete();
                    }
                }
            );
        }
    }
}

jQuery.fn.animateHeight = function(action, height, fnBefore, fnComplete) {

    if (action=="show" || action=="hide") {

        collapsed = $(this).attr('data-collapsed');
        element = $(this);

        if (action=="show" && (!(collapsed) || collapsed==1)) {
            
            if (fnBefore) {
                fnBefore();
            }

            // SHOW
            // Resetear primero
            $(this).addClass('hide');
            $(this).css('height', '0px');
            $(this).removeClass('hide');
            $(this).attr('data-collapsed', 0);

            $(this).animate({height: height}, function() {
                if (fnComplete) {
                    fnComplete();
                }
            });

        } 
        else if (action == "hide" && collapsed==0) {
            // HIDE
            // Resetear primero
            $(this).removeClass('hide');
            $(this).css('height', height);
            $(this).attr('data-collapsed', 1);

            $(this).animate(
                {height: '0px'}, 
                400,
                function() {
                    $(element).addClass('hide');

                    if (fnComplete) {
                        fnComplete();
                    }
                }
            );
        }
    }
}

jQuery.fn.animateRotate = function(action, degrees, fnComplete) {

    if (action=="clockwise") {
        $(this).animate(
            { deg: degrees },
            {              
              step: function(now) {
                $(this).css({ transform: 'rotate(' + now + 'deg)' });
              }
            }
          );
    }
    else if(action=="counterclockwise") {

    }
}

jQuery.fn.changeBlur = function(ammount) {

    var filterValue;
    
    if (ammount > 0) {
        filterValue = "blur(" + ammount + "px)";
    }
    else {
        filterValue = "";
    }

    $(this).css('-webkit-filter', filterValue);
    $(this).css('-moz-filter', filterValue);
    $(this).css('-o-filter', filterValue);
    $(this).css('-ms-filter', filterValue);
    $(this).css('filter', filterValue);    

}


jQuery.fn.shake = function(params) {
    // Default values
    if (!params)
        var params = { interval : 100, long: true };

    if (!params.interval) params.interval = 100;
    if (!params.long) params.long = true;

    $(this).animate({
        'margin-left': '-=5px',
        'margin-right': '+=5px'
    }, params.interval, function() {
        $(this).animate({
            'margin-left': '+=5px',
            'margin-right': '-=5px'
        }, params.interval, function() {
            $(this).animate({
                'margin-left': '-=5px',
                'margin-right': '+=5px'
            }, params.interval, function() {
                $(this).animate({
                    'margin-left': '+=5px',
                    'margin-right': '-=5px'
                }, params.interval, function() {   
                    if (params.long) {
                        $(this).animate({
                            'margin-left': '-=5px',
                            'margin-right': '+=5px'
                        }, params.interval, function() {
                            $(this).animate({
                                'margin-left': '+=5px',
                                'margin-right': '-=5px'
                            }, params.interval, function() {
                                
                            });
                        });
                    }
                });
            });
        });
    });
}

function cssprop($e, id) {
    return parseInt($e.css(id), 10);
}

function slideSwap($set1, $set2, onfinish) {

    //$elem.append(infoString($elem));
    //$after.append(infoString($after));   
    var $set3 = $set2.last().nextAll();    
    
    var mb_prev = cssprop($set1.first().prev(), "margin-bottom");
    if (isNaN(mb_prev)) mb_prev = 0;
    var mt_next = cssprop($set2.last().next(), "margin-top");
    if (isNaN(mt_next)) mt_next = 0;
    
    var mt_1 = cssprop($set1.first(), "margin-top");
    var mb_1 = cssprop($set1.last(), "margin-bottom");
    var mt_2 = cssprop($set2.first(), "margin-top");
    var mb_2 = cssprop($set2.last(), "margin-bottom");
    
    var h1 = $set1.last().offset().top + $set1.last().outerHeight() - $set1.first().offset().top;
    var h2 = $set2.last().offset().top + $set2.last().outerHeight() - $set2.first().offset().top;
    
    move1 = h2 + Math.max(mb_2, mt_1) + Math.max(mb_prev, mt_2) - Math.max(mb_prev, mt_1);
    move2 = -h1 - Math.max(mb_1, mt_2) - Math.max(mb_prev, mt_1) + Math.max(mb_prev, mt_2);
    move3 = move1 + $set1.first().offset().top + h1 - $set2.first().offset().top - h2 + 
        Math.max(mb_1,mt_next) - Math.max(mb_2,mt_next);
    
    // let's move stuff
    $set1.css('position', 'relative');
    $set2.css('position', 'relative');
    $set3.css('position', 'relative');    
    $set1.animate({'top': move1}, {duration: 1000});
    $set3.animate({'top': move3}, {duration: 500});
    $set2.animate({'top': move2}, {duration: 1000, complete: function() {
            // rearrange the DOM and restore positioning when we're done moving          
            $set1.insertAfter($set2.last())
            $set1.css({'position': 'static', 'top': 0});
            $set2.css({'position': 'static', 'top': 0});
            $set3.css({'position': 'static', 'top': 0});

            if (onfinish != null) {
                onfinish();
            }
        }
    });   
    
}