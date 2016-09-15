
/* ==============================================
Revolution Slider Fullscreen
=============================================== */
/*
$(document).ready(function(){
    $(window).scroll(function(){
        if ($(window).scrollTop() > 200){
            $('.tp-banner').css('color','#fff').stop().animate({"opacity":".5"},800)
        }
		else if ($(window).scrollTop() < 200){
            $('.tp-banner').css('color','#fff').stop().animate({"opacity":"1"},800)
        }
    });
});
*/
jQuery(document).ready(function() {
    
    $(window).scroll(function(e){
      parallax();
    });

    function parallax(){
      var scrolled = $(window).scrollTop();
      $('.hero').css('top',-(scrolled*0.0515)+'rem');
      $('.op-1,.op-2,.op-3').css('opacity',1-(scrolled*.00110)); 
             
    }; 
			
    jQuery('.tp-banner').show().revolution( {
        dottedOverlay:" ",
        delay:16000,
        startwidth:1170,
        startheight:700,
        hideThumbs:200,
						
        thumbWidth:100,
        thumbHeight:50,
        thumbAmount:5,
						
        navigationType:"none",
        navigationArrows:"solo",
        navigationStyle:"preview4",

        touchenabled:"on",
        onHoverStop:"on",

        swipe_velocity: 0.7,
        swipe_min_touches: 1,
        swipe_max_touches: 1,
        drag_block_vertical: false,

        parallax:"mouse",
        parallaxBgFreeze:"on",
        parallaxLevels:[7,4,3,2,5,4,3,2,1,0],

        keyboardNavigation:"off",

        navigationHAlign:"center",
        navigationVAlign:"bottom",
        navigationHOffset:0,
        navigationVOffset:20,

        soloArrowLeftHalign:"left",
        soloArrowLeftValign:"center",
        soloArrowLeftHOffset:20,
        soloArrowLeftVOffset:0,

        soloArrowRightHalign:"right",
        soloArrowRightValign:"center",
        soloArrowRightHOffset:20,
        soloArrowRightVOffset:0,

        shadow:0,
        fullWidth:"off",
        fullScreen:"on",

        spinner:"spinner4",

        stopLoop:"off",
        stopAfterLoops:-1,
        stopAtSlide:-1,

        shuffle:"off",

        autoHeight:"off",						
        forceFullWidth:"off",						

        hideThumbsOnMobile:"off",
        hideNavDelayOnMobile:1500,						
        hideBulletsOnMobile:"off",
        hideArrowsOnMobile:"off",
        hideThumbsUnderResolution:0,

        hideSliderAtLimit:0,
        hideCaptionAtLimit:0,
        hideAllCaptionAtLilmit:0,
        startWithSlide:0,
        fullScreenOffsetContainer: ".header"	
    });
});

/* ==============================================
Parallax Sections
=============================================== */

$(document).ready(function(){
    $('#home-parallax-fullscreen').parallax("50%", 0.5);
    $('#home-parallax-fullwidth').parallax("50%", 0.5);
    $('.parallax-section-1').parallax("50%", 0.5);
    $('.parallax-section-2').parallax("50%", 0.5);
    $('.parallax-section-3').parallax("50%", 0.5);
    $('.parallax-section-4').parallax("50%", 0.5);
    $('.parallax-section-5').parallax("50%", 0.5);
    $('.parallax-section-6').parallax("50%", 0.5);
    $('.parallax-section-7').parallax("50%", 0.5); 
    $('.parallax-section-8').parallax("50%", 0.5); 
    //.parallax(xPosition, speedFactor, outerHeight) options:
    //xPosition - Horizontal position of the element
    //inertia - speed to move relative to vertical scroll. Example: 0.1 is one tenth the speed of scrolling, 2 is twice the speed of scrolling
    //outerHeight (true/false) - Whether or not jQuery should use it's outerHeight option to determine when a section is in the viewport
});

/* ==============================================
Header Animation
=============================================== */

$(window).scroll(function() {
    if ($(this).scrollTop() > 5){  
        $('nav').addClass("navbar-small");
    }
    else{
        $('nav').removeClass("navbar-small");
    }
});

/* ==============================================
WOW (triggers animation.css on scroll)
=============================================== */

new WOW().init();

/* ==============================================
Smooth scrolling to anchor
=============================================== */

$(function() {
    $('#to-section a,.btn-scroll').bind('click', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: $($anchor.attr('href')).offset().top - 54
        }, 1000, 'easeInOutExpo');
        event.preventDefault();
    });
});

/* ==============================================
Back to Top
=============================================== */

$(window).scroll(function(){
    if($(window).scrollTop() > 400){
        $("#back-to-top").stop().animate({ bottom:'16px' },300,'easeInOutCubic'); 
    } 
    else{
        $("#back-to-top").stop().animate({ bottom:'-50px' },300,'easeInOutCubic'); 
    }
});
$('#back-to-top,#to-top').click(function() {
    $('html, body').animate({ scrollTop: 0}, 1000, 'easeInOutExpo');
    return false;
});
 
