export default function scrollToTopBottom() {

	let self = this;

	this.init = function () {

		self.delay = 300;

		self.timeout = 0;

		$(window).scroll(self.onScroll);

		self.setBtnScroolToTop($('#back-to-top').first());

		self.setBtnScrollToBottom($('#to-bottom').first());
	};

	this.setBtnScroolToTop = function (btn) {
		console.log('setBtnScroolToTop');
		self.btnScrollToTop = btn;

		self.btnScrollToTop.unbind('click')
			.bind('click', self.scrollToTop);
	};

	this.setBtnScrollToBottom = function (btn) {
		console.log('setBtnScrollToBottom');
		self.btnScrollToBottom = btn;

		self.btnScrollToBottom.unbind('click')
			.bind('click', self.scrollToBottom);
	};

	this.onScroll = function () {

		clearInterval(self.timeout);

		self.timeout = setTimeout(function () {

			let scrollTop = $(this).scrollTop();

			let scrollBottom = self.documentHeight() - self.getScrollPosition();

			console.log('scrollPosition:' + self.getScrollPosition() + '; documentHeight:' + self.documentHeight() + ' ');
			console.log('scrollTop:' + scrollTop + '; scrollBottom:' + scrollBottom + ' ');

			if (scrollTop > 50) {
				self.showBtnScrollToTop();
			} else {
				self.hideBtnScrollToTop();
			}

			if (scrollBottom > 50) {
				self.showBtnScrollToBottom();
			} else {
				self.hideBtnScrollToBottom();
			}

			console.log('isBtnTopVisible:' + self.isBtnTopVisible() + '; isBtnBottomVisible:' + self.isBtnBottomVisible() + ' ');

		}, self.delay);
	};

	this.showBtnScrollToTop = function () {
		self.btnScrollToTop.fadeIn(200);
		//self.css('bottom', 60);
	};

	this.hideBtnScrollToTop = function () {
		self.btnScrollToTop.fadeOut(200);
	};

	this.showBtnScrollToBottom = function () {
		self.btnScrollToBottom.fadeIn(200);
	};

	this.hideBtnScrollToBottom = function () {
		self.btnScrollToBottom.fadeOut(200);
	};

	this.isBtnTopVisible = function () {
		return self.btnScrollToTop.css('display') !== 'none';
	};

	this.isBtnBottomVisible = function () {
		return self.btnScrollToBottom.css('display') !== 'none';
	};

	this.scrollToTop = function () {
		console.log('scrollToTop');

		self.btnScrollToTop.tooltip('hide');
		self.btnScrollToBottom.tooltip('hide');

		$('body,html').animate({
			scrollTop: 0
		}, 200);

		self.hideBtnScrollToTop();
	};

	this.scrollToBottom = function () {
		console.log('scrollToBottom');

		self.btnScrollToTop.tooltip('hide');
		self.btnScrollToBottom.tooltip('hide');

		$('body,html').animate({
			scrollTop: self.documentHeight()
		}, 200);

		self.hideBtnScrollToBottom();
	};

	this.getScrollPosition = function () {
		return $(window).height() + $(window).scrollTop();
	};

	this.documentHeight = function () {
		return $(document).height();
	}
}