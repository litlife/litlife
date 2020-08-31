let button = $(".bookmark-button");

let $modal_bookmark_remove = $('#bookmarkRemoveModal');
let $modal_bookmark_add = $('#bookmarkAddModal');

let $btn_bookmark_remove = $('#bookmarkRemoveButton');
let $btn_bookmark_add = $('#bookmarkAddButton');
let user_id = $btn_bookmark_add.data('user-id');
let $folder_title = $btn_bookmark_remove.find('.folder-title');
let $remove_button_text = $btn_bookmark_remove.find('.remove-button-text');

$modal_bookmark_remove.on('shown.bs.modal', function () {

});

$modal_bookmark_remove.find('.submit').on('click', function () {

	$.ajax({
		method: "DELETE",
		url: "/bookmarks/" + $btn_bookmark_remove.data('bookmark-id')
	}).done(function (msg) {
		$btn_bookmark_remove.addClass('d-none').removeClass('d-flex');
		$btn_bookmark_add.removeClass('d-none').addClass('d-flex');

		$modal_bookmark_remove.modal('hide');
	});
});

$modal_bookmark_add.on('shown.bs.modal', function () {

	let select = $modal_bookmark_add.find('select');

	select.prop("disabled", true);
	select.html("");

	$modal_bookmark_add.find('[name=title]').val($btn_bookmark_add.data('title'));

	$.getJSON("/users/" + user_id + "/bookmark_folders/", function (data) {

		select.append("<option value=''></option>");

		$.each(data, function (key, bookmark_folder) {
			select.append("<option value='" + bookmark_folder.id + "'>" + bookmark_folder.title + "</option>");
		});

		select.prop("disabled", false);
	});
});

$modal_bookmark_add.find('form').on("submit", function (e) {

	let postData = $(this).serializeArray();

	let formURL = $(this).attr("action");

	postData.push({name: 'url', 'value': window.location.href});

	$.ajax({
		url: formURL,
		type: "POST",
		data: postData,
		success: function (bookmark, textStatus, jqXHR) {

			console.log(bookmark);

			$btn_bookmark_remove.data('bookmark-id', bookmark.id);

			$btn_bookmark_add.addClass('d-none').removeClass('d-flex');
			$btn_bookmark_remove.removeClass('d-none').addClass('d-flex');

			$modal_bookmark_add.modal('hide');

			if (bookmark.folder && bookmark.folder.title) {
				$folder_title.removeClass('d-none');
				$remove_button_text.addClass('d-none');

				$folder_title.text(' - ' + bookmark.folder.title);
			} else {
				$folder_title.addClass('d-none');
				$remove_button_text.removeClass('d-none');
			}
		}
	});

	e.preventDefault();
});

button.click(function (e) {
	/*
		e.preventDefault();

		if (button.hasClass('bookmark-remove')) {



			var dialog = BootstrapDialog.confirm({
				title: 'Удаление закладки',
				message: 'Вы действительно хотите удалить закладку?',
				callback: function (result) {
					if (result) {

						$.ajax({
							method: "DELETE",
							url: "/bookmarks/" + button.data('bookmark-id')
						}).done(function (msg) {

							button.removeClass('bookmark-remove').addClass('bookmark-add');

							button.find('.glyphicon').removeClass('glyphicon-star').addClass('glyphicon-star-empty');

							button.find('.button-text').text('Добавить в закладки');
						});
					}
				}
			});
		}
		else {

			$.getJSON( "/bookmark_folders/", function( data ) {

				var items = [];

				$.each( data, function( key, bookmark_folder ) {
					items.push( "<option value='" + bookmark_folder.id + "'>" + bookmark_folder.title + "</option>" );
				});

				var dialog = BootstrapDialog.confirm({
					title: 'Добавление закладки',
					message: '<div class="container-fluid"><div class="row"><textarea class="form-control" placeholder="Введите имя закладки...">' + document.title + '</textarea></div>'
					+ '<div class="row"><select class="form-control">' + items.join( "" ) + '</select></div></div>',

					callback: function (result) {
						if (result) {

							$.ajax({
								method: "POST",
								url: "/bookmarks/?folder=" + dialog.getModalBody().find("select").val(),
								data: {
									'title': dialog.getModalBody().find("textarea").val(),
									'url': window.location.href
								}

							}).done(function (bookmark) {

								button.data('folder-id', bookmark.folder_id);
								button.data('bookmark-id', bookmark.id);

								button.removeClass('bookmark-add').addClass('bookmark-remove');

								button.find('.glyphicon').removeClass('glyphicon-star-empty').addClass('glyphicon-star');

								button.find('.button-text').text('Удалить из закладок');
							});


						}
					}
				});
			});

	}
	*/
});
