var groups = $(".forum_groups");

groups.sortable({
	containerSelector: '.forum_groups',
	handle: '.move_group',
	itemSelector: ".forum_group",
	placeholder: '<div class="placeholder"/>',
	onDrop: function ($item, container, _super, event) {
		$item.removeClass(container.group.options.draggedClass).removeAttr("style");
		$("body").removeClass(container.group.options.bodyClass);

		var data = groups.sortable("serialize").get();

		var order = [];

		$.each(data[0], function (key, value) {
			order.push(value['id']);
		});

		console.log(order);

		$.ajax({
			method: "POST",
			url: "/forum_groups/change_order",
			data: {
				'order': order
			}
		}).done(function (msg) {
			console.log(msg);
		});
	}
});

groups.find('.forum_group').each(function () {

	var forum_group = $(this);

	var forum_group_id = forum_group.data('id');

	var forums = forum_group.find('.forums');

	forums.sortable({
		containerSelector: 'table',
		itemPath: '> tbody',
		itemSelector: 'tr',
		handle: '.move_forum',
		placeholder: '<tr class="placeholder"/>',
		onDrop: function ($item, container, _super, event) {
			$item.removeClass(container.group.options.draggedClass).removeAttr("style");
			$("body").removeClass(container.group.options.bodyClass);

			var data = forums.sortable("serialize").get();

			var order = [];

			$.each(data[0], function (key, value) {
				order.push(value['id']);
			});

			console.log(order);

			$.ajax({
				method: "POST",
				url: "/forum_groups/" + forum_group_id + "/change_order",
				data: {
					'order': order
				}
			}).done(function (msg) {
				console.log(msg);
			});
		}
	});

	forums.find('.forum').each(function () {

		let i = $(this);

		let id = i.data('id');

		let members = i.find('.members');

		let list = i.find('.list');

		members.find('.show_all').click(function () {
			$(this).hide();
			list.show();
		});

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
				url: "/forums/" + id + "/delete"
			}).done(function (msg) {

				console.log(msg);

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
			}).fail(function () {
				btn.show();
			});
		}
	});
});