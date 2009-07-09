/**
 * Javascript debugging console
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 */
var debug = {
	
	id: 'debug',	// style id of debug console
	
	/**
	 * Write a message to the debug console
	 * @param string message
	 */
	trace: function (message) {
		$('#' + this.id + ' .messages').append(message + '<br />');
		this.show();	
	},
	
	/**
	 * Initiates the debugging console and set up open/close action
	 */
	init: function() {
		$('body').append($('<div id="' + this.id + '"><span class="toggle"></span><div class="messages" style="display: none"></div></div>'));
		$('#' + this.id + ' .toggle').click(function() {
			window.debug.toggle();
		});
		window.debug = this;
	},
	
	/**
	 * Show/hide console depending on current state
	 */
	toggle: function() {
		$('#' + this.id + ' .messages').slideToggle();
	},
	
	show: function() {
		$('#' + this.id + ' .messages').slideDown();
	},
	
	dump: function() {
		
	}
	
}