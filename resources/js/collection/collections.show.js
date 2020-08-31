import collection from "../components/collection/item";

$(".collection").each(function () {
	let object = new collection();
	object.init($(this));
});