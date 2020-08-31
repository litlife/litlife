(function ($) {
	var methods =
		{
			init: function (options) {
				var self = this;

				if (!self.length) return self;

				var defaults = {
					open_on_click_inside: true
				};

				self.opts = $.extend(defaults, options);

				self.unbind('onExpand').bind('onExpand', self.opts.onExpand);
				self.unbind('onCompress').bind('onCompress', self.opts.onCompress);
				//self.unbind('beforeShow').bind('beforeShow', self.opts.beforeShow);
				//self.unbind('beforeHide').bind('beforeHide', self.opts.beforeHide);

				//console.log(self.htmlExpand('getHeight'));
				//console.log(self.htmlExpand('getScrollHeight'));
				self.htmlExpand('hideExpandButton');
				self.htmlExpand('hideCompressButton');

				if (self.htmlExpand('getScrollHeight') > self.htmlExpand('getHeight')) {
					//console.log('expand_button show');

					self.htmlExpand('showExpandButton');
				}

				self.on('lazyloaded', function (e) {

					console.log(self.htmlExpand('getScrollHeight') + ' ' + self.htmlExpand('getHeight'));

					if (self.htmlExpand('getScrollHeight') > self.htmlExpand('getHeight')) {
						self.htmlExpand('showExpandButton');
						self.htmlExpand('hideCompressButton');
					} else {
						self.htmlExpand('hideExpandButton');
					}
				});

				self.height_before_expand = self.htmlExpand('getHeight');

				self.opts.expand_button.click(function () {
					$('[data-toggle=tooltip]').tooltip('hide');
					self.htmlExpand('expand');
				});

				self.opts.compress_button.click(function () {
					$('[data-toggle=tooltip]').tooltip('hide');
					self.htmlExpand('compress');
				});

				if (self.opts.open_on_click_inside) {
					self.click(function () {
						$('[data-toggle=tooltip]').tooltip('hide');
						self.htmlExpand('expand');
					});
				}

				return self;
			},

			expand: function () {
				var self = this;

				console.log('expand');

				if (self.htmlExpand('getScrollHeight') > self.htmlExpand('getHeight')) {

					self.htmlExpand('hideExpandButton');
					self.htmlExpand('showCompressButton');

					self.css('max-height', 'none')
						.css('overflow-y', 'visible');

					self.trigger('onExpand', self);
				}

				return self;
			},

			compress: function () {
				var self = this;

				console.log('compress');

				self.htmlExpand('hideCompressButton');

				self.css('max-height', self.height_before_expand + 'px')
					.css('overflow-y', 'hidden');

				if (self.htmlExpand('getScrollHeight') > self.htmlExpand('getHeight')) {
					self.htmlExpand('showExpandButton');
				}

				self.trigger('onCompress', self);

				return self;
			},

			showExpandButton: function () {
				var self = this;
				console.log('showExpandButton');
				self.opts.expand_button.show();
			},

			hideExpandButton: function () {
				var self = this;
				console.log('hideExpandButton');
				self.opts.expand_button.hide();
			},

			showCompressButton: function () {
				var self = this;
				console.log('showCompressButton');
				self.opts.compress_button.show();
			},

			hideCompressButton: function () {
				var self = this;
				console.log('hideCompressButton');
				self.opts.compress_button.hide();
			},

			getHeight: function () {
				return Math.round(this.prop('clientHeight'));
			},

			getScrollHeight: function () {
				return this.prop('scrollHeight');
			},
		};

	$.fn.htmlExpand = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method ' + method + ' doesnt exists');
		}
	};

})(jQuery);