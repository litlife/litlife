export default function item(i, $parent = null) {

	let id = i.data("id");

	let $btn_delete = i.find('.delete').first();
	let $btn_restore = i.find('.restore').first();

	$btn_delete.unbind("click").on('click', function () {
		deleteOrRestore($(this));
	});
	$btn_restore.unbind("click").on('click', function () {
		deleteOrRestore($(this));
	});

	function deleteOrRestore(btn) {
		btn.button('loading');

		btn.hide();

		$.ajax({
			method: "DELETE",
			url: "/notes/" + id
		}).done(function (msg) {

			if (msg.deleted_at) {
				$btn_delete.hide();
				$btn_restore.show();
				i.addClass('transparency');
			} else {
				$btn_delete.show();
				$btn_restore.hide();
				i.removeClass('transparency');
			}

			$btn_restore.button('reset');
			$btn_delete.button('reset');
		});
	}
}