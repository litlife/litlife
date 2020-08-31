<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Settings
	|--------------------------------------------------------------------------
	|
	| The configuration settings array is passed directly to HTMLPurifier.
	|
	| Feel free to add / remove / customize these attributes as you wish.
	|
	| Documentation: http://htmlpurifier.org/live/configdoc/plain.html
	|
	*/

	'settings' => [

		/*
		|--------------------------------------------------------------------------
		| Core.Encoding
		|--------------------------------------------------------------------------
		|
		| The encoding to convert input to.
		|
		| http://htmlpurifier.org/live/configdoc/plain.html#Core.Encoding
		|
		*/

		'Core.Encoding' => 'utf-8',

		/*
		|--------------------------------------------------------------------------
		| Core.SerializerPath
		|--------------------------------------------------------------------------
		|
		| The HTML purifier serializer cache path.
		|
		| http://htmlpurifier.org/live/configdoc/plain.html#Cache.SerializerPath
		|
		*/

		'Cache.SerializerPath' => storage_path('purify'),

		/*
		|--------------------------------------------------------------------------
		| HTML.Doctype
		|--------------------------------------------------------------------------
		|
		| Doctype to use during filtering.
		|
		| http://htmlpurifier.org/live/configdoc/plain.html#HTML.Doctype
		|
		*/

		'HTML.Doctype' => 'XHTML 1.0 Transitional',

		/*
		|--------------------------------------------------------------------------
		| HTML.Allowed
		|--------------------------------------------------------------------------
		|
		| The allowed HTML Elements with their allowed attributes.
		|
		| http://htmlpurifier.org/live/configdoc/plain.html#HTML.Allowed
		|
		*/

		'HTML.Allowed' => 'p[style],strong,em,s,u,sub,sup,ol,li,ul,blockquote,img[width|height|src|alt|style],' .
			'table[border|cellpadding|cellspacing|style],caption,tbody,thead,tr,th[style],td[style],' .
			'hr,iframe[frameborder|height|width|src],a[href],span[style],' .
			'b,i,u,br,div[class],ul,li',

		'Attr.AllowedClasses' => 'spoiler,spoiler-title,spoiler-toggle,hide-icon,spoiler-content',

		/*
		|--------------------------------------------------------------------------
		| HTML.ForbiddenElements
		|--------------------------------------------------------------------------
		|
		| The forbidden HTML elements. Elements that are listed in
		| this string will be removed, however their content will remain.
		|
		| For example if 'p' is inside the string, the string: '<p>Test</p>',
		|
		| Will be cleaned to: 'Test'
		|
		| http://htmlpurifier.org/live/configdoc/plain.html#HTML.ForbiddenElements
		|
		*/

		'HTML.ForbiddenElements' => '',

		/*
		|--------------------------------------------------------------------------
		| CSS.AllowedProperties
		|--------------------------------------------------------------------------
		|
		| The Allowed CSS properties.
		|
		| http://htmlpurifier.org/live/configdoc/plain.html#CSS.AllowedProperties
		|
		*/

		'CSS.Trusted' => 'true',

		'CSS.AllowedProperties' => 'text-align,height,width,color,background-color,border-spacing,border-collapse',

		/*
		|--------------------------------------------------------------------------
		| AutoFormat.AutoParagraph
		|--------------------------------------------------------------------------
		|
		| The Allowed CSS properties.
		|
		| This directive turns on auto-paragraphing, where double
		| newlines are converted in to paragraphs whenever possible.
		|
		| http://htmlpurifier.org/live/configdoc/plain.html#AutoFormat.AutoParagraph
		|
		*/

		'AutoFormat.AutoParagraph' => true,

		/*
		|--------------------------------------------------------------------------
		| AutoFormat.RemoveEmpty
		|--------------------------------------------------------------------------
		|
		| When enabled, HTML Purifier will attempt to remove empty
		| elements that contribute no semantic information to the document.
		|
		| http://htmlpurifier.org/live/configdoc/plain.html#AutoFormat.RemoveEmpty
		|
		*/

		'AutoFormat.RemoveEmpty' => false,

		'CSS.MaxImgLength' => '700px',

		'HTML.FlashAllowFullScreen' => true,

		'HTML.SafeIframe' => true,

		'AutoFormat.Linkify' => true,
		'URI.Host' => parse_url(env('APP_URL', 'https://litlife.club'), PHP_URL_HOST),
		'URI.Munge' => '/away?url=%s',


		'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'

	],

];
