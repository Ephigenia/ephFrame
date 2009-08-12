/**
 *	jQuery Slide-Show Plugin, (requires jQuery 1.3.2 or maybe earlier)
 *
 *	This simple slideshow plugin will provide your effect gallery with
 * 	some simple features:
 *
 *	- auto slideshow with repeat and a lot of options
 *	- callback method for slide change action (for your own code)
 *	- different option variables to configure
 *	- change of slide through clicking numbers, next, prev etc and mouse
 *	  movement over the slides
 *	- text over images possible
 *	
 *	learn more about this plugin and other projects on
 *	http://projects.marceleichner.de
 *
 *	@since 2009-06-11
 *	@author Marcel Eichner // love@ephigenia.de
 */
(function($){
	
	$.fn.slideShow = function(options) {
		// multiple elements
		if (this.length > 1) {
			this.each(function() { $(this).slideShow(options)});
			return this;
		}

		// private vars
		this.defaults = {
			start: 		0,		// start index
			interval: 	3,		// interval, autoplay, set to false for no auto-play, in seconds
			repeat: 	true,	// repeat at the end
			transition: {
				mode: 'fade',
				speed: 1000
			},
			slideSize: 	'auto',	// size for slides (used for mouseover and stuff)
			useMouse: 	false	// enable mouse to change images 
		};
		this.options = $.extend({}, this.defaults, options);
		
		// internal vars
		this.numSlides = this.find('.slide').length;
		this.options.start = Math.floor(Math.random()*this.numSlides);
		this.current = this.options.start;
		if (this.current >= this.numSlides) {
			this.current = this.numSlides - 1;
		}
		this.last = false;
		this.elm = $(this);
		this.interval = false;
		this.mouse = {
						x: 0,		// store mouse x relative position on element
						y: 0,		// store mouse y relative position on element
						over: false	// store mouse over boolena value
					};
		
		// init slideshow
		this.init = function() {
			
			// set slide container to correct width and height
			if (this.find('.slides').length) {
				// auto-detect slide size
				if (this.options.slideSize == 'auto') {
					this.options.slideSize = {
						width: this.find('.slide:first img').width(),
						height: this.find('.slide:first img').height()
					};
				}
				// don't set size of slides and slide container if not needed
				if (this.options.slideSize != 'none' && this.options.slideSize != false) {
					this.find('.slides, .slide').css({
						height: this.options.slideSize.height + 'px',
						width: this.options.slideSize.width + 'px',
						overflow: 'hidden'
					});
				}
			}
			
			// set slides to be positioned absolute
			this.find('.slide').css('position','absolute');
			// hide slides if not hidden allready
			this.find('.slide:not(:eq(' + this.current + '))').hide();
			
			// navigation shortcuts
			this.find('.first, .next, .prev, .last, .navigation, .slide, .page, .slides').data('slideShow', this);
			this.find('.first').click(this.first);
			this.find('.next').click(this.next);
			this.find('.prev').click(this.previous);
			this.find('.last').click(this.last);
			
			// init pagínation buttons if available
			this.find('.navigation .page:eq(' + this.current + ')').addClass('selected');
			this.find('.page').click(function() {
				if (!(slideShow = $(this).data('slideShow'))) {
					var slideShow = this;
				}
				slideShow.gotoSlide($(this).html()-1);
			});
			
			// init mouse move handler
			this.find('.slide').mousemove(function(event) {
				var slideShow = $(this).data('slideShow');
				// calculate mouse relative position and store it
				slideShow.mouse.x = Math.abs(event.clientX - $(this).position().left);
				slideShow.mouse.y = Math.abs(event.clientY - $(this).position().top);
				if (slideShow.mouse.x > slideShow.options.slideSize.width) slideShow.mouse.x = slideShow.options.slideSize.width;
				if (slideShow.mouse.y > slideShow.options.slideSize.height) slideShow.mouse.y = slideShow.options.slideSize.height;
				// use mouse vor navigation, calculate new page from mouse position
				if (slideShow.options.useMouse) {
					var index = Math.round((slideShow.numSlides - 1) * slideShow.mouse.x / slideShow.options.slideSize.width);
					slideShow.gotoSlide(index, true);
				}	
			});
			
			// mouse enter handler
			this.find('.slide').mouseenter(function() {
				var slideShow = $(this).data('slideShow');
				slideShow.mouse.over = true;
				slideShow.stopAuto();
			});
			
			// mouse leave handler
			this.find('.slide').mouseleave(function() {
				var slideShow = $(this).data('slideShow');
				slideShow.mouse.over = false;
				slideShow.auto();
			});

			g = this.current;
			this.current = -1;
			this.gotoSlide(g);
			// start interval for auto animation
			if (this.options.interval > 0) {
				this.auto();
			}
			return this;
		};
		
		// public methods
		this.auto = function() {
			if (!(slideShow = $(this).data('slideShow'))) {
				var slideShow = this;
			}
			if (!slideShow.interval && slideShow.options.interval > 0.001) {
				slideShow.interval = window.setInterval(function() {
					slideShow.next();
				}, slideShow.options.interval * 1000);
			}
			return this;
		}
		this.stopAuto = function() {
			if (!(slideShow = $(this).data('slideShow'))) {
				var slideShow = this;
			}
			if (slideShow.interval) {
				window.clearInterval(slideShow.interval);
				slideShow.interval = false;
			}
			return this;
		}
		this.first = function(elm) {
			if (!(slideShow = $(this).data('slideShow'))) {
				var slideShow = this;
			}
			return slideShow.gotoSlide(0);
		};
		this.next = function() {
			if (!(slideShow = $(this).data('slideShow'))) {
				var slideShow = this;
			}
			return slideShow.gotoSlide(slideShow.current + 1);
		};
		this.previous = function() {
			if (!(slideShow = $(this).data('slideShow'))) {
				var slideShow = this;
			}
			return slideShow.gotoSlide(slideShow.current - 1);
		};
		this.last = function() {
			if (!(slideShow = $(this).data('slideShow'))) {
				var slideShow = this;
			}
			return slideShow.gotoSlide(slideShow.numSlides);
		};
		this.gotoSlide = function(index, noanimation) {			
			if (index < 0) {
				index = this.numSlides - 1;
			}
			if (index >= this.numSlides) {
				index = 0;
			}
			if (index === this.current) return this;
			// get slide elements
			var oldSlide = this.find('.slide:eq(' + this.current +')');
			var newSlide = this.find('.slide:eq(' + index +')');
			// callbacks for animation finished
			oldFinished = function () {
				$(this).removeClass('selected');
				if (!(slideShow = $(this).data('slideShow'))) {
					var slideShow = this;
				}
				slideShow.elm.find('.navigation .page:eq(' + slideShow.current + ')').addClass('selected');
				if (!slideShow.mouse.over) {
					slideShow.auto();
				}
			}
			newFinished = function() {
				if (!(slideShow = $(this).data('slideShow'))) {
					var slideShow = this;
				}
				slideShow.elm.find('.navigation .page:not(:eq(' + slideShow.current + '))').removeClass('selected');
				$(this).addClass('selected');
			}
			// get slideshow
			if (!(slideShow = $(this).data('slideShow'))) {
				var slideShow = this;
			}
			slideShow.stopAuto();
			// call callback
			if (typeof(this.options.gotoSlide) == 'function') {
				this.options.gotoSlide(slideShow, index);
			}
			// start transition
			if (noanimation) {
				oldSlide.hide(1, oldFinished);
				newSlide.show(1, newFinished);
			} else {
				if (typeof(this.options.transition.mode) == 'function') {
					this.call(this.options.transition.mode, newSlide, oldSlide);
				} else {
					switch(this.options.transition.mode) {
						default:
						case 'fade':
							oldSlide.fadeOut(this.options.transition.speed, oldFinished);
							newSlide.fadeIn(this.options.transition.speed, newFinished);
							break;
					}
				}
			}
			this.last = this.current;
			this.current = index;
			return this;
		};
		
		return this.init();
	}
	
})(jQuery);