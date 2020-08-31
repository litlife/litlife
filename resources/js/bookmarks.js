import folder_item from "./components/bookmark/folder/item";
import bookmark_item from "./components/bookmark/item";

let folders = $(".folders");

folders.find(".item").each(function () {
	folder_item($(this));
});

let bookmarks = $(".bookmarks");

bookmarks.find(".item").each(function () {
	bookmark_item($(this));
});

folders.sortable({
	itemSelector: '.list-group-item',
	containerSelector: '.list-group',
	handle: '.handle',
	onDrop: function ($item, container, _super, event) {
		$item.removeClass(container.group.options.draggedClass).removeAttr("style");
		$("body").removeClass(container.group.options.bodyClass);

		var data = folders.sortable("serialize").get();

		var order = [];

		$.each(data[0], function (key, value) {
			order.push(value['id']);
		});

		//console.log(order);

		$.ajax({
			method: "POST",
			url: "/bookmark_folders/save_position",
			data: {
				'order': order
			}
		})
			.done(function (msg) {
				$('.position-save-output').html(msg);
			});

	}
});