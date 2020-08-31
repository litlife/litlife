<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
@if (session('sisyphus_ok'))

	<script type="text/javascript">
		$(function () {
			window.sisyphus_remove_by_name('{{ session('sisyphus_ok') }}');
		});
	</script>

@endif
@stack('body_append')

<a id="back-to-top" href="#" class="btn btn-primary btn-lg back-to-top" role="button"
   title="{{ __('common.back_to_top') }}"
   data-toggle="tooltip" data-placement="left">
	<i class="fas fa-caret-up"></i>
</a>

<a id="to-bottom" href="#" class="btn btn-primary btn-lg to-bottom " role="button"
   title="{{ __('common.to_bottom') }}"
   data-toggle="tooltip" data-placement="left">
	<i class="fas fa-caret-down"></i>
</a>

<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
@stack('ckeditor')
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/i18n/{{ strtolower(config('app.locale')) }}.js"></script>
<!--
<script src="//cdnjs.cloudflare.com/ajax/libs/sisyphus.js/1.1.3/sisyphus.min.js"></script>
<script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
-->
@auth
	<script src="//cdnjs.cloudflare.com/ajax/libs/sceditor/2.1.3/jquery.sceditor.bbcode.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/sceditor/2.1.3/plugins/undo.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/sceditor/2.1.3/plugins/autoyoutube.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/sceditor/2.1.3/plugins/dragdrop.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.22.0/js/vendor/jquery.ui.widget.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.22.0/js/jquery.fileupload.min.js"></script>
@endauth
@stack('scripts_before_app')

<script src="{{ mix('js/app.js', config('litlife.assets_path')) }}"></script>
@stack('scripts')

<style type="text/css">

</style>

