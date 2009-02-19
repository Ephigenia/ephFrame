
/**
 *	Highlight lines in HTML-Tags
 *	
 *	!! This is not for highlighting search terms in texts !!
 *
 *	This jquery plugin will help you highlight every line with a color
 *	Read the short docu here or check out some more at:
 * 	http://code.moresleep.net/project/highlightlines/
 *	
 *	<code>
 *	// create red background, yellow font, justified highlighted lines in all .highlight tags
 * 	$('.highlight').highlightlines({ backgroundColor: '#ff0000', color: '#ffff00'});
 *	</code>
 * 	
 *	The tag that is used to highlight every word is by default em, so you can
 * 	create a style for the em to style your highlighted text. Even spaces between
 * 	the words are possible.
 *
 *	@param string tag tag to use for word capsuling
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 *	@version 0.1
 */
(function($) {
	$.fn.highlightlines = function(options) {
		
		var defaults = {
			tag: 'span',
			justify: true,	// create justified output or 'flattersatz'
			backgroundColor: false,		// background color to highlight
			color: false, // highlighted text color
			trim: true, // trim whitespace from the words (removing trailing space)
			nl: '<div style="clear: both;"></div>'
		};
		var config = $.extend(defaults, options);
		
		this.each( function (index, elm) { 
		
			var elm = $(elm);
			var targetWidth = elm.width();
			var content = elm.html();

			// get padding with of text em
			var testTag = $('<' + config.tag + '>test</' + config.tag + '>');
			elm.html(testTag);
			var padding = parseInt(testTag.css('padding-left')) + parseInt(testTag.css('padding-right'));
			elm.html('');
			
			// create array of word pieces including tags
			var inTag = false;
			content = content.replace(/\t/g, '').replace(/\s$/g, '').replace(/^\s+/, '');
			var contentLength = content.length;
			
			var word = '';
			var words = Array();
			for (var i = 0; i < contentLength; i++) {
				c = content.substr(i, 1);
				word += c;
				if (c == '<') {
					inTag = true;
				} else if (c == '>') {
					inTag = false;
				}
				if ((c.match(/\s/) && inTag == false) || i == contentLength-1) {
					word = word.replace(/<br>/g, '<br>');
					if (config.trim) {
						word = word.replace(/[ \t]+$/, ''); // trim spaces from the end
					} else {
						word = word.replace(/[ \t]+$/, '&nbsp;'); // trim spaces from the end
					}
					words.push(word);
					word = '';
				}
			}

			// insert each word after an other
			var lineWidth = 0;
			for (var i = 0; i < words.length; i++) {
				word = words[i];
				var wordTag = $('<' + config.tag + ' style="position: relative;float: left;">' + word + '</' + config.tag + '>');
				if (config.backgroundColor) {
					wordTag.css('background-color', config.backgroundColor);
				}
				if (config.color) {
					wordTag.css('color', config.color);
				}
				elm.append(wordTag);
				if (wordTag.css('width') == 'auto') {
					var wordTagWidth = wordTag.width();
				} else {
					var wordTagWidth = parseFloat(wordTag.css('width'));
				}
				if (word.match(/<br>/)) {
					lineWidth = 0;
					wordTagWidth = -padding;
					elm.append(config.nl);
				}
				wordTagWidth += padding;
				lineWidth += wordTagWidth;
				if (lineWidth > targetWidth) {
					lineWidth -= wordTagWidth;
					lastWord = $(elm.find(config.tag)[i-1]);
					lastWordWidth = lastWord.width() - padding;
/*					if (lastWord.css('width') == 'auto') {
						
					} else {
						lastWordWidth = parseFloat(lastWord.css('width'));						
					}*/
					var newWidth = parseFloat(targetWidth - lineWidth + lastWordWidth);
					if (config.justify) {
						lastWord.css('width', newWidth + 'px');
					}
					lineWidth = wordTagWidth;
				}
			}

			elm.append('<div style="clear: both;"></div>');

		}); // highlighted
	}
})(jQuery);
