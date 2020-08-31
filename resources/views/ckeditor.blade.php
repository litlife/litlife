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

			CKEDITOR.plugins.addExternal('youtube', '/ckeditor/plugins/youtube/plugin.js');

			@auth
			CKEDITOR.addCss('html { font-size:{{ auth()->user()->setting->font_size_px }}px; }');
			@if (!empty(auth()->user()->setting->font_family))
			CKEDITOR.addCss('body { font-family: \'{{ auth()->user()->setting->font_family }}\'; }');
			@endif
			@endauth

			CKEDITOR.addCss('html { padding: 20px; }');
			CKEDITOR.addCss('body { background-color:#FFF; }');

			CKEDITOR.replace(element, {
				autoGrow_minHeight: "250",
				autoGrow_maxHeight: "{{ empty($height) ? 500 : $height }}",
				language: "{{ strtolower(config('app.locale')) }}",
				height: "{{ empty($height) ? 500 : $height }}px",
				skin: 'moono-lisa',

				// Upload images to a CKFinder connector (note that the response type is set to JSON).
				uploadUrl: '/images/?responseType=json',

				// /images/create?CKEditor=ckeditor&CKEditorFuncNum=1&langCode=ru
				// Configure your file manager integration. This example uses CKFinder 3 for PHP.
				filebrowserImageUploadUrl: '/images/?responseType=json',
				filebrowserWindowWidth: '640',
				filebrowserWindowHeight: '480',
				smiley_path: '{{ config('litlife.smiley_path') }}',
				smiley_images: [
					@foreach (App\Smile::regular()->get() as $smile)
						'{{ $smile->name }}',
					@endforeach
				],
				smiley_descriptions: [
					@foreach (App\Smile::regular()->get() as $smile)
						'{{ $smile->description }}',
					@endforeach
				],
				smiley_columns: 16,
				extraPlugins: "image2,font,richcombo,floatpanel,listblock,panel,justify,magicline,sourcearea,filebrowser,showborders,contextmenu,table," +
					"image,autoembed,autolink,clipboard,notification,undo,uploadwidget,widget,filetools," +
					"notificationaggregator,toolbar,button,lineutils,widgetselection,removeformat,youtube,colorbutton",
				//extraPlugins: "uploadimage,image,autogrow,autoembed,autolink,youtube",

				// Configure the Enhanced Image plugin to use classes instead of styles and to disable the
				// resizer (because image size is controlled by widget styles or the image takes maximum
				// 100% of the editor width).
				image2_alignClasses: ['u-image-align-left', 'u-image-align-center', 'u-image-align-right'],
				image2_disableResizer: true,

				toolbarGroups: [
					{name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
					{name: 'styles', groups: ['styles']},
					{name: 'clipboard', groups: ['undo', 'clipboard']},
					{name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']},
					{name: 'forms', groups: ['forms']},
					{name: 'links', groups: ['links']},
					{name: 'insert', groups: ['insert']},
					{name: 'colors', groups: ['colors']},
					{name: 'tools', groups: ['tools']},
					{name: 'others', groups: ['others']},
					{name: 'about', groups: ['about']},
					{name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing']},
					{name: 'document', groups: ['mode', 'document', 'doctools']}
				],

				removePlugins: 'image,elementspath',

				removeButtons: 'Save,Templates,Scayt,HiddenField,ImageButton,Button,Select,Textarea,TextField,Radio,Checkbox,Outdent,Indent,CreateDiv,BidiLtr,BidiRtl,Language,Anchor,Flash,Smiley,SpecialChar,PageBreak,Iframe,Styles,Format,ShowBlocks,About,NewPage,Preview,Print,BGColor',

				allowedContent: 'h1; h2; h3; h4; h5; h6; ' +
					'a[id,name,target,!href,class](u-title);' +
					'p{text-align}[style](u-image-align-center); strong; em; s; u; sub; sup; b; i; br; blockquote; ' +
					'span{color,background-color,font-size,font-family}[lang,style];' +
					'div(u-section-break,u-title,u-empty-line,u-subtitle,u-annotation,u-date,u-epigraph,u-text-author,u-poem,u-stanza,u-image-align-center,u-date,u-v)[class,id];' +
					'img(u-image-align-left,u-image-align-right){text-align,width,height}[!src,alt,width,height,style,class]; ' +
					'table{border-spacing,border-collapse}[border,cellpadding,cellspacing,style,class]; caption; tbody; thead; tr; th{padding}[style,abbr]; td{padding}[style,abbr]; tfoot; col[width,valign,align,span]; colgroup[width,valign,align,span];' +
					'hr(u-section-break)[class]; ' +
					'dl; dt; dd;' +
					'ol; li; ul; ' +
					'pre; code;' +
					'iframe[allowfullscreen,frameborder,width,height,!src];',

				contentsCss:
					[
						'{{ mix('css/app.css', config('litlife.assets_path')) }}',
					],
			});
		}
	</script>
@endpush



