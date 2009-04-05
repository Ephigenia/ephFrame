/*
 * jQuery plugin: fieldSelection - v0.1.0 - last change: 2006-12-16
 * (c) 2006 Alex Brem <alex@0xab.cd> - http://blog.0xab.cd
 */
(function(){
    var c = {
        getSelection: function(){
            var e = this.jquery ? this[0] : this;
            return (('selectionStart' in e &&
            function(){
                var l = e.selectionEnd - e.selectionStart;
                return {
                    start: e.selectionStart,
                    end: e.selectionEnd,
                    length: l,
                    text: e.value.substr(e.selectionStart, l)
                }
            }) || (document.selection &&
            function(){
                e.focus();
                var r = document.selection.createRange();
                if (r == null) {
                    return {
                        start: -1,
                        end: e.value.length,
                        length: 0
                    }
                }
                var a = e.createTextRange();
                var b = a.duplicate();
                a.moveToBookmark(r.getBookmark());
                b.setEndPoint('EndToStart', a);
                return {
                    start: b.text.length,
                    end: b.text.length + r.text.length,
                    length: r.text.length,
                    text: r.text
                }
            }) ||
            function(){
                return {
                    start: 0,
                    end: e.value.length,
                    length: 0
                }
            })()
        },
        replaceSelection: function(){
            var e = this.jquery ? this[0] : this;
            var a = arguments[0] || '';
            return (('selectionStart' in e &&
            function(){
                e.value = e.value.substr(0, e.selectionStart) + 'X' + e.value.substr(e.selectionEnd, e.value.length);
                return this
            }) || (document.selection &&
            function(){
                e.focus();
				document.selection.createRange().text = a;
                return this
            }) ||
            function(){
                e.value += a;
                return this
            })()
        }
    };
    jQuery.each(c, function(i){
        jQuery.fn[i] = this
    })
})();
