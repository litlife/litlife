<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>@include('head')</head>
<body>
@include('header', ['menu_disable' => true])

<div class="container-fluid" style="max-width:1200px;">

	<div class="row mt-0">
		<div class="col-12 px-0">
			{!!  Breadcrumbs::render() !!}
		</div>
	</div>

	<div class="row mt-0 ">

		<main id="main" class="col-12 py-3 @stack('main_classes')" style="min-height:300px;">
			@yield('content')
		</main>

		@include ('footer')
	</div>
</div>
@include('body_append')
</body>
</html>
