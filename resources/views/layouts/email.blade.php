<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title>{{ config('app.name', 'Laravel') }}</title>
</head>
<body>
<div class="container">

	<div class="row">
		<div class="col-12">
			@if (empty($recepient))
				{{ __('common.hello') }}!
			@else
				{{ __('common.hello') }}, {{ $recepient->userName }}!
			@endif
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			@yield('content')
		</div>
	</div>

	@if (!empty($to))

		<div class="row">
			<div class="col-12">
				<a href="{{ \Illuminate\Support\Facades\URL::signedRoute('email.notice_disable', ['email' => $to]) }}">{{ __('common.unsubscribe') }}</a>
				{{ __('email.unsubscribe') }}
			</div>
		</div>

	@endif

	<div class="row">
		<div class="col-12">
			{{ __('email.sincerely_yours') }}, <a href="{{ config('app.url') }}">{{ __('app.title') }}</a>
		</div>
	</div>

</div>
</body>
</html>