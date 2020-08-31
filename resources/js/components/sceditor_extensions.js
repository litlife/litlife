import DOMPurify from 'dompurify';

if (typeof sceditor !== 'undefined') {
	sceditor.command.set("spoiler", {
		exec: function () {
			var lang = document.documentElement.getAttribute('lang');
			var Spoiler = sceditor.locale[lang].Spoiler;
			//this.insert('<div class="spoiler">', '</div>');
			this.insert('[spoiler=' + Spoiler + ']', '[/spoiler]');
		},
		txtExec: function () {
			var lang = document.documentElement.getAttribute('lang');
			var Spoiler = sceditor.locale[lang].Spoiler;
			//this.insert('[spoiler]', '[/spoiler]');
			this.insert('[spoiler=' + Spoiler + ']', '[/spoiler]');
		},
		tooltip: 'Add Spoiler'
	});

	sceditor.formats.bbcode.set('spoiler', {

		tags: {
			div: {
				'class': ['bb_spoiler']
			}
		},

		quoteType: sceditor.BBCodeParser.QuoteType.always,

		allowedChildren: ['b', 'i', 'u', 'sub', 'sup', 'font', 'img', 'color', 'hr', 'left', 'right', 'center', 'url'],
		isInline: false,
		allowsEmpty: true,

		isSelfClosing: false,

		format: function (element, content) {

			console.log('format');

			var element = $(element);

			var attr = '',
				$desc = element.children(".desc:first");

			if ($desc.length === 1 && $desc.text())
				attr = '=' + $desc.text();
			else if (element.data('spoiler-id'))
				attr = '=' + element.data('spoiler-id');

			return '[spoiler' + attr + ']' + content + '[/spoiler]';
		},

		html: function (token, attrs, content) {
			console.log('html');

			var idspoiler = '', spo = '';

			if (attrs.defaultattr) {
				spo = '<div class="bb_spoiler_title sceditor-ignore">' + attrs.defaultattr + '</div>';
				idspoiler = attrs.defaultattr;
			}

			return '<div class="bb_spoiler" data-spoiler-id="' + idspoiler + '">' + spo + '<div class="bb_spoiler_text">' + content + '</div></div>';

			/*
					return '<div class="bb_spoiler">' +
						'<div class="bb_spoiler_title" style="cursor:text">' + (typeof attrs.defaultattr !== 'undefined' ? attrs.defaultattr : "Спойлер") + '</div>' +
						'<div class="bb_spoiler_text" style="display: block;">' + content + '</div>' +
						'</div>';
					*/
		},
		tooltip: 'Spoiler'
	});

	sceditor.command.set("smiles", {
		exec: function () {
			smilesDialog(this);
		},
		txtExec: function () {
			smilesDialog(this);
		},
		tooltip: 'Insert an emoticon'
	});

	function smilesDialog(sceditor) {
		var dialog = bootbox.dialog({
			message: ' ',
			closeButton: true,
			backdrop: true,
			animate: false,
			size: 'large',
			buttons: {
				cancel: {
					label: '<i class="fas fa-times"></i>',
					className: 'btn-info',
					callback: function () {

					}
				}
			}

		}).init(function () {
			console.log('init');

			var body = dialog.find('.bootbox-body');

			var message = '';

			for (var simple_form in sceditor.opts.emoticons.dropdown) {
				message += '<div style="width:50px; height:50px;" class="emoticon d-inline-block text-center" data-simple-form="' + simple_form + '">';
				message += '<img data-src="' + sceditor.opts.emoticons.dropdown[simple_form] + '" class="lazyload" style="max-width:40px; cursor:pointer"/>';
				message += '</div>';
			}

			body.html(message);

			body.find('.emoticon').each(function () {

				var smile = $(this);
				var simple_form = smile.data('simple-form');

				smile.unbind('click').click(function () {

					var url = smile.find('img').attr('src');

					bootbox.hideAll();

					sceditor.insert(' ' + simple_form + ' ');
				});
			});
		});
	}

	sceditor.command.set("uploadImage", {
		exec: function () {
			uploadImage(this);
		},
		txtExec: function () {
			uploadImage(this);
		},
		tooltip: 'Upload Image'
	});

	function uploadImage(sceditor) {
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
				url: "/images/create"
			}).done(function (result) {

				body.find('.spinner').remove();

				body.html(result);

				var fileupload = body.find('[type=file]');

				fileupload.fileupload({
					context: this,
					dataType: 'json',
					limitMultiFileUploadSize: '100000000'
				}).bind('fileuploadfail', function (e, data) {

					console.log('fileuploadfail');

				}).bind('fileuploaddone', function (e, data) {

					console.log(data.result);

					if (data.errors) {
						var response = JSON.parse(data.errors);
						var errorString = '<ul>';
						$.each(response.errors, function (key, value) {
							errorString += '<li>' + value + '</li>';
						});
						errorString += '</ul>';
					} else {

						var image = data.result;

						image.url = image.url + '';

						bootbox.hideAll();

						var img = new Image();
						img.onload = function () {
							if (this.width > 500)
								this.width = 500;

							if (this.height > 500)
								this.height = 500;

							if (sceditor.inSourceMode()) {
								sceditor.insert("\r\n" + '[img=' + this.width + 'x' + this.height + ']' + image.url + '[/img]' + "\r\n");
							} else {
								sceditor.wysiwygEditorInsertHtml('<br /> <img src="' + image.url + '" width="' + this.width + '" height="' + this.height + '" /> <br />');
							}
						};
						img.src = image.url;
					}
				});

			}).always(function () {

			});
		});
	}

	sceditor.formats.bbcode.set('img', {

		tags: {
			'img': null
		},

		isInline: true,
		allowsEmpty: false,
		isSelfClosing: false,
		allowedChildren: ['#'],
		skipLastLineBreak: true,

		format: function (element, content) {
			console.log('format');

			// if element emoticon
			if (element.hasAttribute('data-sceditor-emoticon'))
				return content;

			var element = $(element);

			var href = element.attr('src');

			//console.log(element);

			var width = element.attr('width');
			var height = element.attr('height');

			if ((width) && (height)) {
				width = element.css('width');
				height = element.css('height');
			}

			if (!width) {
				width = element.width();
			}

			if (!height) {
				height = element.height();
			}

			width = parseInt(width, 10);
			height = parseInt(height, 10);

			console.log(width);
			console.log(height);

			console.log(element.width());
			console.log(element.height());

			if ((width) && (height)) {
				return '[img=' + width + 'x' + height + ']' + href + '[/img]';
			} else if (width) {
				return '[img=' + width + ']' + href + '[/img]';
			} else {
				return '[img]' + href + '[/img]';
			}
		},

		html: function (token, attrs, href) {
			console.log('html');

			//console.log(token);
			//console.log(href);

			if (typeof attrs.defaultattr !== 'undefined') {
				//console.log(attrs.defaultattr);

				var matches = attrs.defaultattr.match(/^([0-9]+)x([0-9]+)$/);

				if (matches !== null) {
					var width = matches[1];
					var height = matches[2];
				}

				var matches = attrs.defaultattr.match(/^([0-9]+)$/);

				if (matches !== null) {
					var width = matches[1];
				}
			}

			//console.log(width);
			//console.log(height);

			var addOrReplaceParam = function (uri, key, value) {
				var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
				var separator = uri.indexOf('?') !== -1 ? "&" : "?";
				if (uri.match(re)) {
					return uri.replace(re, '$1' + key + "=" + value + '$2');
				} else {
					return uri + separator + key + "=" + value;
				}
			};

			var html = '<img ';
			html += 'srcset="';
			html += addOrReplaceParam(addOrReplaceParam(href, "w", "400"), "q", "85") + ' 400w,';
			html += addOrReplaceParam(addOrReplaceParam(href, "w", "700"), "q", "80") + ' 700w,';
			html += addOrReplaceParam(addOrReplaceParam(href, "w", "1000"), "q", "75") + ' 1000w ';
			html += '"';
			html += ' sizes="(max-width: 400px) 400px, (max-width: 700px) 700px, (max-width: 1000px) 1000px, 1000px"';
			html += (typeof width !== 'undefined' ? "width=" + width + " " : "");
			html += (typeof height !== 'undefined' ? "height=" + height + " " : "");
			html += ' src="' + href + '" />';

			return html;
		},
		tooltip: 'Image'
	});

	sceditor.formats.bbcode.set('size', {

		tags: {
			'font': {
				'size': null
			}
		},
		isInline: true,
		allowsEmpty: false,
		isSelfClosing: false,
		html: function (token, attrs, text) {
			return text;
		}
	});


	sceditor.formats.bbcode.set('font', {

		quoteType: sceditor.BBCodeParser.QuoteType.auto,

		format: function (element, content) {

			//console.log('format');

			var fonts_lower = this.opts.fonts.trim().split(/,/g).map(function (font) {
				return font.toLowerCase();
			});

			var fontFamilyStr = element.style.getPropertyValue('font-family');
			fontFamilyStr.trim();

			if (!fontFamilyStr)
				fontFamilyStr = element.getAttribute('face');

			console.log(fontFamilyStr);

			fontFamilyStr = fontFamilyStr.replace(/"/gi, '\'');

			var fontFamilyFilteredArray = fontFamilyStr.trim().replace(/"/gi, '\'')
				.split(/,\s{1,}/g)
				.map(function (font) {
					return font.replace(/'/ig, '');
				}).filter(function (font) {
					if (fonts_lower.indexOf(font.toLowerCase()) >= 0)
						return true;
				});

			fontFamilyStr = fontFamilyFilteredArray.map(function (font) {
				if (font.match(/\s/i))
					return "'" + font + "'";
				else
					return font;
			}).join(', ');

			fontFamilyStr.trim();

			if (fontFamilyStr)
				return '[font="' + fontFamilyStr + '"]' + content + '[/font]';
			else
				return '[font]' + content + '[/font]';
		},

		html: function (token, attrs, content) {

			//console.log('html');
			if (attrs.defaultattr) {
				var fontFamily = attrs.defaultattr.replace(/"/gi, '\'');
				//console.log(fontFamily);
				var fontFamilyArray = fontFamily.split(',[\ ]+');
				//console.log(fontFamilyArray);
				return '<span style="font-family: ' + attrs.defaultattr + '">' + content + '</span>';
			} else {
				return '<span>' + content + '</span>';
			}
		}
	});

	sceditor.formats.bbcode.set('ul', {
		allowsEmpty: false,
		fixInvalidNesting: true,
		allowedChildren: ['li'],
		breakStart: false,
		breakEnd: false,
		format: function (element, content) {

			console.log(element, content);

			return '[ul]' + $.trim(content) + '[/ul]';
		},
		html: function (token, attrs, content) {

			console.log(token, attrs, content);

			let ul = $("<ul></ul>").html($.trim(content));
			let rows = ul.children('li');

			if (rows.length < 1)
				return '<ul><li>' + ul.html() + '</li></ul>';
			else {
				let ul = $("<ul></ul>");
				rows.each(function () {
					ul.append($(this));
				});
				return '<ul>' + ul.html() + '</ul>';
			}
		}
	});

	sceditor.formats.bbcode.set('ol', {
		allowsEmpty: false,
		fixInvalidNesting: true,
		allowedChildren: ['li'],
		breakStart: false,
		breakEnd: false,
		format: function (element, content) {

			console.log(element, content);

			return '[ol]' + $.trim(content) + '[/ol]';
		},
		html: function (token, attrs, content) {

			console.log(token, attrs, content);

			let ol = $("<ol></ol>").html($.trim(content));
			let rows = ol.children('li');

			if (rows.length < 1)
				return '<ol><li>' + ol.html() + '</li></ol>';
			else {
				let ol = $("<ol></ol>");
				rows.each(function () {
					ol.append($(this));
				});
				return '<ol>' + ol.html() + '</ol>';
			}
		}
	});

	sceditor.formats.bbcode.set('li', {
		breakStart: false,
		breakBefore: false,
		breakAfter: false,
		breakEnd: false,
		skipLastLineBreak: false
	});

	sceditor.formats.bbcode.set('url', {
		allowsEmpty: false,
		fixInvalidNesting: true,
		breakStart: false,
		breakEnd: false,
		format: function (element, content) {

			//console.log(element, content);

			if ($.trim(element.getAttribute('href')) === '')
				return content;

			return '[url=' + element.getAttribute('href') + ']' + $.trim(content) + '[/url]';
		},
		html: function (token, attrs, content) {

			//console.log(token, attrs, content);

			if ($.trim(attrs.defaultattr) === '')
				return '';

			if ($.trim(content) === '')
				return '';

			return ' <a href="' + $.trim(attrs.defaultattr) + '">' + $.trim(content) + '</a> ';
		}
	});

	var extend = sceditor.utils.extend;

	sceditor.plugins.santizepaste = function () {
		var plainTextEnabled = true;

		this.init = function () {
			var commands = this.commands;
			var opts = this.opts;

			if (opts && opts.plaintext && opts.plaintext.addButton) {
				plainTextEnabled = opts.plaintext.enabled;

				commands.pastetext = extend(commands.pastetext || {}, {
					state: function () {
						return plainTextEnabled ? 1 : 0;
					},
					exec: function () {
						plainTextEnabled = !plainTextEnabled;
					}
				});
			}
		};

		this.signalPasteRaw = function (data) {
			if (plainTextEnabled) {

				if (data.html && !data.text) {
					var div = document.createElement('div');
					div.innerHTML = data.html;
					data.text = div.innerText;
				}

				let html = DOMPurify.sanitize(data.html, {
					ALLOWED_TAGS: ['a', 'img', 'p', 'br', 'div', 'ul', 'ol', 'li'],
					ALLOWED_ATTR: ['href', 'src']
				});

				let container = $('<div/>').html(html);

				data.html = container.html();
			}
		};
	};
}

