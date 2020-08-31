(function ($) {
	var methods =
		{
			init: function (options) {
				var self = this;

				var defaults = {
					timeout: 200,
					minDistanceToStartLoad: 400
				};

				self.load_in_progress = false;

				self.opts = $.extend(defaults, options);

				self.unbind('onShow').bind('onShow', self.opts.onShow);
				self.unbind('onAppend').bind('onAppend', self.opts.onAppend);
				self.unbind('onFail').bind('onFail', self.opts.onFail);

				var timer;

				$(window).scroll(function () {

					if (timer) {
						window.clearTimeout(timer);
					}

					timer = window.setTimeout(function () {
						self.infinityScroll('checkDistance');

					}, self.opts.timeout);
				});

				self.infinityScroll('checkDistance');

				return self;
			},

			checkDistance: function () {
				var self = this;

				// если элемент не видим то тоже ничего не делаем
				if (!self.is(":visible"))
					return self;

				console.log("is visible " + self.is(":visible"));

				// если дистанция до низа больше чем минимальная дистанция до загрузки, то ничего не делаем
				if (self.infinityScroll('getDistanceToBottom') > self.opts.minDistanceToStartLoad)
					return self;

				console.log('distance ' + self.infinityScroll('getDistanceToBottom') + ' | ' + self.opts.minDistanceToStartLoad);

				self.infinityScroll('load');

				return self;
			},

			load: function () {

				var self = this;

				if (!self.load_in_progress) {

					// проверяем функция это или селектор
					if (typeof self.opts.path === "function") {
						var url = self.opts.path(self);
					} else {
						var url = $(self.opts.path).attr('href');
					}

					// если url получен то продолжаем, а если нет то ничего не делаем

					if (url) {

						console.log('start load');

						self.load_in_progress = true;

						console.log(url);

						$.ajax({
							url: url,
							beforeSend: function (xhr) {

								var html = $(".page-load-status").find(".infinite-scroll-request").clone().wrap('<div>').parent().html();
								self.append(html);
							}

						}).done(function (html) {

							console.log('load done');

							self.load_in_progress = false;

							self.append(html);

							self.trigger('onAppend', [self]);

							self.find(".infinite-scroll-request").remove();

							self.infinityScroll('checkDistance');
						}).fail(function (jqXHR, textStatus, errorThrown) {

							self.trigger('onFail', [self, jqXHR, textStatus, errorThrown]);


						});
					} else {
						console.log('skip');
					}
				}
			},

			getDistanceToBottom: function () {
				var self = this;

				var scrollPosition = window.pageYOffset;
				var windowSize = window.innerHeight;
				var bodyHeight = self.height();

				return Math.max(bodyHeight - (scrollPosition + windowSize), 0);
			},
		};

	$.fn.infinityScroll = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Метод ' + method + ' в jQuery.infinityScroll не существует');
		}
	};

})(jQuery);