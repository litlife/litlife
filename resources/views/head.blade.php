<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
{!! SEOMeta::generate() !!}
{!! OpenGraph::generate() !!}
{!! Twitter::generate() !!}

@if (!auth()->check())
	<meta name="caffeinated" content="false">
@endif
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ \DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs::pageTitle() }}</title>

<link rel="icon" type="image/x-icon" href="/favicon.ico"/>

<link href="{{ mix('css/app.css', config('litlife.assets_path')) }}" rel="stylesheet">

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" crossorigin="anonymous">

@stack('css')

@auth
	<style>
		html {
			font-size: {{ auth()->user()->setting->font_size_px }}px;
		}

		@if (!empty(auth()->user()->setting->font_family))
        body {
			font-family: '{{ auth()->user()->setting->font_family }}' !important;
		}
		@endif
	</style>
@endauth

@guest
	<style>
		html {
			font-size: {{ (new \App\User)->setting->font_size_px }}px;
		}
	</style>
@endauth

{!! shared()->render() !!}