(function ($) {
	var methods =
		{
			init: function (options) {
				var self = this;

				var defaults = {timeout: 1000};

				self.opts = $.extend(defaults, options);

				self.unbind('onShow').bind('onShow', self.opts.onShow);
				self.unbind('onChange').bind('onChange', self.opts.onChange);

				self.find("select").unbind("change").change(function () {
					self.formChange('inputChange');
				});

				self.find("[type=checkbox]").unbind("change").change(function () {
					self.formChange('inputChange');
				});

				var last_value = null;

				self.find("input, textarea").unbind("keypress").keydown(function (e) {
					// сохраняем значение при нажатии клавиши
					last_value = $.trim($(this).val());
				}).keyup(function (e) {
					// сравниваем после отпускания клавиши
					if (last_value != $.trim($(this).val())) {
						self.formChange('inputChange');
						// теперь обнуляем значение
					}
					//last_value = null;
				});

				return self;
			},

			inputChange: function () {
				var self = this;

				self.trigger('onChange', self);

				if (self.last_time_timeout_id) {
					clearTimeout(self.last_time_timeout_id);
				}

				self.last_time_timeout_id = setTimeout(function () {
					self.trigger('onShow', self);
				}, self.opts.timeout);

				return self;
			},
		};

	$.fn.formChange = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Метод ' + method + ' в jQuery.formChange не существует');
		}
	};

})(jQuery);