<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" prefix="og: http://ogp.me/ns# book: http://ogp.me/ns/book# profile: http://ogp.me/ns/profile#">
<head>@include('head')</head>
<body>
@include('header')

<div class="container-fluid" style="max-width:1200px;">

	<div class="row mt-0">
		<div class="col-12 px-0">
			{!!  Breadcrumbs::render() !!}
		</div>
	</div>
	<div class="row mt-0">

		<aside id="sidebar"
			   class="@if (empty($errors->login)) d-none @endif @if (!empty($showSidebar)) d-sm-block @endif
					   sps sps--abv sidebar pl-0 pr-0 h-100">

			@include('sidebar')

		</aside>

		<main id="main"
			  class="col-12 py-3 @if (!empty($showSidebar)) pl-260px @endif @stack('main_classes')"
			  style="min-height:300px;">

			@auth
				@if (!auth()->user()->isHaveConfirmedMailbox() and Route::currentRouteName() != 'users.emails.index')
					<div class="row">
						<div class="col-12">
							<div class="alert alert-warning">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
								{{ __('common.not_found_any_confirmed_email') }}
								{{ __('common.please_confirm_email') }}
								<a href="{{ route('users.emails.index', auth()->user()) }}" class="btn btn-primary mt-3">
									{{ __('common.go_to_my_mailboxes') }}
								</a>
							</div>
						</div>
					</div>
				@endif

				@include('greeting')

			@endauth

			@yield('content')
		</main>

		@include ('footer')
	</div>
</div>
@include('body_append')
</body>
</html>
