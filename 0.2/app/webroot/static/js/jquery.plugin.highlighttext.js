/**
 *	Highlights all texts on the page with the given style expression and color
 *	@param string tag tag to use for word capsuling
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
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