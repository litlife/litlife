export default function Like() {

	let self = this;

	this.init = function (like) {

		self.like = like;

		self.like.btn_liked = self.like.find('button.liked');
		self.like.btn_empty = self.like.find('button.empty');

		self.like.counter = self.like.find(".counter");
		self.like.last_liked_status = self.like.data('liked');

		self.likeable_type = self.like.data('likeable-type');
		self.likeable_id = self.like.data('likeable-id');
		self.likeable_create_user_id = self.like.data('likeable-create-user-id');

		self.mouseOverDelay = 1000;
		self.mouseLeaveDelay = 1000;

		self.like.btn_liked.unbind("click").on('click', self.onClick);
		self.like.btn_empty.unbind("click").on('click', self.onClick);

		self.like.popover({
			boundary: 'window',
			placement: 'top',
			html: true,
			trigger: 'manual',
			animation: false,
		});

		self.like.unbind("shown.bs.popover").bind('shown.bs.popover', self.onPopoverShown);
		self.like.unbind("mouseover").bind('mouseover', self.onButtonMouseOver);
		self.like.unbind("mouseleave").bind('mouseleave', self.onButtonMouseLeave);
	};

	this.onClick = function () {

		console.log('onClick');

		let $btn = $(this);

		if (self.isLiked())
			self.decrementCounter();
		else
			self.incrementCounter();

		$btn.addClass('loading-cap');

		self.setPopoverHtml('<i class="fas fa-spinner fa-spin"></i>');

		if (self.getCount() > 0) {
			self.showPopover();
		}

		self.request($btn);
	};

	this.onButtonMouseOver = function () {

		console.log('onButtonMouseOver');

		clearTimeout(self.mouseOverTimer);

		self.mouseOverTimer = setTimeout(function () {
			if (!self.isMouseAway() && self.getCount() > 0) {

				self.load();

				if (self.isDataLoaded()) {
					self.showPopover();
				}
			}
		}, self.mouseOverDelay);
	};

	this.onButtonMouseLeave = function () {
		console.log('onButtonMouseLeave');

		clearTimeout(self.mouseLeaveTimer);

		self.mouseLeaveTimer = setTimeout(function () {
			self.hidePopoverIfMouseAwayAndPopoverOpened();
		}, self.mouseLeaveDelay);
	};

	this.onPopoverShown = function () {
		console.log('onPopoverShown');

		self.getPopover().unbind('mouseleave').bind('mouseleave', self.onPopoverMouseLeave);
	};

	this.onPopoverMouseLeave = function () {

		console.log('onPopoverMouseLeave');

		clearTimeout(self.mouseLeaveTimer);

		self.mouseLeaveTimer = setTimeout(function () {
			self.hidePopoverIfMouseAwayAndPopoverOpened();
		}, self.mouseLeaveDelay);
	};

	this.hidePopoverIfMouseAwayAndPopoverOpened = function () {
		console.log('hidePopoverIfMouseAwayAndPopoverOpened');
		if (self.isMouseAway()) {
			self.hidePopover();
		}
	};

	this.request = function ($btn) {
		$.ajax({
			method: "GET",
			url: "/likes/" + self.likeable_type + "/" + self.likeable_id + ""
		}).done(self.onDoneLoadClick)
			.fail(self.fail)
			.always(function () {
				$btn.removeClass('loading-cap');
			});
	};

	this.onDoneLoadClick = function (result) {
		console.log('onDoneLoadClick');
		console.log(result);

		if (!result.like.id) {
			self.toggleToEmpty();
		} else {

			if (result.like.deleted_at)
				self.toggleToEmpty();
			else
				self.toggleToLiked();
		}

		self.setCount(result.item.like_count);

		if (self.getCount() < 1) {
			self.hidePopover();
		} else {
			self.refreshTooltipContent(result.latest_likes_html);
		}
	};

	this.fail = function (result) {
		self.hidePopover();

		if (self.isLiked())
			self.incrementCounter();
		else
			self.decrementCounter();
	};

	this.refreshTooltipContent = function (html = '') {

		console.log('refreshTooltipContent');

		if (html !== '') {
			self.dataLoaded();
			self.setPopoverHtml(html);
		} else {
			self.dataNotLoaded();
			self.load();
		}
	};

	this.load = function () {

		if (self.like.data('ajax-start')) {
			console.log('ajax already started');
			return;
		}

		if (self.isDataLoaded()) {
			console.log('ajax already loaded');
			return
		}

		$.ajax({
			url: "/likes/" + self.likeable_type + '/' + self.likeable_id + '/tooltip',
			cache: true,
			beforeSend: function (xhr) {
				console.log('beforeSend');
				self.showPopover();

				self.setPopoverHtml('<i class="fas fa-spinner fa-spin"></i>');

				self.like.data('ajax-start', true);
			}
		}).done(function (html) {

			console.log('onDoneLoadLikeList');

			self.setPopoverHtml(html);
			self.dataLoaded();

		}).always(function () {
			self.like.data('ajax-start', false);
		});
	};

	this.incrementCounter = function () {
		console.log("incrementCounter");

		let count = self.getCount();
		self.setCount(count + 1);
	};

	this.decrementCounter = function () {
		console.log("decrementCounter");

		let count = self.getCount();
		self.setCount(count - 1);
	};

	this.setCount = function (count) {

		count = parseInt(count);

		self.like.counter.text(count);

		if (count < 1)
			self.like.counter.hide();
		else
			self.like.counter.show();
	};

	this.toggleToLiked = function () {
		self.like.btn_liked.show();
		self.like.btn_empty.hide();
	};

	this.toggleToEmpty = function () {
		self.like.btn_liked.hide();
		self.like.btn_empty.show();
	};

	this.setPopoverHtml = function (html) {
		var div = $('<div>' + html + '</div>');

		div.find('img.lazyload').each(function () {
			$(this).attr('src', $(this).data('src'));
		});

		var attr = self.like.attr('aria-describedby');

		if (self.isPopoverOpened())
			$("#" + attr).find('.popover-body').html(div.html());

		self.like.attr('data-content', div.html());
		self.updatePopover();
	};

	this.dataLoaded = function () {
		self.like.data('data-loaded', true);
	};

	this.dataNotLoaded = function () {
		self.like.data('data-loaded', false);
	};

	this.isDataLoaded = function () {
		return !!self.like.data('data-loaded');
	};

	/*
	Если окно открыто, то возвращает true , если нет то false
	*/
	this.isPopoverOpened = function () {
		var attr = self.like.attr('aria-describedby');

		return typeof attr !== typeof undefined && attr !== false;
	};

	/*
	Возвращает true, если указатель находится не на лайке и не на всплывающем окне
	 */
	this.isMouseAway = function () {
		var attr = self.like.attr('aria-describedby');

		if (!self.like.is(':hover') && ($('#' + attr + ':hover').length === 0)) {
			return true;
		} else {
			return false;
		}
	};

	/*
	Возвращает true, если кнопка "лайкнута", то есть лайк поставлен
	*/
	this.isLiked = function () {
		return !!self.like.btn_liked.is(':visible');
	};

	/*
	Возвращает true, если это тач устройство
	*/
	this.isTouchDevice = function () {
		return window.isTouchDevice();
	};

	this.getCount = function () {
		var count = parseInt(self.like.counter.first().text(), 10);
		console.log('count: ' + count);
		return count;
	};

	this.getPopover = function () {
		var attr = self.like.attr('aria-describedby');
		return $('#' + attr + '').first();
	};

	this.showPopover = function () {
		if (!self.isPopoverOpened()) {
			self.like.popover('show');
			self.like.popover('update');
		}
	};

	this.hidePopover = function () {
		if (self.isPopoverOpened()) {
			self.like.popover('hide');
		}
	};

	this.updatePopover = function () {
		if (self.isPopoverOpened()) {
			self.like.popover('update');
		}
	};
}