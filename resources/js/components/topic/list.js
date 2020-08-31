import item from "./item";

export default function topics_list(parent) {

	parent.find('.item').each(function () {

		item($(this));
	});
}




