export default function em_to_px_convert(input) {
	let emSize = parseFloat($("body").css("font-size"));
	return (emSize * input);
}