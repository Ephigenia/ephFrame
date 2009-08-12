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

	$.fn.simplePreview = function(options) {
		
		// default simplePreview configuration
		var defaults = {
			showPreview: false,
			buttons: ['bold', 'italic', 'quote', 'url', 'image' ]
		};
		// default button configuration
		var possibleButtons = {
			bold: { label: 'bold', replaceSelection: '<strong>%s</strong>' },
			italic: { label: 'italic', replaceSelection: '<em>%s</em>' },
			quote: { label: 'quote', replaceSelection: '<q>%s</q>' },
			code: { label: 'code', replaceSelection: '<code>%s</code>' },
			
			// url replacement
			url: { label: 'url', callback: function(config, selection) {
					window.DialogManager.create('Prompt', {
						title: 'Bitte eingeben',
						content: 'Bitte geben Sie eine gültige URL ein:',
						value: 'http://',
						width: 640,
						callback: function(dialog, url) {
							dialog.close();
							if (url == '' || url == 'http://www.' || url == 'http://') return;
							if (selection.text == '') selection.text = url;
							var text = config.target.val();
							config.target.val(text.substr(0, selection.start) + '<a href="' + url + '" rel="external" title="' + selection.text + '">' + selection.text + '</a>' + text.substr(selection.end));
						}
					});
					config.target.trigger('keyup');
				} // function
			},
			
			// image insertion
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
		
		var config = $.extend(defaults, options);
		
		var usedButtons = {};
		if (typeof(config.buttons) == 'string') {
			config.buttons = config.buttons.split(',')
		}
		for (i in config.buttons) {
			var buttonName = config.buttons[i];
			if (typeof(possibleButtons[buttonName]) == 'object') {
				usedButtons[buttonName] = possibleButtons[buttonName];
			} else {
				usedButtons[i] = buttonName;
			}
		}
		
		var inputName = $(this).attr('name');

		// TOOLBAR
		// --------
		if (typeof(usedButtons) == 'object') {
			var toolbarClassName = inputName + 'Toolbar';
			var toolbar = $('<div class="' + toolbarClassName + ' simplePreviewToolbar" />');
			$(this).before(toolbar);
			for (i in usedButtons) {
				var btnConfig = usedButtons[i];
				if (typeof(btnConfig) !== 'object') continue;
				btnConfig.target = $(this);
				// add button to toolbar
				var button = $('<a href="javascript:void(0);" title="' + btnConfig.label + '" class="' + inputName + 'Button' + btnConfig.label + ' ' + btnConfig.label + '"><span>' + btnConfig.label + '</span></a>');
				toolbar.append(button);
				button.data('btnConfig', btnConfig);
				// add click action to buttons
				button.click(function() {
					var btnConfig = $(this).data('btnConfig');
					var selection = btnConfig.target.getSelection();
					// replace text using replaceSelection param
					if (btnConfig.replaceSelection) {
						var replaced = btnConfig.replaceSelection.replace(/%s/, selection.text);
						btnConfig.target.replaceSelection(replaced);
					// replace using callback
					} else if (btnConfig.callback) {
						btnConfig.callback(btnConfig, selection);
					}
					btnConfig.target.trigger('keyup');
				});
			}
		}
		
		// PREVIEW-PANE
		// ------------	
		if (config.showPreview) {
			var previewName = inputName + 'preview';
			$(this).after('<div id="' + previewName + '" class="simplePreview">' + ($(this).html()) + '</div>');
			// key listener that updates preview
			$(this).keyup(function(){
				if ($(this).val() !== '') {
					// replace line breaks in preview
					var text = $(this).val().replace(/\n/g, '<br />');
					$('#' + previewName).html(text);
					$('#' + previewName).show();
				} else {
					$('#' + previewName).hide();
				}
			});
			$(this).trigger('keyup');
		}
		
	};
	
})(jQuery);
