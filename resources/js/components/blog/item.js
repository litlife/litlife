import Like from "../Like";
import shareButton from "share_button";

export default function item(i, $parent = null) {

	let user_id = i.data("user-id");
	let id = i.data("id");
	let $id = id;
	let level = i.data("level");
	let buttons_panel = i.find('.buttons-panel').first();
	i.descendants = i.find(".descendants").first();
	let html_box = i.find('.html_box').first();

	let $btn_expand_descendants = buttons_panel.find('.open_descendants').first();
	let $btn_compress_descendants = buttons_panel.find('.close_descendants').first();
	i.counter_expand_descendants = $btn_expand_descendants.find('.counter:first');
	i.counter_compress_descendants = $btn_compress_descendants.find('.counter:first');
	let $btn_delete = buttons_panel.find('.delete').first();
	let $btn_restore = buttons_panel.find('.restore').first();
	let $btn_edit = buttons_panel.find('.btn-edit').first();
	let $btn_get_link = buttons_panel.find('.get_link').first();
	let $btn_reply = buttons_panel.find('.btn-reply').first();
	let $btn_expand = buttons_panel.find('.btn-expand').first();
	let $btn_compress = buttons_panel.find('.btn-compress').first();
	let $btn_share = buttons_panel.find('button.share').first();
	let $parent_id = buttons_panel.data('parent-id');
	let $btn_approve = buttons_panel.find('.approve').first();

	i.self = i.find('[data-self]:first');

	i.parent = $parent;

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

	i.find('.like').each(function () {
		new Like().init($(this));
	});

	new shareButton().init($btn_share);

	$btn_delete.unbind("click").on('click', function () {
		deleteOrRestore($(this));
	});
	$btn_restore.unbind("click").on('click', function () {
		deleteOrRestore($(this));
	});

	$btn_expand_descendants.unbind("click").on('click', open_descendants);
	$btn_compress_descendants.unbind("click").on('click', close_descendants);
	$btn_get_link.removeAttr('href').unbind("click").on('click', get_link);

	$btn_reply.unbind("click").on('click', open_reply);
	$btn_edit.unbind("click").on('click', open_edit);
	$btn_approve.unbind("click").on('click', approve);

	i.descendants.find(".item:first").siblings('.item').addBack().each(function () {
		item($(this), i);
	});

	function open_reply() {

		event.preventDefault();

		var reply_box = i.descendants.children('.reply-box').first();

		if (reply_box.length < 1) {
			$btn_reply.addClass('loading-cap');

			$.ajax({
				method: "GET",
				url: $btn_reply.attr('href')
			}).done(function (html) {

				i.descendants.append(html);

				var reply_box = i.descendants.children(".reply-box").first();

				$(window).scrollTo(reply_box);

				$btn_reply.removeClass("loading-cap");

				let reply_form = reply_box.find('form').first();

				set_sceditor(reply_form.find('.sceditor').first().get(0));

				reply_form.ajaxForm({
					dataType: 'json',
					beforeSend: function (data) {
						reply_box.addClass('loading-cap');
					},
					success: function (blog) {

						$.ajax({
							method: "GET",
							url: "/blogs/" + blog.id
						}).done(function (html) {

							reply_box.removeClass('loading-cap');

							reply_box.after(html);

							reply_box.remove();

							i.descendants.find(".item[data-id='" + blog.id + "']").each(function () {
								item($(this), i);
							});

							i.increment_childs();
						});
					},
					error: function (data) {
						reply_box.removeClass('loading-cap');
					},

				});
			});

		} else {
			$(window).scrollTo(reply_box);
		}
	}

	function deleteOrRestore(btn) {
		btn.button('loading');

		btn.hide();

		$.ajax({
			method: "DELETE",
			url: "/users/" + user_id + "/blogs/" + id
		}).done(function (msg) {

			if (msg.deleted_at) {
				$btn_delete.hide();
				$btn_restore.show();
				html_box.addClass('transparency');

				if (i.parent != null)
					i.parent.decrement_childs();

				close_descendants();
				$btn_expand_descendants.remove();
				$btn_compress_descendants.remove();

			} else {
				$btn_delete.show();
				$btn_restore.hide();
				html_box.removeClass('transparency');

				if (i.parent != null)
					i.parent.increment_childs();
			}

			$btn_restore.button('reset');
			$btn_delete.button('reset');
		});
	}

	function open_descendants() {
		console.log('open_childs');

		$btn_expand_descendants.addClass('loading-cap');
		$btn_compress_descendants.addClass('loading-cap');

		i.descendants.children('.item').remove();

		i.descendants.prepend('<div class="spinner" style="text-align: center; font-size:48px;"><i class="fas fa-spinner fa-spin"></i></div>');

		$.ajax({
			method: "GET",
			url: "/users/" + user_id + "/blogs/" + id + "/loadChild?level=" + level
		}).done(function (result) {

			i.descendants.find('.spinner').remove();

			i.descendants.prepend(result);

			i.descendants.find(".item:first").siblings('.item').addBack().each(function () {
				item($(this), i);
			});

			$btn_expand_descendants.hide();
			$btn_compress_descendants.removeClass("loading-cap");
			$btn_compress_descendants.show();

		}).always(function () {
			$btn_expand_descendants.removeClass("loading-cap");
		});
	}

	function close_descendants() {
		console.log('close_childs');

		i.descendants.children('.item').remove();
		$btn_compress_descendants.hide();
		$btn_expand_descendants.show();
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

			$btn_edit.hide();

			form.ajaxForm({
				dataType: 'json',
				beforeSend: function (data) {
					form.addClass('loading-cap');
				},
				success: function (blog) {

					html_box.html(blog.text);
					html_box.show();
					form.remove();

					item(i);

					$(window).scrollTo(i);

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

	function get_link(e) {
		e.preventDefault();
		bootbox.alert('<textarea class="form-control">' + $btn_get_link.data('href') + '</textarea>');
	}


	i.increment_childs = function () {
		console.log('increment_childs');

		let count = i.counter_expand_descendants.text();
		count = parseInt(count, 10) + 1;
		i.counter_expand_descendants.text(count);

		count = i.counter_compress_descendants.text();
		count = parseInt(count, 10) + 1;
		i.counter_compress_descendants.text(count);

		if (count > 0) {
			if (!$btn_expand_descendants.is(":visible") & !$btn_compress_descendants.is(":visible"))
				$btn_compress_descendants.show();
		}
	}

	i.decrement_childs = function () {
		console.log('decrement_childs');

		let count = i.counter_expand_descendants.text();
		count = parseInt(count, 10) - 1;
		i.counter_expand_descendants.text(count);

		count = i.counter_compress_descendants.text();
		count = parseInt(count, 10) - 1;
		i.counter_compress_descendants.text(count);

		if (count < 1) {
			if ($btn_expand_descendants.is(":visible") || $btn_compress_descendants.is(":visible"))
				$btn_compress_descendants.hide();
		}
	}

	let $btn_get_user_agent = i.find('.get_user_agent').first();
	$btn_get_user_agent.unbind("click").on('click', get_user_agent);

	function get_user_agent() {

		var dialog = bootbox.dialog({
			message: ' ',
			closeButton: true,
			backdrop: true
		}).init(function () {
			console.log('init');

			var body = dialog.find('.bootbox-body');

			body.html('<div class="spinner" style="text-align: center; font-size:48px;"><i class="fas fa-spinner fa-spin"></i></div>');

			$.ajax({
				method: "GET",
				url: "/user_agents/blog/" + $id
			}).done(function (result) {

				body.find('.spinner').remove();
				body.html(result);
			}).always(function () {

			});
		});
	}

	function approve() {

		event.preventDefault();
		event.stopPropagation();

		$btn_approve.hide();

		$.ajax({
			method: "GET",
			url: "/blogs/" + $id + "/approve"
		}).done(function (msg) {

			if (msg.status_changed_at) {

			} else {
				$(this).show();
			}
		});
	}
}