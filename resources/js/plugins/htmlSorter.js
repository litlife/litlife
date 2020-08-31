export default function htmlSorter(a, b) {

	var a = $.trim($('<span>' + a + '</span>').text()).replace(/( +)/g, " ");
	var b = $.trim($('<span>' + b + '</span>').text()).replace(/( +)/g, " ");

	if (a < b) return -1;
	if (a > b) return 1;
	return 0;
}








