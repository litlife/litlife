export default function item(i) {

	var forum_id = i.data("forum-id");

	var id = i.data("id");

	let $btn_delete = i.find('.delete');

	$btn_delete.unbind("click").on('click', function () {
		deleteOrRestore($(this));
	});

	let $btn_restore = i.find('.restore');

	$btn_restore.unbind("click").on('click', function () {
		deleteOrRestore($(this));
	});

	function deleteOrRestore(btn) {
		btn.button('loading');

		btn.hide();

		$.ajax({
			method: "DELETE",
			url: "/topics/" + id
		}).done(function (msg) {

			console.log(msg);

			if (msg.deleted_at) {
				$btn_delete.hide();
				$btn_restore.show();
				i.find('.name').addClass('transparency');
				i.find('.description').addClass('transparency');
			} else {
				$btn_delete.show();
				$btn_restore.hide();
				i.find('.name').removeClass('transparency');
				i.find('.description').removeClass('transparency');
			}

			$btn_restore.button('reset');
			$btn_delete.button('reset');
		}).fail(function () {
			btn.show();
		});
	}
}