/* ==============================================
Team, Portfolio Image Hover
=============================================== */

$(document).ready(function(){
    if (Modernizr.touch) {
        // show the close overlay button
        $(".close-overlay").removeClass("hidden");
        // handle the adding of hover class when clicked
        $(".img").click(function(e){
            if (!$(this).hasClass("hover")) {
                $(this).addClass("hover");
            }
        });
        // handle the closing of the overlay
        $(".close-overlay").click(function(e){
            e.preventDefault();
            e.stopPropagation();
            if ($(this).closest(".img").hasClass("hover")) {
                $(this).closest(".img").removeClass("hover");
            }
        });
    } 
    else {
        // handle the mouseenter functionality
        $(".img").mouseenter(function(){
            $(this).addClass("hover");
        })
        // handle the mouseleave functionality
        .mouseleave(function(){
            $(this).removeClass("hover");
        });
    }
});

// Reinitialize after ajax load
$(document).ajaxComplete(function( event, xhr, settings ) {
  if ( settings.url === "projects/loadMore.html" ) {
        if (Modernizr.touch) {
            $(".close-overlay").removeClass("hidden");
            $(".img").click(function(e){
                if (!$(this).hasClass("hover")) {
                    $(this).addClass("hover");
                }
            });
            $(".close-overlay").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                if ($(this).closest(".img").hasClass("hover")) {
                    $(this).closest(".img").removeClass("hover");
                }
            });
        } 
        else {
            $(".img").mouseenter(function(){
                $(this).addClass("hover");
            })
            .mouseleave(function(){
                $(this).removeClass("hover");
            });
        }
  }
});

$('.cbp-filter-item').click(function () {
    if (Modernizr.touch) {
            $(".close-overlay").removeClass("hidden");
            $(".img").click(function(e){
                if (!$(this).hasClass("hover")) {
                    $(this).addClass("hover");
                }
            });
            $(".close-overlay").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                if ($(this).closest(".img").hasClass("hover")) {
                    $(this).closest(".img").removeClass("hover");
                }
            });
        } 
        else {
            $(".img").mouseenter(function(){
                $(this).addClass("hover");
            })
            .mouseleave(function(){
                $(this).removeClass("hover");
            });
        }
});

/* ==============================================
Owl Slider callings
=============================================== */

$(document).ready(function() {
    
    $("#quote-slider").owlCarousel({
        autoPlay : false,
        singleItem : true,
        pagination: false,
        navigation: false,
    });
    
    $("#owl-testimonials").owlCarousel({
        autoPlay : true,
        singleItem : true,
        pagination: true,
        navigation: false,
    }); 
    
});

// AJAX project slider
$(document).ajaxComplete(function( event, xhr, settings ) {
    if ( settings.url === "projects/project1.html","projects/project2.html","projects/project3.html",
            "projects/project4.html","projects/project5.html","projects/project6.html",
            "projects/project7.html","projects/project8.html","projects/project9.html",
            "projects/project10.html","projects/project11.html","projects/project12.html",
            "projects/project13.html","projects/project14.html","projects/project15.html",
            "projects/project16.html","projects/project17.html","projects/project18.html") {
        $("#project-slider").owlCarousel({
            autoPlay : true,
            singleItem : true,
            pagination: true,
            navigation: false,
        });
    }
});

/* ==============================================
Active Menu Item on Page Scroll
=============================================== */   
    
var sections = $('section')
  , nav = $('nav')
  , nav_height = nav.outerHeight();
 
$(window).on('scroll', function () {
  var cur_pos = $(this).scrollTop();
 
  sections.each(function() {
    var top = $(this).offset().top - nav_height,
        bottom = top + $(this).outerHeight();
 
    if (cur_pos >= top && cur_pos <= bottom) {
      nav.find('a').removeClass('current');
      sections.removeClass('current');
 
      $(this).addClass('current');
      nav.find('a[href="#'+$(this).attr('id')+'"]').addClass('current');
    }
  });
});

/* ==============================================
Auto Close Responsive Navbar on Click
=============================================== */

function close_toggle() {
    if ($(window).width() <= 992) {
      $('.navbar-collapse a').on('click', function(){
          $('.navbar-collapse').collapse('hide');
      });
    }
    else {
     $('.navbar .navbar-default a').off('click');
    }
}
close_toggle();

$(window).resize(close_toggle); 

$(".navbar-collapse").css({ maxHeight: $(window).height() - $(".navbar-header").height() + "px" });


/* ==============================================
Google Maps
=============================================== */

