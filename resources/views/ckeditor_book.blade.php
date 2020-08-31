@push('ckeditor')

	<script src="//cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.2/ckeditor.js" type="text/javascript"></script>

	@env('local')
	<script type="text/javascript">
		CKEDITOR.timestamp = '{{ uniqid() }}';
	</script>
	@endenv

	<script type="text/javascript">

		let elements = document.getElementsByClassName("ckeditor_book");

		CKEDITOR.plugins.addExternal('note', '/ckeditor/plugins/note/plugin.js');
		CKEDITOR.plugins.addExternal('sectionbreak', '/ckeditor/plugins/sectionbreak/plugin.js');
		CKEDITOR.plugins.addExternal('wordcount', '/ckeditor/plugins/wordcount/plugin.js');

		for (let i = 0; i < elements.length; i++) {

			let element = elements[i];

			@auth
			CKEDITOR.addCss('html {font-size:{{ auth()->user()->setting->font_size_px }}px;}');
			@if (!empty(auth()->user()->setting->font_family))
			CKEDITOR.addCss('body { font-family: \'{{ auth()->user()->setting->font_family }}\'; }');
			@endif
			@endauth

			CKEDITOR.addCss('html { padding: 20px; }');
			CKEDITOR.addCss('body { background-color:#FFF; }');

			var editor = CKEDITOR.replace(element, {
				bodyClass: 'book_text',
				language: "{{ strtolower(config('app.locale')) }}",
				height: "{{ empty($height) ? '500' : $height }}px",

				uploadUrl: '/books/{{ $book->id }}/attachments/storeFromCkeditor?responseType=json',
				filebrowserBrowseUrl: '/books/{{ $book->id }}/attachments',
				filebrowserImageBrowseUrl: '/books/{{ $book->id }}/attachments?type=Images',
				filebrowserImageUploadUrl: '/books/{{ $book->id }}/attachments/storeFromCkeditor?responseType=json',

				customValues: {
					sectionUrl: "/books/{{ $book->id }}/notes/loadList"
				},

				skin: 'moono-lisa',

				// Upload images to a CKFinder connector (note that the response type is set to JSON).
				//uploadUrl: '/images/?responseType=json',

				// /images/create?CKEditor=ckeditor&CKEditorFuncNum=1&langCode=ru
				// Configure your file manager integration. This example uses CKFinder 3 for PHP.
				//filebrowserImageUploadUrl: '/images/',
				filebrowserWindowWidth: '1000',
				filebrowserWindowHeight: '600',
				extraPlugins: "blockquote,sectionbreak,note,magicline,sourcearea,filebrowser,showborders," +
					"contextmenu,table,uploadimage,image2,autoembed,autolink,clipboard,notification,undo,uploadwidget," +
					"widget,filetools,notificationaggregator,toolbar,button,lineutils,widgetselection,wordcount",
				//extraPlugins: "uploadimage,image,autogrow,autoembed,autolink,youtube",

				// Configure the Enhanced Image plugin to use classes instead of styles and to disable the
				// resizer (because image size is controlled by widget styles or the image takes maximum
				// 100% of the editor width).
				image2_alignClasses: ['u-image-align-left', 'u-image-align-center', 'u-image-align-right'],
				image2_disableResizer: true,

				toolbarGroups: [
					{name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
					{
						name: 'paragraph',
						groups: ['list', 'blocks', 'align', 'bidi', 'paragraph']
					},
					{name: 'clipboard', groups: ['clipboard', 'undo']},
					{name: 'insert', groups: ['insert']},
					{name: 'links', groups: ['links']},
					{name: 'colors', groups: ['colors']},
					{name: 'tools', groups: ['tools']},
					{name: 'document', groups: ['mode', 'document', 'doctools']},
					//{name: 'spellchecker'},
					{name: 'editing', groups: ['find', 'selection', 'editing']},
					{name: 'forms', groups: ['forms']},
				],

				removePlugins: 'image,elementspath',
				scayt_autoStartup: false,
				disableNativeSpellChecker: true,
				removeButtons: 'Save,NewPage,Preview,Print,Templates,Scayt,Form,Checkbox,Radio,TextField,Select,Textarea,Button,ImageButton,HiddenField,CreateDiv,BidiLtr,BidiRtl,Language,Anchor,Flash,SpecialChar,PageBreak,Iframe,Format,Font,FontSize,ShowBlocks,About,PasteFromWord,SelectAll,Smiley',

				allowedContent: 'h1; h2; h3; h4; h5; h6; ' +
					'a[id,name,target,!href,class](u-title);' +
					'hr(u-section-break)[class]; ' +
					'p{text-align}[style,class](u-image-align-center); strong; em; s; u; sub; sup; b; i; br; blockquote; ' +
					'span{color,background-color}[lang,style];' +
					'div(u-section-break,u-title,u-empty-line,u-subtitle,u-annotation,u-date,u-epigraph,u-text-author,u-poem,u-stanza,u-cite,u-v,u-image-align-center,u-date)[class,id];' +
					'img(*){text-align,width,height}[!src,data-*,alt,width,height,style]; ' +
					'table{border-spacing,border-collapse}[border,cellpadding,cellspacing]; caption; tbody; thead; tr; th{padding}[style,abbr]; td{padding}[style,abbr]; tfoot; col[width,valign,align,span]; colgroup[width,valign,align,span];' +
					'dl; dt; dd;' +
					'ol; li; ul; ' +
					'pre; code;',

				contentsCss: [
					'{{ mix('css/app.css', config('litlife.assets_path')) }}'
				],

				stylesheetParser_validSelectors: /\w+\.\w+/,
				stylesheetParser_skipSelectors: /(^body\.|^caption\.|\.high|^\.)/i,
				wordcount: {
					showParagraphs: true,
					showWordCount: true,
					showCharCount: true,
					countSpacesAsChars: false,
					countHTML: false,
					countLineBreaks: false,
					maxWordCount: -1,
					maxCharCount: '{{ config('litlife.max_section_characters_count') }}',
					maxParagraphs: -1,
					pasteWarningDuration: 0
				}
			});
		}

	</script>
@endpush

