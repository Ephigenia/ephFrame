/**
 *	jQuery Extensions creating dialog-like windows
 *
 *	This class can help you making your web-application look fancy better!
 *	It will create cool looking windows with buttons and inputs in it. No need
 *	for ugly OS-Depending Confirmation / Prompt Windows.
 *
 *	Example Usage of Prompt
 *	<code>
 *	
 *	</code>
 *	
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
		
		createDialog: function(title, content) {
			var window = '<div id="' + divId + '" class="dialog prompt">%s</div>';
			var divId = 'dialog' + this.dialoges.length;
			var source = window.replace(/%s/, content);
			$('body').prepend(source);
			var dialog = $('#' + divId);
			dialog.close = function() {
				$(this).fadeOut(400);
			}
			this.dialoges[divId] = dialog;
		},
		
		prompt: function(title, message, value, callback) {
			this.init();
			var content = '<h1>' + title + '</h1>';
			if (message) content += '<p>' + message + '</p>'; 
			if (typeof(value) == 'undefined') value = '';
			content += '<input type="text" name="prompt" class="value" value="' + value + '" /><br />';
			content += '<input type="submit" value="OK" class="submit" />';
			dialog = createDialog(title, content);
			$('.submit', dialog).click(function() {
				if (typeof(callback) == 'function') {
					callback($('input.value', dialog).val());
				}
				dialog.close();
			});
			$('.value', dialog).keyup(function(e) {
				var keyCode = e.keyCode || window.event.keyCode;
				if (keyCode == 27) {
					$(this).close();
				} else if (keyCode == 13 || keyCode == 10) {
					$('.submit', dialog).trigger('click');
				}
			});
			dialog.centerOnScreen();
			dialog.hide().fadeIn(400);
		},
		
		confirm: function(title, message, callback) {
			this.init();
			dialog = this.createDialog(title, message);
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