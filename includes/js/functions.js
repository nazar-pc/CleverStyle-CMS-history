function print_r (array) {
	// http://kevin.vanzonneveld.net
	// + original by: Michael White (http://getsprink.com)
	// + improved by: Ben Bryan
	// + input by: Brett Zamir (http://brett-zamir.me)
	// + improved by: Brett Zamir (http://brett-zamir.me)
	// + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// + optimized by: Mokrynskyi Nazar (http://cleverstyle.org)
    
    var output = '', pad_char = ' ', d = this.window.document;
    var getFuncName = function (fn) {
        var name = (/\W*function\s+([\w\$]+)\s*\(/).exec(fn);
        if (!name) {
            return '(Anonymous)';
        }
        return name[1];
    };
    var repeat_char = function (len, pad_char) {
        var str = '';
        for (var i=0; i < len; i++) {
            str += pad_char;
        }
        return str;
    };
    var formatArray = function (obj, cur_depth, pad_char) {
        if (cur_depth > 0) {
            cur_depth++;
        }
        var base_pad = repeat_char(4*cur_depth, pad_char);
        var thick_pad = repeat_char(4*(cur_depth+1), pad_char);
        var str = '';
        if (typeof obj === 'object' && obj !== null && obj.constructor && getFuncName(obj.constructor) !== 'PHPJS_Resource') {
            str += "Array\n" + base_pad + "(\n";
            for (var key in obj) {
                if (obj[key] instanceof Array) {
                    str += thick_pad + "["+key+"] => "+formatArray(obj[key], cur_depth+1, pad_char);
                } else {
                    str += thick_pad + "["+key+"] => " + obj[key] + "\n";
                }
            }
            str += base_pad + ")\n";
        } else if (obj === null || obj === undefined) {
            str = '';
        } else {
            str = obj.toString();
        }
        return str;
    };
	return output;
}
function string_trim (input) {
	input = input.match(/^.*$/gm);
	size = input.length;
	result = '';
	for (i = 0; i < size; i++) {
		if (input[i]) {
			input[i] = $.trim(input[i]);
		}
		result += input[i];
	}
	return result;
}