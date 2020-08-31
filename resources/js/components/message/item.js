export default function item(i) {

	let id = i.data("id");
	let user_id = i.data("user-id");
	let $btn_open_descendants = i.find('.open_descendants').first();
	let $btn_close_descendants = i.find('.close_descendants').first();
	let $btn_expand = i.find('.btn-expand').first();
	let $btn_compress = i.find('.btn-compress').first();
	let html_box = i.find('.html_box').first();
	let $btn_delete = i.find('.delete').first();
	let $btn_restore = i.find('.restore').first();
	let $btn_edit = i.find('.btn-edit').first();
	i.self = i.find('[data-self]:first');

	$btn_edit.unbind("click").on('click', open_edit);
	html_box.htmlExpand({
		expand_button: $btn_expand,
		compress_button: $btn_compress,
		onExpand: function () {
			//$(window).scrollTo(i);
		},
		onCompress: function () {
			$(window).scrollTo(i);
		}
	});

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
			url: "/messages/" + id
		}).done(function (msg) {

			console.log(msg);

			if (msg.deleted_at) {
				$btn_delete.hide();
				$btn_restore.show();
				html_box.addClass('transparency');
			} else {
				$btn_delete.show();
				$btn_restore.hide();
				html_box.removeClass('transparency');
			}

			$btn_restore.button('reset');
			$btn_delete.button('reset');
		}).fail(function () {
			btn.show();
		});
	}

	function open_edit() {

		event.preventDefault();

		$btn_edit.hide();

		$.ajax({
			method: "GET",
			url: $btn_edit.attr('href')
		}).done(function (html) {

			html_box.hide();

			html_box.after(html);

			let form = i.self.find('form:first');

			set_sceditor(form.find('.sceditor').first().get(0));

			$(window).scrollTo(i);

			form.ajaxForm({
				dataType: 'json',
				beforeSend: function (data) {
					form.addClass('loading-cap');
				},
				success: function (message) {

					html_box.html(message.text);
					html_box.show();
					form.remove();

					item(i);

					$btn_edit.show();
				},
				error: function (data) {
					form.removeClass('loading-cap');
				}
			});
		}).fail(function () {
			$btn_edit.show();
		});
	}
}