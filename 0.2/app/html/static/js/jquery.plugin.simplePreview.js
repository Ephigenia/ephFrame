/**
 *  Simple Preview
 *  
 *  SimplePreview automaticly creates a toolbar before or after input fields
 *	and creates a preview div before or after the input field so that you'll get
 *	a live preview of the content from the input field. This is cool for editing
 *	blog entries or longer texts that can have simple html-tags in it.
 *  
 *  Usage:
 *  	$('.textarea').simplePreview();
 *  
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 *	@since 2008-11-11
 */
(function($) {

	$.fn.simplePreview = function(configure) {
		$(this).loadCSS(document.WEBROOT + 'static/css/simplePreview.css');
		$(this).loadJS(document.WEBROOT + 'static/js/jquery.plugin.dialog.js');
		$(this).loadJS(document.WEBROOT + 'static/js/jquery.plugin.dialog.js');
		var inputName = $(this).attr('name');
		var previewName = inputName + 'preview';
		// insert preview pange
		$(this).after(
			'<div id="' + previewName + '" class="simplePreview">'+($(this).html())+'</div>'
		);
		// insert toolbar buttons
		var toolbarButtons = {
			bold: { label: 'bold', replaceSelection: '<strong>%s</strong>' },
			italic: { label: 'italic', replaceSelection: '<em>%s</em>' },
			quote: { label: 'quote', replaceSelection: '<q>%s</q>' },
			code: { label: 'code', replaceSelection: '<code>%s</code>' },
			url: { label: 'url', callback: function(config, selection) {
				$('body').dialog.prompt('Add URL', 'Please enter a valid URL here:', 'http://www.', function(url) {
					if (url == '' || url == 'http://www.') return;
					if (selection.text == '') selection.text = url;
					config.target.replaceSelection('<a href="' + url + '">' + selection.text + '</a>');
					
				});
				config.target.trigger('keyup');
			}},
			image: { label: 'image', callback: function(config, selection) {
				$('body').dialog.prompt('Add Image', 'Please enter a valid URL to an image here:', 'http://www.', function(src) {
					if (src == '' || src == 'http://www.') return;
					if (selection.text == '') {
						config.target.replaceSelection('<img src="' + src + '" title="' + selection.text + '" />');
					} else {
						config.target.val(
							config.target.val().substring(0,selection.start) + 
							'<img src="' + src + '" />' +
							config.target.val().substring(selection.start)
						);
					}
					config.target.trigger('keyup');
				});
			}}
		}
		var toolbarClassName = inputName + 'Toolbar';
		$(this).before('<div class="' + toolbarClassName + ' simplePreviewToolbar" />');
		var toolbar = $('.' + toolbarClassName);
		for (i in toolbarButtons) {
			if (typeof(toolbarButtons[i]) !== 'object') continue;
			var btnConfig = toolbarButtons[i];
			btnConfig.target = $(this);
			var btnClassName = inputName + 'Button' + btnConfig.label;
			toolbar.append('<a href="javascript:void(0);" title="' + btnConfig.label + '" class="' + btnClassName + ' ' + btnConfig.label + '"><span>' + btnConfig.label + '</span></a>');
			// add click action to buttons
			$('.' + btnClassName).data('btnConfig', btnConfig);
			$('.' + btnClassName).click(function() {
				var btnConfig = $(this).data('btnConfig');
				var selection = btnConfig.target.getSelection();
				if (btnConfig.replaceSelection) {
					var replaced = btnConfig.replaceSelection.replace(/%s/, selection.text);
					btnConfig.target.replaceSelection(replaced);
				} else if (btnConfig.callback) {
					btnConfig.callback(btnConfig, selection);
				}
				btnConfig.target.trigger('keyup');
			});
		}
		// key listener that updates preview
		$(this).keyup(function() {
			if ($(this).val() !== '') {
				$('#' + previewName).html($(this).val());
				$('#' + previewName).show();
			} else {
				$('#' + previewName).hide();
			}
		});
		$(this).trigger('keyup');
	};
	
})(jQuery);
