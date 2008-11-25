/**
 * 	Core java-script
 * 	@author Ephigenia M. Eichner
 */

/**
 *	make some stuff to get the current browser in use
 */
var BROWSER = '';
if (navigator.userAgent.match(/msie/ig)) {
	BROWSER = 'IE';
} else {
	BROWSER = 'UNKNOWN';
}

/**
 *	Returns the classname of any object if defined
 *	@return string
 */
Object.prototype.getClassname = function() {
	if(this.constructor && this.constructor.toString) {
		var arr = this.constructor.toString().match(/function\s*(\w+)/);
		return (arr && (arr.length==2?arr[1]:undefined));
	} else {
		return undefined;
	}
}

/**
 *	Checks if the given var is empty in any kind of perspective
 *	@param mixed string
 *  @return boolean
 */
function isEmpty(string) {
	if (string == '') return true;
	if (string == undefined) return true;
	if (typeof(string) == 'number') return (string !== 0);
	return string.match(/^$/, string);
}

/**
 *	Remove an index from an indexed array
 *	<code>
 *	var a = new Array(0,1,2,3,4,5);
 *	// should alert '0,2,3,4,5'
 *	alert(a.remove(2));
 *	</code>
 */
Array.prototype.remove = function(from, to) {
	var rest = this.slice((to || from) + 1 || this.length);
	this.length = from < 0 ? this.length + from : from;
	return this.push.apply(this, rest);
};

/**
 *	Delete Values from an indexed array with the passed value or regular
 *	expression
 *	<code>
 *	// filter all non-numeric entries
 *	var a = arr.filter(/[^\d]/);
 *	</code>
 */
Array.prototype.filter = function(m) {
	for (var a = 0; a < this.length; a++) {
		if (this[a] == m || (m.getClassname && m.getClassname() == 'RegExp' && m.exec(this[a]))) {
			this.splice(a, 1);
			a--;
		}
	}
	return this;
}

/**
 *	Returns the index of the $value if found
 * 	@param array(mixed) $val
 */
Array.prototype.indexOf = function($val) {
	for(var a = 0; a < this.length; a++) {
		if (this[a] == $val) {
			return a;
		}
	}
	return false;
}


/**
 *	Url Escapes utf8 characters in a string and returns it
 *	@param string data
 *	@return string
 */
function escape_utf8(data) {
	if (isEmpty(data)) return '';
	data = data.toString();
	var buffer = '';
	var chars = '0123456789ABCDEF';
	for (var i = 0; i < data.length; i++) {
		var c = data.charCodeAt(i);
		var bs = new Array();
		if (c > 0x10000) {
			bs[0] = 0xF0 | ((c & 0x1C0000) >>> 18);
			bs[1] = 0x80 | ((c & 0x3F000) >>> 12);
			bs[2] = 0x80 | ((c & 0xFC0) >>> 6);
			bs[3] = 0x80 | (c & 0x3F);
		} else if (c > 0x800) {
			bs[0] = 0xE0 | ((c & 0xF000) >>> 12);
			bs[1] = 0x80 | ((c & 0xFC0) >>> 6);
			bs[2] = 0x80 | (c & 0x3F);
		} else if (c > 0x80) {
			bs[0] = 0xC0 | ((c & 0x7C0) >>> 6);
			bs[1] = 0x80 | (c & 0x3f);
		} else {
			bs[0] = c;
		}
		if (bs.length > 1) {
			for (var j = 0; j < bs.length; j++) {
				var b = bs[j];
				var hex = chars.charAt((b & 0xF0) >>> 4) + chars.charAt(b & 0x0F);
				buffer += '%' + hex;
			}
		} else {
			buffer += data.charAt(i);
		}
	}
	return buffer;
}