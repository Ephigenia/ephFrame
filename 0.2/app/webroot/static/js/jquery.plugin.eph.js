/**
 *	jQuery Extension/Plugins written by Ephigenia
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 */

jQuery.fn.slideLeft =  function(speed, callback) {
	return this.animate({width: "show"}, speed, callback);
};
jQuery.fn.slideRight =  function(speed, callback) {
	return this.animate({width: "hide"}, speed, callback);
};

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

/**
 *	Highlights all texts on the page with the given style expression and color
 *	@param string tag tag to use for word capsuling
 */
jQuery.fn.highlightText = function(tag) {
	if (tag == undefined) {
		var tag = 'em';
	}
	$(this).each(function(index, elm) {
		var elm = $(elm);
		var words = elm.html().match(/([a-z0-9\xC0-\xEF\u201E\u201C\x25&_:+\\\/"'“„€]+)((&shy;|-|\.|\!|\?|,|;|\s)+)?/gi).slice(1);
		// remove empty
		words = words.filter('');
		if (window.debug) window.debug.trace(words);
		var targetWidth = elm.width();
		var lineWidth = 0;
		elm.css('background-color', 'transparent');
		elm.css('padding', '0px');
		// get padding with of text em
		var testTag = $('<' + tag + '>test</' + tag + '>');
		elm.html(testTag);
		var padding = parseInt(testTag.css('padding-left')) + parseInt(testTag.css('padding-right'));
		elm.html('');
		// insert each word after an other
		for (var i = 0; i < words.length; i++) {
			word = words[i];
			word = word.replace(/[\s]/, '');
			var wordTag = $('<' + tag + '>' + word + '</' + tag + '>');
			elm.append(wordTag);
			if (wordTag.css('width') == 'auto') {
				var wordTagWidth = wordTag.width();
			} else {
				var wordTagWidth = parseFloat(wordTag.css('width'));
			}
			wordTagWidth += padding;
			lineWidth += wordTagWidth;
			if (lineWidth > targetWidth) {
				lineWidth -= wordTagWidth;
				lastWord = $(elm.find(tag)[i-1]);
				//lastWord.css('background-color', '#a2a2a2');
				if (lastWord.css('width') == 'auto') {
					var lastWordWidth = lastWord.width() - padding;
				} else {
					var lastWordWidth = parseFloat(lastWord.css('width'));
				}
				var newWidth = targetWidth - lineWidth + lastWordWidth;
				lastWord.css('width', newWidth + 'px');
				lineWidth = wordTagWidth;
			}
		}
		elm.append('<div class="c"></div>');
	}); // highlighted
};