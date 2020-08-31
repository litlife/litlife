@push('ckeditor')
	<script src="//cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.2/ckeditor.js" type="text/javascript"></script>

	@env('local')
	<script type="text/javascript">
		CKEDITOR.timestamp = '{{ uniqid() }}';
	</script>
	@endenv

	<script type="text/javascript">

		let elements = document.getElementsByClassName("editor");

		for (let i = 0; i < elements.length; i++) {

			let element = elements[i];

			@auth
			CKEDITOR.addCss('html{font-size:{{ auth()->user()->setting->font_size_px }}px;}');
			@if (!empty(auth()->user()->setting->font_family))
			CKEDITOR.addCss('body { font-family: \'{{ auth()->user()->setting->font_family }}\'; }');
			@endif
			@endauth

			CKEDITOR.addCss('html { padding: 20px; }');
			CKEDITOR.addCss('body { background-color:#FFF; }');

			CKEDITOR.replace(element, {
				language: "{{ strtolower(config('app.locale')) }}",
				height: "{{ empty($height) ? '500' : $height }}px",

				skin: 'moono-lisa',

				// Upload images to a CKFinder connector (note that the response type is set to JSON).
				uploadUrl: '/images/?responseType=json',

				// /images/create?CKEditor=ckeditor&CKEditorFuncNum=1&langCode=ru
				// Configure your file manager integration. This example uses CKFinder 3 for PHP.
				filebrowserImageUploadUrl: '/images/?responseType=json',
				filebrowserWindowWidth: '640',
				filebrowserWindowHeight: '480',
				smiley_columns: 16,
				extraPlugins: "image2,font,richcombo,floatpanel,listblock,panel,justify,magicline,sourcearea,filebrowser,showborders,contextmenu,table," +
					"image,autoembed,autolink,clipboard,notification,undo,uploadwidget,widget,filetools," +
					"notificationaggregator,toolbar,button,lineutils,widgetselection,removeformat,colorbutton",
				//extraPlugins: "uploadimage,image,autogrow,autoembed,autolink,youtube",

				// Configure the Enhanced Image plugin to use classes instead of styles and to disable the
				// resizer (because image size is controlled by widget styles or the image takes maximum
				// 100% of the editor width).
				image2_alignClasses: ['u-image-align-left', 'u-image-align-center', 'u-image-align-right'],
				image2_disableResizer: true,

				toolbarGroups: [
					{name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
					{name: 'clipboard', groups: ['clipboard', 'undo']},
					{name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing']},
					{name: 'forms', groups: ['forms']},
					{name: 'links', groups: ['links']},
					{name: 'insert', groups: ['insert']},
					{name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']},
					{name: 'styles', groups: ['styles']},
					{name: 'colors', groups: ['colors']},
					{name: 'tools', groups: ['tools']},
					{name: 'others', groups: ['others']},
					{name: 'about', groups: ['about']},
					{name: 'document', groups: ['mode', 'document', 'doctools']}
				],

				removePlugins: 'elementspath',

				removeButtons: 'About,ShowBlocks,Styles,Format,Font,FontSize,PageBreak,SpecialChar,Smiley,Flash,Anchor,Language,BidiRtl,BidiLtr,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,Blockquote,CreateDiv,Indent,Outdent,NumberedList,BulletedList,CopyFormatting,Form,Scayt,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,SelectAll,Find,Replace,Templates,Save,NewPage,Preview,Print',

				allowedContent: 'p{text-align}[style](u-image-align-center); ' +
					'strong; em; s; u; sub; sup; ol; li; ul; blockquote; ' +
					'img(u-image-align-left,u-image-align-right){text-align,width,height}[!src,alt,style]; ' +
					'table{border-spacing,border-collapse}[border,cellpadding,cellspacing,style,class]; caption; tbody; thead; tr; th{padding}[style]; td{padding}[style];' +
					'hr; ' +
					'iframe[allowfullscreen,frameborder,height,width,!src]; ' +
					'a[!href]; ' +
					'span{color}[style];',

				contentsCss:
					[
						'{{ mix('css/app.css', config('litlife.assets_path')) }}',
					],
			});
		}
	</script>
@endpush