$("#map").gmap3({
    marker:{     
	address:"44 W 66th St, New York, NY", 
	options:{ icon: "img/assets/marker.png"}},
    map:{
    options:{
	styles: [ {
	stylers: [ { "saturation":-90 }, { "lightness": 0 }, { "gamma": 0.0 }]},
	],
	zoom: 13,
	scrollwheel:false,
	draggable: true }
	}
});	

/* ==============================================
Twitter Feed Calling
=============================================== */ 

$('.tweet').twittie({
    dateFormat: '%b. %d, %Y',
    template: '{{tweet}} <div class="date">{{date}}</div>',
    count: 3,
    hideReplies: true
});

/* ==============================================
Animate Progress Bars
=============================================== */

$('.progress-bars').waypoint(function() {
   $('.progress').each(function(){
		$(this).find('.progress-bar').animate({
			width:$(this).attr('data-percent')
		},800);
	});
	
	}, { offset: '95%' 
});

/* ==============================================
Fun Facts Counter Up
=============================================== */

jQuery(document).ready(function($) {
    $('.counter').counterUp({
        delay: 8,
        time: 1400
    });
});

/* ==============================================
Video Section Lightbox Calling
=============================================== */
 /*
(function ($, window, document, undefined) {

    var gridContainer = $('#video-lightbox');
	// init cubeportfolio
    gridContainer.cubeportfolio({
        animationType: 'sequentially',
        gapHorizontal: 0,
        gapVertical: 0,
        gridAdjustment: 'alignCenter',
        caption: 'none',
        displayType: 'bottomToTop',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: ' ',
        lightboxShowCounter: false,
    });     
})(jQuery, window, document);
*/


    jQuery(document).ready( function() {
         jQuery('#video-lightbox').cubeportfolio({
            // options
            gridAdjustment: 'alignCenter',
            lightboxDelegate: '.cbp-lightbox',
            lightboxGallery: true,
            lightboxTitleSrc: ' ',
            lightboxShowCounter: false,
         });
    });

/* ==============================================
Portfolio Fullwidth Calling
=============================================== */

(function($, window, document, undefined) {
    'use strict';

    var gridContainer = $('#grid-container-fullwidth'),
        filtersContainer = $('#filters-container-fullwidth'),
        wrap, filtersCallback;


    /*********************************
     init cubeportfolio
     *********************************/
    gridContainer.cubeportfolio({

        defaultFilter: '*',
        animationType: 'slideDelay',
        gapHorizontal: 15,
        gapVertical: 15,
        gridAdjustment: 'responsive',
        caption: 'zoom',
        displayType: 'lazyLoading',
        displayTypeSpeed: 100,
        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
        // singlePage popup
        singlePageDelegate: '.cbp-singlePage',
        singlePageDeeplinking: true,
        singlePageStickyNavigation: true,
        singlePageCounter: '<div class="cbp-popup-singlePage-counter">{{current}} of {{total}}</div>',
        singlePageCallback: function(url, element) {
            // to update singlePage content use the following method: this.updateSinglePage(yourContent)
            var t = this;
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'html',
                timeout: 5000
            })
                .done(function(result) {
                    t.updateSinglePage(result);
                })
                .fail(function() {
                    t.updateSinglePage("Error! Please refresh the page!");
                });
        },
        // single page inline
        singlePageInlineDelegate: '.cbp-singlePageInline',
        singlePageInlinePosition: 'above',
        singlePageInlineInFocus: true,
        singlePageInlineCallback: function(url, element) {
            // to update singlePage Inline content use the following method: this.updateSinglePageInline(yourContent)
        }
    });

    /*********************************
     add listener for filters
     *********************************/
    if (filtersContainer.hasClass('cbp-l-filters-dropdown')) {
        wrap = filtersContainer.find('.cbp-l-filters-dropdownWrap');
        wrap.on({
            'mouseover.cbp': function() {
                wrap.addClass('cbp-l-filters-dropdownWrap-open');
            },
            'mouseleave.cbp': function() {
                wrap.removeClass('cbp-l-filters-dropdownWrap-open');
            }
        });
        filtersCallback = function(me) {
            wrap.find('.cbp-filter-item').removeClass('cbp-filter-item-active');
            wrap.find('.cbp-l-filters-dropdownHeader').text(me.text());
            me.addClass('cbp-filter-item-active');
            wrap.trigger('mouseleave.cbp');
        };
    } else {
        filtersCallback = function(me) {
            me.addClass('cbp-filter-item-active').siblings().removeClass('cbp-filter-item-active');
        };
    }
    filtersContainer.on('click.cbp', '.cbp-filter-item', function() {
        var me = $(this);
        if (me.hasClass('cbp-filter-item-active')) {
            return;
        }
        // get cubeportfolio data and check if is still animating (reposition) the items.
        if (!$.data(gridContainer[0], 'cubeportfolio').isAnimating) {
            filtersCallback.call(null, me);
        }
        // filter the items
        gridContainer.cubeportfolio('filter', me.data('filter'), function() {});
    });

    /*********************************
     activate counter for filters
     *********************************/
    gridContainer.cubeportfolio('showCounter', filtersContainer.find('.cbp-filter-item'), function() {
        // read from url and change filter active
        var match = /#cbpf=(.*?)([#|?&]|$)/gi.exec(location.href),
            item;
        if (match !== null) {
            item = filtersContainer.find('.cbp-filter-item').filter('[data-filter="' + match[1] + '"]');
            if (item.length) {
                filtersCallback.call(null, item);
            }
        }
    });


    /*********************************
     add listener for load more
     *********************************/
    $('.cbp-l-loadMore-button-link').on('click.cbp', function(e) {
        e.preventDefault();
        var clicks, me = $(this),
            oMsg;
        if (me.hasClass('cbp-l-loadMore-button-stop')) {
            return;
        }
        // get the number of times the loadMore link has been clicked
        clicks = $.data(this, 'numberOfClicks');
        clicks = (clicks) ? ++clicks : 1;
        $.data(this, 'numberOfClicks', clicks);
        // set loading status
        oMsg = me.text();
        me.text('LOADING...');
        // perform ajax request
        $.ajax({
            url: me.attr('href'),
            type: 'GET',
            dataType: 'HTML'
        }).done(function(result) {
            var items, itemsNext;
            // find current container
            items = $(result).filter(function() {
                return $(this).is('div' + '.cbp-loadMore-block' + clicks);
            });
            gridContainer.cubeportfolio('appendItems', items.html(),
                function() {
                    // put the original message back
                    me.text(oMsg);
                    // check if we have more works
                    itemsNext = $(result).filter(function() {
                        return $(this).is('div' + '.cbp-loadMore-block' + (clicks + 1));
                    });

                    if (itemsNext.length === 0) {
                        me.text('NO MORE WORKS');
                        me.addClass('cbp-l-loadMore-button-stop');
                    }
                });
        }).fail(function() {
            // error
        });
    });
})(jQuery, window, document);