@auth
	<script type="text/javascript">

		(function () {
			'use strict';

			sceditor.locale['{{ config('app.locale') }}'] = {
				'Bold': '{{ __('sceditor.bold') }}',
				'Italic': '{{ __("sceditor.italic") }}',
				'Underline': '{{ __("sceditor.underline") }}',
				'Strikethrough': '{{ __("sceditor.strikethrough") }}',
				'Subscript': '{{ __("sceditor.subscript") }}',
				'Superscript': '{{ __("sceditor.superscript") }}',
				'Align left': '{{ __("sceditor.align_left") }}',
				'Center': '{{ __("sceditor.center") }}',
				'Align right': '{{ __("sceditor.align_right") }}',
				'Justify': '{{ __("sceditor.justify") }}',
				'Font Name': '{{ __("sceditor.font_name") }}',
				'Font Size': '{{ __("sceditor.font_size") }}',
				'Font Color': '{{ __("sceditor.font_color") }}',
				'Remove Formatting': '{{ __("sceditor.remove_formatting") }}',
				'Cut': '{{ __("sceditor.cut") }}',
				'Your browser does not allow the cut command. Please use the keyboard shortcut Ctrl/Cmd-X': '{{ __("sceditor.your_browser_does_not_allow_the_cut_command_please_use_the_keyboard_shortcut_ctrl_cmd_x") }}',
				'Copy': '{{ __("sceditor.copy") }}',
				'Your browser does not allow the copy command. Please use the keyboard shortcut Ctrl/Cmd-C': '{{ __("sceditor.your_browser_does_not_allow_the_cut_command_please_use_the_keyboard_shortcut_ctrl_cmd_c") }}',
				'Paste': '{{ __("sceditor.paste") }}',
				'Your browser does not allow the paste command. Please use the keyboard shortcut Ctrl/Cmd-V': '{{ __("sceditor.your_browser_does_not_allow_the_cut_command_please_use_the_keyboard_shortcut_ctrl_cmd_v") }}',
				'Paste your text inside the following box:': '{{ __("sceditor.paste_your_text_inside_the_following_box") }}',
				'Paste Text': '{{ __("sceditor.paste_text") }}',
				'Bullet list': '{{ __("sceditor.bullet_list") }}',
				'Numbered list': '{{ __("sceditor.numbered_list") }}',
				'Undo': '{{ __("sceditor.undo") }}',
				'Redo': '{{ __("sceditor.redo") }}',
				'Rows:': '{{ __("sceditor.rows") }}',
				'Cols:': '{{ __("sceditor.cols") }}',
				'Insert a table': '{{ __("sceditor.insert_a_table") }}',
				'Insert a horizontal rule': '{{ __("sceditor.insert_a_horizontal_rule") }}',
				'Code': '{{ __("sceditor.code") }}',
				'Insert a Quote': '{{ __("sceditor.insert_a_auote") }}',
				'Width (optional):': '{{ __("sceditor.width_optional") }}',
				'Height (optional):': '{{ __("sceditor.height_optional") }}',
				'Insert an image': '{{ __("sceditor.insert_an_image") }}',
				'E-mail:': '{{ __("sceditor.email") }}',
				'Insert an email': '{{ __("sceditor.insert_an_email") }}',
				'URL:': '{{ __("sceditor.url") }}',
				'Insert a link': '{{ __("sceditor.insert_a_link") }}',
				'Unlink': '{{ __("sceditor.unlink") }}',
				'More': '{{ __("sceditor.more") }}',
				'Insert an emoticon': '{{ __("sceditor.insert_an_emoticon") }}',
				'Video URL:': '{{ __("sceditor.video_url") }}',
				'Insert': '{{ __("sceditor.insert") }}',
				'Insert a YouTube video': '{{ __("sceditor.insert_a_youTube_video") }}',
				'Insert current date': '{{ __("sceditor.insert_current_date") }}',
				'Insert current time': '{{ __("sceditor.insert_current_time") }}',
				'Print': '{{ __("sceditor.print") }}',
				'View source': '{{ __("sceditor.view_source") }}',
				'Maximize': '{{ __("sceditor.maximize") }}',
				'Upload Image': '{{ __("sceditor.upload_image") }}',
				'Add Spoiler': '{{ __("sceditor.add_spoiler") }}',
				'Spoiler': '{{ __("sceditor.spoiler") }}',
				dateFormat: 'day.month.year'
			};
		})();

		var textareas = document.getElementsByClassName('sceditor');

		for ($a = 0; $a < textareas.length; $a++) {
			set_sceditor(textareas[$a]);
		}

		function set_sceditor(textarea) {

			var url = '{!! optional(\App\Variable::where('name', \App\Enums\VariablesEnum::SmilesJsonUrl)->first())->value !!}';

			$.ajax({
				url: url,
				dataType: "jsonp",
				jsonpCallback: "jsonp",
				success: function (smiles) {

					var smiles_asoc_array = [];

					smiles.forEach(function (smile) {
						smiles_asoc_array[smile.simple_form] = smile.full_url;
					});

					sceditor.create(textarea, {
						width: '100%',
						format: 'bbcode',
						emoticonsEnabled: false,
						plugins: 'spoiler,undo,mysmiles,autosave,autoyoutube,dragdrop,santizepaste',
						spellcheck: true,
						emoticons: {
							dropdown: smiles_asoc_array
						},
						locale: '{{ config('app.locale') }}-{{ strtoupper(config('app.locale')) }}',
						style: '{{ mix('css/sceditor_content.css', config('litlife.assets_path')) }}',
						toolbar: 'bold,italic,underline,strike,subscript,superscript|font,removeformat|left,center,right,justify|' +
							'cut,copy,paste|image,uploadImage|youtube|color|quote,spoiler,code,|smiles|table|undo,redo|bulletlist,orderedlist|' +
							'horizontalrule|link,unlink|maximize,source|undo,redo',
						autoUpdate: true,
						enablePasteFiltering: true,
						fonts: "{{ implode(',', config('litlife.available_fonts')) }}",
						parserOptions: {
							fixInvalidNesting: true,
							fixInvalidChildren: true
						},
						dragdrop: {
							allowedTypes: ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'],
							isAllowed: function (file) {
								return true;
							},
							handlePaste: true,
							handleFile: function (file, createPlaceholder) {
								var placeholder = createPlaceholder();

								asyncUpload(file).then(function (result) {

									var img = new Image();
									img.onload = function () {
										if (this.width > 500)
											this.width = 500;

										if (this.height > 500)
											this.height = 500;

										placeholder.insert('<br /> <img src="' + result.url + '" width="' + this.width + '" height="' + this.height + '" /> <br />');
									}
									img.src = result.url;

								}).catch(function () {
									placeholder.cancel();
								});

								function asyncUpload(file) {

									var form = new FormData();
									form.append('upload', file);

									return fetch('/images/?responseType=json', {
										method: 'post',
										body: form
									}).then(function (response) {
										return response.json();
									}).then(function (result) {
										console.log(result);
										if (result.uploaded) {
											return result;
										}

										throw 'Upload error';
									});
								}
							}
						}
					});

					var instance = sceditor.instance(textarea)
						.nodeChanged(function (e) {
							var val = instance.getBody().innerHTML;
							//console.log(val);
							if (val == "<p><br></p>" || val == "<p></p>" || val == "<p><br /></p>") {
								//console.log('set');
								instance.setWysiwygEditorValue("<div><br /></div>");
							}
						}).css('html{font-size:{{ auth()->user()->setting->font_size_px }}px; } @if (!empty(auth()->user()->setting->font_family)) body { font-family: \'{{ auth()->user()->setting->font_family }}\'; } @endif');

					instance.emoticons(true);

				}
			});

		}

		{{--
		window.Echo.private('user.50000')
			.listen('TestEvent', (e) => {
				console.log(e);
			})
			.listen('NewPersonalMessageReceiving', (e) => {
				console.log(e);
			});

		window.Echo.private('App.User.50000')
			.notification((notification) => {
				switch (notification.type) {
					case "App\\Notifications\\NewPersonalMessage":


						console.log(notification);

					function onShowNotification() {
						console.log('notification is shown!');
					}

					function onCloseNotification() {
						console.log('notification is closed!');
					}

					function onClickNotification() {
						console.log('notification was clicked!');
					}

					function onErrorNotification() {
						console.error('Error showing notification. You may need to request permission.');
					}

					function onPermissionGranted() {
						console.log('Permission has been granted by the user');
						doNotification();
					}

					function onPermissionDenied() {
						console.warn('Permission has been denied by the user');
					}

					function doNotification() {
						var myNotification = new Notify(notification.title, {
							body: '123',
							tag: 'My unique id',
							notifyShow: onShowNotification,
							notifyClose: onCloseNotification,
							notifyClick: onClickNotification,
							notifyError: onErrorNotification,
							timeout: 10
						});

						myNotification.show();
					}

						if (!Notify.needsPermission) {
							doNotification();
						} else if (Notify.isSupported()) {
							Notify.requestPermission(onPermissionGranted, onPermissionDenied);
						}


						break;
				}
			});
	--}}

	</script>
@endauth

@stack('ads_js')







