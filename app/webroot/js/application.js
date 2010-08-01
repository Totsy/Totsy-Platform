/**
 * Mimic PHP's number_format() function by adding commas to numbers.
 */
jQuery.numberFormat = function(n, decimals) {
	decimals = decimals || 0;

	if (n > 9999) {
		n = Math.round(n / 1000) + "K";
	}

	n += '';
	x = n.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;

	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}

	if (x2.length > 3) {
		x2 = x2.substr(0, 3);
	}
	if (decimals && !x2) {
		x2 = '.';
	}
	if (x2.length > 0) {
		while (x2.length < decimals + 1) {
			x2 += "0";
		}
	}
	return x1 + x2;
};

jQuery.getNumber = function(n) {
	n = ('' + n).replace(/[^\-\+0-9\.Kk]/g, '');
	m = (/k|K/g).test(n);
	n = n.replace(/K|k/g, '');

	if (parseFloat(n).toString() == 'NaN') {
		return 0;
	}
	return parseFloat(n) * (m ? 1000 : 1);
};

jQuery.fn.template = function(text, formatters) {

	var replace = function(data, key, t) {
		if (typeof formatters != 'undefined' && typeof formatters[key] != 'undefined') {
			data = formatters[key](data);
		}
		if (t) {
			return t.replace(new RegExp('{:' + key + '}', 'g'), data);
		}
		return '';
	};
	keyPath = [];

	var loop = function(data) {
		for (n in data) {
			keyPath.push(n);
			if (data[n] === null) {
				data[n] = '';
			}

			if (typeof data[n] == 'object') {
				if (!(data[n] instanceof Array)) {
					text = loop(data[n]);
				}
			} else {
				text = replace(data[n], keyPath.join('.'), text);
			}
			keyPath.pop();
		}
		return text;
	};
	return loop(this[0]).replace(/{:\w+}/g, '');
};
