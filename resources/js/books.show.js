import Favorite from "./components/user_library_button";
import comment from "./components/comment/item";
import Like from "./components/Like";
import shareButton from "share_button";

var booksShow = $(".book");

if (booksShow.length) {

	let annotation = booksShow.find('#annotation').first();
	let expand_biography = booksShow.find('.expand-biography').first();
	let compress_biography = booksShow.find('.compress-biography').first();
	let top_comments = booksShow.find('.top_comments').first();
	let comments = booksShow.find('.comments').first();
	let form_create_comment = booksShow.find('.reply-box').find('form');
	let $btn_share = booksShow.find('button.share');
	let id = window.sharedData.book_id;

	new shareButton().init($btn_share);

	annotation.htmlExpand({
		expand_button: expand_biography,
		compress_button: compress_biography,
		onExpand: function () {
			//$(window).scrollTo(annotation, 50, {offset: -60});
		},
		onCompress: function () {
			$(window).scrollTo(annotation);
		}
	});

	booksShow.find('.like').each(function () {
		new Like().init($(this));
	});

	booksShow.find('.read-status').change(function () {

		var button = $(this);

		$.ajax({
			method: "GET",
			url: '/books/' + window.sharedData.book_id + '/read_status/' + button.val(),
			beforeSend: function (xhr) {
				button.addClass('saving');
				button.removeClass('saved');
				button.removeClass('error');
			}
		}).done(function (msg) {
			button.addClass('saved');
			button.removeClass('saving');
			button.removeClass('error');
		}).fail(function () {
			button.val("0");
			button.addClass('error');
			button.removeClass('saving');
			button.removeClass('saved');
		});
	});

	booksShow.find('.cover').click(function (event) {

		event.preventDefault();

		const dialog = bootbox.dialog({
			show: true,
			message: "<div class='text-center h1'><i class=\"fas fa-spinner fa-spin\"></i></div>"
		});

		let href = $(this).find('a').attr('href');

		$.ajax({
			method: "GET",
			url: href
		}).done(function (html) {

			dialog.find('.bootbox-body').html(html);
		});
	});


	new Favorite().init($(".user_library").first(), "/books/" + window.sharedData.book_id + "/toggle_my_library");


	booksShow.find('.similars_item').each(function () {

		var similars_item = $(this);
		var other_book_id = similars_item.data('other-book-id');

		similars_item.find('.similar').click(function () {

			similars_item.addClass('loading-cap');

			similars_item.find('.not_similar').removeClass('active');

			$.ajax({
				method: "GET",
				url: '/books/' + window.sharedData.book_id + '/similar_vote/' + other_book_id + '/1'
			}).done(function (similar_vote) {
				result(similar_vote);
			}).fail(function () {
				similars_item.removeClass('loading-cap');
				similars_item.find('.not_similar').removeClass('active');
				similars_item.find('.similar').removeClass('active');
			});
		});

		similars_item.find('.not_similar').click(function () {

			similars_item.addClass('loading-cap');

			similars_item.find('.similar').removeClass('active');

			$.ajax({
				method: "GET",
				url: '/books/' + window.sharedData.book_id + '/similar_vote/' + other_book_id + '/-1'
			}).done(function (similar_vote) {
				result(similar_vote);
			}).fail(function () {
				similars_item.removeClass('loading-cap');
				similars_item.find('.not_similar').removeClass('active');
				similars_item.find('.similar').removeClass('active');
			});
		});

		function result(similar_vote) {

			similars_item.removeClass('loading-cap');

			console.log(similar_vote);
			similar_vote.vote = parseInt(similar_vote.vote);

			if (similar_vote.vote > 0) {
				similars_item.find('.similar').addClass('active');
				similars_item.find('.not_similar').removeClass('active');
			} else if (similar_vote.vote < 0) {
				similars_item.find('.not_similar').addClass('active');
				similars_item.find('.similar').removeClass('active');
			} else {
				similars_item.find('.not_similar').removeClass('active');
				similars_item.find('.similar').removeClass('active');
			}
		}
	});

	// popover для ключевых слов

	booksShow.find('.keyword').each(function () {

		let keyword = $(this);
		let button = $(this);
		let book_keyword_id = keyword.data('id');
		let popover = $('#' + keyword.data('target'));
		let book_id = keyword.data('book-id');

		let leave_timer;
		let delay_leave = 300;

		button.popover({
			animation: false,
			html: true,
			toggle: 'popover',
			placement: 'top',
			container: 'body',
			trigger: 'manual',
			content: function () {
				let html = popover.html();
				//console.log(html);
				return html;
			}
		}).unbind("click").on('click', function () {

			if (!isPopoverOpened()) {
				//console.log('show on click');
				button.popover('show');
				button.popover('update');
			}

		}).unbind("mouseover").on('mouseover', function () {

			if (!isMouseAway() && !isPopoverOpened()) {

				//console.log('show on mouseover');
				button.popover('show');
				button.popover('update');
			}

		}).unbind("mouseleave").on('mouseleave', function () {
			//console.log('mouseleave');

			clearTimeout(leave_timer);

			leave_timer = setTimeout(function () {
				if (isMouseAway() && isPopoverOpened()) {
					//console.log('hided when mouse leave button');
					button.popover('hide');
				}
			}, delay_leave);
		});

		function isPopoverOpened() {
			var attr = button.attr('aria-describedby');

			if (typeof attr !== typeof undefined && attr !== false) {
				return true;
			} else
				return false;
		}

		function isMouseAway() {
			var attr = button.attr('aria-describedby');

			if (!button.is(':hover') && ($('#' + attr + ':hover').length == 0)) {
				return true;
			}
		}

		button.on('shown.bs.popover', function () {
			var window = $('#' + button.attr('aria-describedby'));

			window.unbind("mouseleave").on('mouseleave', function () {
				//console.log('window mouseleave');

				clearTimeout(leave_timer);

				leave_timer = setTimeout(function () {
					if (isMouseAway() && isPopoverOpened()) {
						//console.log('hided when mouse leave button');
						button.popover('hide');
					}
				}, delay_leave);
			});

			var up_button = window.find('.up');
			var up_button = up_button.add(popover.find('.up'));

			var down_button = window.find('.down');
			var down_button = down_button.add(popover.find('.down'));

			up_button.unbind('click').bind('click', function () {

				up_active();
				before_send();

				$.ajax({
					method: "GET",
					url: '/books/' + book_id + '/keywords/' + book_keyword_id + '/vote/1'
				}).done(function (msg) {
					result(msg);
				}).fail(function () {
					up_active_remove();
					down_active_remove();
				});
			});

			down_button.unbind('click').bind('click', function () {

				down_active();
				before_send();

				$.ajax({
					method: "GET",
					url: '/books/' + book_id + '/keywords/' + book_keyword_id + '/vote/-1'
				}).done(function (msg) {
					result(msg);
				}).fail(function () {
					up_active_remove();
					down_active_remove();
					enable_buttons();
				});
			});

			function up_active() {
				up_button.addClass('active')
					.addClass('btn-success').removeClass('btn-light');

				down_active_remove();
			}

			function up_active_remove() {
				up_button.removeClass('active')
					.addClass('btn-light').removeClass('btn-success');
			}

			function down_active() {
				down_button.addClass('active')
					.addClass('btn-danger').removeClass('btn-light');

				up_active_remove();
			}

			function down_active_remove() {
				down_button.removeClass('active')
					.addClass('btn-light').removeClass('btn-danger');
			}

			function result(vote) {
				console.log(vote);

				vote.vote = parseInt(vote.vote);

				if (vote.vote > 0) {
					up_active();
					down_active_remove();
				} else if (vote.vote < 0) {
					down_active();
					up_active_remove();
				} else {
					up_active_remove();
					down_active_remove();
				}

				enable_buttons();
			}

			function before_send() {
				disable_buttons();
			}

			function disable_buttons() {
				up_button.attr('disabled', 'disabled');
				down_button.attr('disabled', 'disabled');
			}

			function enable_buttons() {
				up_button.removeAttr('disabled');
				down_button.removeAttr('disabled');
			}
		});
	});


	$('.files').find('.file').each(function () {

		let i = $(this);
		let id = i.data('id');

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
				url: "/books/" + window.sharedData.book_id + "/files/" + id
			}).done(function (msg) {
				//console.log(msg);
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
	});

	// оценки книг
	/*
		$('#rating').rating({
			filled: 'glyphicon glyphicon-star-empty orange',
			filledSelected: 'glyphicon glyphicon-star orange',
			empty: 'glyphicon glyphicon-star-empty orange',
			start: 0,
			stop: 10,
			extendSymbol: function (rate) {
				$(this).tooltip({
					container: 'body',
					placement: 'bottom',
					title: 'Оценка  ' + rate
				});
			}
		}).on('change', function () {
			//alert('Rating: ' + $(this).val());

			$.ajax({
				method: "GET",
				url: '/books/' + window.sharedData.book_id + '/vote/' + $(this).val()
			}).done(function (msg) {
				location.reload();
			});
		});


		$('.removeVote').click(function(){
			$.ajax({
				method: "GET",
				url: '/books/' + window.sharedData.book_id + '/vote/remove'
			}).done(function (msg) {
				location.reload();
			});
		});
	*/
	// комментарии


	form_create_comment.ajaxForm({
		dataType: 'json',
		beforeSend: function (data) {
			form_create_comment.addClass('loading-cap');
		},
		success: function (item) {

			form_create_comment.removeClass('loading-cap');
			form_create_comment.trigger("reset").trigger("reset");

			load_comments('/books/' + id + '/?page=1');
		},
		error: function (data) {
			form_create_comment.removeClass('loading-cap');
		}
	});

	top_comments.find('.item').each(function () {
		comment($(this));
	});

	on_list_ready();

	comments.on('click', '.pagination a', function (e) {
		e.preventDefault();

		let url = $(this).attr('href');

		load_comments(url);
	});

	function load_comments(url) {
		comments.addClass("loading-cap");

		window.history.pushState("", "", url);

		$.ajax({
			url: url, data: {'ajax': true}
		}).done(function (data) {
			comments.removeClass("loading-cap");
			comments.html(data);

			on_list_ready();

			$('html, body').animate({
				scrollTop: comments.offset().top - 80
			}, 100);

		}).fail(function () {
			if (jqXHR.status == 401) location.reload();
		});
	}

	function on_list_ready() {
		comments.find('.item').each(function () {

			comment($(this));
		});
	}

	var $btn_stop_reading = $('[data-button=stop-reading]:first');
	var $btn_reading = $('[data-button=reading]:first');
	var $btn_contune_reading = $('[data-button=contune-reading]:first');

	$btn_stop_reading.click(function () {

		$btn_stop_reading.addClass("loading-cap");
		$btn_contune_reading.addClass("loading-cap");

		$.ajax({
			url: '/books/' + id + '/stop_reading'
		}).done(function (data) {

			$btn_stop_reading.removeClass("loading-cap");
			$btn_contune_reading.removeClass("loading-cap");

			$btn_stop_reading.hide();
			$btn_contune_reading.hide();

			$btn_reading.show();

		}).fail(function () {
			$btn_stop_reading.removeClass("loading-cap");
			$btn_contune_reading.removeClass("loading-cap");

			if (jqXHR.status == 401) location.reload();
		});
	});

	const $btn_change_the_date_of_rating = $('#change_the_date_of_rating').first();
	const $date_of_rating = $('#date_of_rating').first();

	$btn_change_the_date_of_rating.unbind('click').on('click', function (event) {
		event.preventDefault();

		const url = $btn_change_the_date_of_rating.attr('href');

		$btn_change_the_date_of_rating.addClass('disabled');

		$.ajax({
			url: url
		}).done(function (html) {

			const dialog = bootbox.dialog({
				show: false,
				message: html
			});

			const errors = dialog.find('.errors');

			dialog.on('shown.bs.modal', function (event) {

				console.log('show');

				const form = dialog.find('form');

				form.ajaxForm({
					beforeSend: function (data) {
						form.addClass('loading-cap');
					},
					success: function (html) {

						form.removeClass('loading-cap');

						dialog.modal('hide');

						$date_of_rating.html(html);

					},
					error: function (data) {

						console.log('date', data);

						let output = '<ul>';

						$.each(data.responseJSON.errors, function (key, array) {

							$.each(array, function (key, value) {
								output += '<li>' + value + '</li>';
							});
						});

						output += '</ul>';

						errors.html('<div class="alert alert-danger">' + output + '</div>');

						form.removeClass('loading-cap');
					}
				});
			});

			dialog.modal('show');

		}).fail(function () {

		}).always(function () {
			$btn_change_the_date_of_rating.removeClass('disabled');
		});
	});


	const $btn_change_the_date_of_read_status = $('#change_the_date_of_read_status').first();
	const $date_of_read_status = $('#date_of_read_status').first();

	$btn_change_the_date_of_read_status.unbind('click').on('click', function (event) {
		event.preventDefault();

		const url = $btn_change_the_date_of_read_status.attr('href');

		$btn_change_the_date_of_read_status.addClass('disabled');

		$.ajax({
			url: url
		}).done(function (html) {

			const dialog = bootbox.dialog({
				show: false,
				message: html
			});

			const errors = dialog.find('.errors');

			dialog.on('shown.bs.modal', function (event) {

				console.log('show');

				const form = dialog.find('form');

				form.ajaxForm({
					beforeSend: function (data) {
						form.addClass('loading-cap');
					},
					success: function (html) {

						form.removeClass('loading-cap');

						dialog.modal('hide');

						$date_of_read_status.html(html);

					},
					error: function (data) {

						console.log('date', data);

						let output = '<ul>';

						$.each(data.responseJSON.errors, function (key, array) {

							$.each(array, function (key, value) {
								output += '<li>' + value + '</li>';
							});
						});

						output += '</ul>';

						errors.html('<div class="alert alert-danger">' + output + '</div>');

						form.removeClass('loading-cap');
					}
				});
			});

			dialog.modal('show');

		}).fail(function () {

		}).always(function () {
			$btn_change_the_date_of_read_status.removeClass('disabled');
		});
	});
}




