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