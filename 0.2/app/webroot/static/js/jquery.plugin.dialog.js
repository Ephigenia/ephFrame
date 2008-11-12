/**
 *	GIF-Like Animation
 *	@todo change the name of this plugin to fit 
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 *	@since 2008-11-12
 */
(function($) {
	
	$.fn.dialog = {
		
		dialoges: [],
		
		init: function() {
			$(this).loadCSS(document.WEBROOT + 'static/css/dialog.css');
		},
		
		prompt: function(title, message, value, callback) {
			this.init();
			var divId = 'dialog' + this.dialoges.length;
			var window = '<div id="' + divId + '" class="dialog prompt">%s</div>';
			var content = '<h1>' + title + '</h1>';
			if (message) {
				content += '<p>' + message + '</p>'; 
			}
			if (typeof(value) == 'undefined') {
				value = '';
			}
			content += '<input type="text" name="prompt" class="value" value="' + value + '" /><br />';
			content += '<input type="submit" value="OK" class="submit" />';
			var source = window.replace(/%s/, content);
			$('body').prepend(source);
			var dialog = $('#' + divId);
			dialog.close = function() {
				$(this).fadeOut(400);
			}
			this.dialoges[divId] = dialog;
			$('#' + divId + ' .submit').click(function() {
				if (typeof(callback) == 'function') {
					callback($('#'+ divId + ' input.value').val());
				}
				dialog.close();
			});
			$('#' + divId + ' .value').keyup(function(e) {
				var keyCode = e.keyCode || window.event.keyCode;
				if (keyCode == 27) {
					$(this).close();
				} else if (keyCode == 13 || keyCode == 10) {
					$('#' + divId + ' .submit').trigger('click');
				}
			});
			dialog.centerOnScreen();
			dialog.hide().fadeIn(400);
		},
		
		closeAll: function() {
			for(i in this.dialoges) {
				if (typeof(this.dialoges[i].close) != 'function') continue;
				this.dialoges[i].close();
			}
		}
		
	}
	
})(jQuery);