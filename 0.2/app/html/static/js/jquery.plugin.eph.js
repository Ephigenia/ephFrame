/**
 *	jQuery Extension/Plugins written by Ephigenia
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 */

(function($) {
	
	$.fn.slideLeft =  function(speed, callback) {
		return this.animate({width: "show"}, speed, callback);
	},
	
	$.fn.slideRight =  function(speed, callback) {
		return this.animate({width: "hide"}, speed, callback);
	},
	
	$.fn.loadedCSSFiles = {},
	$.fn.loadCSS = function (filename) {
		if (!$.fn.loadedCSSFiles[filename]) {
			$('head').append('<link rel="stylesheet" href="' + filename + '" type="text/css" />');
			$.fn.loadedCSSFiles[filename] = true;
		}
	},
	
	$.fn.loadedJSFiles = {},
	$.fn.loadJS = function (filename) {
		if (!$.fn.loadedJSFiles[filename]) {
			$('head').append('<script type="text/javascript" src="' + filename + '"></script>');
			$.fn.loadedJSFiles[filename] = true;
		}
	},
	
	$.fn.centerOnScreen = function() {
		r = $(this);
		setTimeout(function() {
			if (BROWSER == 'IE') {
				var posY = document.body.scrollTop;
			} else {
				var posY = window.pageYOffset;
			}
			r.css('position', 'absolute')
				.css('top', (Screen.height / 2 - r.outerHeight() / 2 + posY) + 'px')
				.css('left', (Screen.width / 2 - r.outerWidth() / 2) + 'px');
		}, 100);
	}
	
})(jQuery);


/**
 *	Screen helper class
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 */
var Screen = {
	width: 0,
	height: 0,
	detectSize: function() {
		if (typeof(window.innerWidth) == 'number') {
			this.width = window.innerWidth;
			this.height = window.innerHeight;
		} else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
			this.width = document.documentElement.clientWidth;
			this.height = document.documentElement.clientHeight;
		} else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
			this.width = document.body.clientWidth;
			this.height = document.body.clientHeight;
		}
		return new Array(this.width, this.height);
	}
}
$(document).ready(function() {
	Screen.detectSize();
});