/* ==============================================
Contact Form
=============================================== */

jQuery(document).ready(function(){
	$('#contactform').submit(function(){
		var action = $(this).attr('action');
		$("#message").slideUp(250,function() {
            $('#message').hide();
            $('#submit')
                .after('<img src="img/assets/contact-form-loader.gif" class="loader" />')
                .attr('disabled','disabled');
            $.post(action, {
                name: $('#name').val(),
                email: $('#email').val(),
                subject: $('#subject').val(),
                comments: $('#comments').val(),
            },
                function(data){
                    document.getElementById('message').innerHTML = data;
                    $('#message').slideDown(250);
                    $('#contactform img.loader').fadeOut('slow',function(){$(this).remove()});
                    $('#submit').removeAttr('disabled');
                    if(data.match('success') != null) $('#contactform').slideUp(850, 'easeInOutExpo');
                }
            );
		});
		return false;
	});
});

/* ==============================================
Subscribe Form AJAX
=============================================== */

$( document ).on( 'ready', function() {
    $( '#subscribe-form' ).on( 'submit', function( e ) {
        e.preventDefault();
        var $el = $( this ),
            $alert = $el.find( '.form-validation' ),
			$submit = $el.find( 'button' ),
			action = $el.attr( 'action' );
        $submit.button( 'loading' );
        $alert.removeClass( 'alert-danger alert-success' );
        $alert.html( '' );
        $.ajax({
            type     : 'POST',
            url      : action,
            data     : $el.serialize() + '&ajax=1',
            dataType : 'JSON',
            success  : function( response ) {
				if ( response.status == 'error' ) {
                    $alert.html( response.message );
                    $alert.addClass( 'alert-danger' ).fadeIn( 500 );
				} 
				else {
                    $el.trigger( 'reset' );
                    $alert.html( response.message );
                    $alert.addClass( 'alert-success' ).fadeIn( 500 );
				}
				$submit.button( 'reset' );
            },
        })
    });
});

/* ==============================================
Bootstrap Tooltip, Alert, Tabs
=============================================== */

$(function () { $("[data-toggle='tooltip']").tooltip();  
    $(".alert").alert()
});

$(function () {
    var active = true;
    $('#collapse-init').click(function () {
        if (active) {
            active = false;
            $('.panel-collapse').collapse('show');
            $('.panel-title').attr('data-toggle', '');
            $(this).text('Close All');
        } else {
            active = true;
            $('.panel-collapse').collapse('hide');
            $('.panel-title').attr('data-toggle', 'collapse');
            $(this).text('Open All');
        }
    });
    $('#accordion').on('show.bs.collapse', function () {
        if (active) $('#accordion .in').collapse('hide');
    });
});

$('#myTab a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})
