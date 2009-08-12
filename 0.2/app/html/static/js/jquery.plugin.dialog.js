/**
 *	
 *	Requirements:
 *		+ jQuery 1.3 or later (http://www.jquery.com)
 *		+ Js.Class (http://jsclass.jcoglan.com/)
 *
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 *	@since 2009-07-13
 */

/** **/
var DialogManager = new JS.Class({
	
	dialogs: [],
	types: ['confirm', 'prompt', 'message', 'iframe'],
	overlay: false,
	
	initialize: function() {
		$('body').data('DialogManager', this);
	},
	
	create: function(type, options) {
		this.createOverlay(options);
		var windowId = 'dialog' + (this.dialogs.length);
		type = type.charAt(0).toUpperCase() + type.substring(1);
		var classname = eval(type + 'Window');
		var window = new classname(this, windowId, options);
		this.dialogs.push(window);
		return window;
	},
	
	createOverlay: function(options) {
		var options = $.extend({ opacity: 0.5 }, options);
		if ($('body').find('#dialogOverlay').length > 0) return true;
		this.overlay = $('<div id="dialogOverlay" style="opacity:0"></div>');
		this.overlay.height = $(window).height();
		this.overlay.click(function() {
			$('body').data('DialogManager').closeAll();
		});
		$('body').prepend(this.overlay);
		this.overlay.fadeTo('fast', options.opacity);
	},
	
	closeOverlay: function(speed) {
		$('#dialogOverlay').fadeOut(speed, function() {
			$('#dialogOverlay').remove();
		});
		this.overlay = false;
	},
	
	close: function(DialogWindow, speed) {
		speed = speed ? speed : 'fast';
		DialogWindow.window.slideUp(speed, function() { $(this).remove(); });
		for (index in this.dialogs) {
			if (this.dialogs[index] == DialogWindow) this.dialogs.splice(index, 1);
		}
		if (this.dialogs.length <= 0) {
			this.closeOverlay('slow');
		}
	},
	
	closeAll: function() {
		for (windowId in this.dialogs) {
			this.dialogs[windowId].close();
		}
	}
	
});

window.DialogManager = new DialogManager();

/** **/
var DialogWindow = new JS.Class({
	
	DialogManager: false,
	windowId: false,
	options: {
		'class': '',
		title: 'defaul title',
		content: '',
		width: false,
		height: false
	},
	window: false,
	titleBar: false,
	content: false,
	
	initialize: function(DialogManager, windowId, options) {
		this.DialogManager = DialogManager;
		this.windowId = windowId;
		this.options = $.extend({}, this.options, options);
		// create dom elements
		this.window = $('<div class="dialog" />');
		this.content = $('<div class="content" />');
		this.titleBar = $('<div class="titleBar" />');
		// add windowId
		this.window.attr('id', this.windowId);
		if (this.options['class']) {
			this.window.addClass(this.options['class']);
		}
		this.window.prepend(this.content);
		this.window.data('dialog', this);
		$('body').prepend(this.window);
		this.initializeKeys();
		this.draw();
	},
	initializeKeys: function() {
		$(document).keyup(function(e) {
			var keyCode = e.keyCode || window.event.keyCode;
			if (keyCode == 27) {
				$('.dialog').data('dialog').close();
			}
		});
	},
	draw: function() {
		if (this.options.title) {
			this.window.prepend(this.titleBar.html(this.options.title));
			this.titleBar.append('<a class="closeBtn" href="#">X</a>');
			this.titleBar.find('.closeBtn').click(function() {
				var dialog = $(this).parents('.dialog').data('dialog');
				dialog.close();
			});
		}
		if (this.options.width > 0) {
			this.window.width(this.options.width);
		}
		if (this.options.height > 0) {
			this.window.height(this.options.height);
		}
		if (this.options.content) {
			this.window.append(this.content.html(this.options.content));
		}
		this.moveTo('center');
	},
	close: function() {
		this.DialogManager.close(this);
	},
	moveTo: function(x, y, speed) {
		if (x == 'center') {
			x = ($(window).width() - this.window.width()) / 2;
			y = ($(window).height() - this.window.height()) / 2;
		}
		this.window.animate({
			top: y ? y + 'px' : '',
			left: x ? x + 'px' : ''
		}, { duration: speed ? speed : 0 });
	},
	resizeTo: function(width, height, speed) {
		if (!speed) speed = 0;
		this.window.animate({
			width: width + 'px',
			height: height + 'px'
		}, { duration: speed ? speed : 0 });
	}
});

/** **/
var MessageWindow = new JS.Class(DialogWindow, {
});
var ErrorMessageWindow = new JS.Class(MessageWindow, {
	draw: function() {
		this.callSuper();
		this.window.addClass('error');
	}
});

/** modal window **/
var ModalWindow = new JS.Class(MessageWindow, {
	modalOptions: [
		{ label: 'OK', result: true },
		{ label: 'Abbrechen', result: false}
	],
	draw: function() {
		this.callSuper();
		this.drawModalOptions();
	},
	drawModalOptions: function() {
		var modal = $('<div class="modal"></div>');
		for(index in this.modalOptions) {
			var modalOption = $('<a href="#" class="btn">' + this.modalOptions[index].label + '</a>');
			modalOption.data('result', this.modalOptions[index].result);
			modalOption.click(function() {
				var dialog = $(this).parents('.dialog').data('dialog');
				dialog.options.callback(dialog, $(this).data('result'));
				dialog.close();
			})
			modal.append(modalOption);
		}
		this.content.append(modal);
		this.moveTo('center');
	}
});

/** confirmation windows **/
var ConfirmWindow = new JS.Class(ModalWindow, {});

/** prompt windows **/
var PromptWindow = new JS.Class(MessageWindow, {
	draw: function() {
		this.callSuper();
		this.drawPrompt();
	},
	drawPrompt: function() {
		var prompt = $('<div class="prompt"></div>');
		var input = $('<input type="text" name="prompt" class="value" />');
		var submit = $('<a href="#" class="btn">OK</a>');
		if (this.options.value) {
			input.val(this.options.value);
		}
		input.keyup(function(e) {
			var keyCode = e.keyCode || window.event.keyCode;
			if (keyCode == 13 || keyCode == 10) {
				submit.trigger('click');	
			}
		});
		submit.click(function() {
			var dialog = $(this).parents('.dialog').data('dialog');
			dialog.options.callback(dialog, $(this).parents('.prompt').find('.value').val());
		});
		this.content.append(prompt.append(input).append(submit));
		this.moveTo('center');
	}
});

var IFrameWindow = new JS.Class(MessageWindow, {
	draw: function() {
		this.callSuper();
		var iframe = $('<iframe src="' + this.options.url + '"></iframe>');
		if (this.options.width) {
			iframe.attr('width', this.options.width);
		}
		if (this.options.height) {
			iframe.attr('height', this.options.height - this.titleBar.height());
		}
		this.content.append(iframe);
		this.window.addClass('iframe');
		this.moveTo('center');
	}
});
