<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" prefix="og: http://ogp.me/ns# book: http://ogp.me/ns/book#">
<head>@include('head')</head>
<body style="padding-top: 0px; ">

<div class="container">
	@yield('content')
</div>

@include('body_append')
</body>
</html>
