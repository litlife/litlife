(function ($) {
	var methods =
		{
			init: function (options) {
				var self = this;

				var defaults = {timeout: 60000};

				self.opts = $.extend(defaults, options);

				// set last value of textarea
				self.last_value = self.val();
				self.last_time_saved = Date.now();

				self.keyup(function () {
					self.autosave('checkChange');
				});
				/*
								$( window ).unload(function() {
									self.autosave('save');
								});
								*/

				var timeoutId;

				timeoutId = setTimeout(function () {
					self.autosave('checkChange');
				}, 1000);

				return self;
			},

			// check if textarea changed?
			checkChange: function () {
				var self = this;

				if (self.last_value != self.val()) {
					// set last value of textarea
					self.last_value = self.val();
					self.autosave('changed');
				}


				return self;
			},

			// textarea changed
			changed: function () {
				var self = this;

				console.log('changed');
				self.trigger('onChange', self);

				console.log(Date.now());
				console.log(self.last_time_saved + self.opts.timeout);

				if (Date.now() > (self.last_time_saved + self.opts.timeout))
					self.autosave('save');

				return self;
			},

			// save
			save: function () {
				var self = this;

				console.log('save');

				self.last_time_saved = Date.now();

				self.trigger('onSave', self);

				return self;
			},
		};

	$.fn.autosave = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method ' + method + ' in jQuery.autosave doesn\'t exists');
		}
	};

})(jQuery);