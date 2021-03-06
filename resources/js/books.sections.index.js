var group = $("ol.sortable").sortable({
	handle: '.handle',
	group: 'sortable',
	nested: true,
	pullPlaceholder: true,
	onDrop: function ($item, container, _super) {

		var $clonedItem = $('<li/>').css({height: 0});
		$item.before($clonedItem);
		$clonedItem.animate({'height': $item.height()});

		$item.animate($clonedItem.position(), function () {
			$clonedItem.detach();
			_super($item, container);
		});

		/*
		var data = group.sortable("serialize").get();

		var jsonString = JSON.stringify(data, null, ' ');

		//$('#serialize_output2').text(jsonString);

		_super($item, container);
		*/
	}
});

let $btnSavePosition = $(".save").first();

$btnSavePosition.unbind('click').bind('click', function () {

	$btnSavePosition.find('.fa-spinner').show();

	var data = group.sortable("serialize").get();

	var hierarchy = data[0];

	function handler(hierarchy) {

		$.each(hierarchy, function (key, array) {
			hierarchy[key]['children'] = handler(array['children'][0]);
		});
		return hierarchy;
	}

	$.ajax({
		type: "POST",
		url: "/books/" + window.sharedData.book.id + "/sections/save_position",
		data: {
			"hierarchy": handler(hierarchy)
		},
		success: function (msg) {
			$btnSavePosition.find('.fa-spinner').hide();
			console.log(msg);
		},
		error: function (msg) {
			$btnSavePosition.find('.fa-spinner').hide();
			console.log(msg);
		}
	});
});


/*
            $('ol.sortable').nestedSortable({
                forcePlaceholderSize: true,
                handle: '.handle',
                helper: 'clone',
                items: 'li',
                opacity: .6,
                placeholder: 'placeholder',
                revert: 250,
                tabSize: 25,
                tolerance: 'pointer',
                toleranceElement: '> .section',
                //maxLevels: 3,
                isTree: true,
                expandOnHover: 700,
                startCollapsed: true,
                relocate: function () {


                }
            });
*/


$(".section").each(function () {
	let i = $(this);
	let id = i.data('id');
	let book_id = i.data('book-id');
	let inner_id = i.data('inner-id');

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
			method: "GET",
			url: "/books/" + book_id + "/sections/" + inner_id + "/delete"
		}).done(function (msg) {

			if (msg.deleted_at) {
				$btn_delete.hide();
				$btn_restore.show();
				i.find('.title').addClass('transparency');
			} else {
				$btn_delete.show();
				$btn_restore.hide();
				i.find('.title').removeClass('transparency');
			}

			$btn_restore.button('reset');
			$btn_delete.button('reset');
		});
	}

	let $moveToNotes = i.find('.move-to-notes');

	$moveToNotes.unbind("click").on('click', function () {

		$.ajax({
			type: "POST",
			url: "/books/" + window.sharedData.book.id + "/sections/move_to_notes",
			data: {"ids": id},
			dataType: 'json',
			success: function (msg) {
				i.remove();
			},
			error: function (msg, s) {
				console.log(s);
			}
		});

	});
});