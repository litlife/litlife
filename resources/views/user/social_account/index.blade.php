@extends('layouts.app')

@section('content')

	<div class="row">
		<div class="col-md-8 order-md-0 order-1">
			<div class="card mb-3">
				<div class="card-body">

					@if (session('success'))
						<div class="alert alert-success alert-dismissable">
							{{ session('success') }}
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						</div>
					@endif
					<div class="table-responsive">
						<table class="table table-striped">
							<tr>
								<td>
									{{ __('Google') }}
								</td>
								<td>
									@if (empty($account = $user->social_accounts->where('provider', 'google')->first()))
										<a class="btn btn-light"
										   href="{{ route('social_accounts.redirect', ['provider' => 'google']) }}">
											{{ __('user.social_accounts_array.bind') }}
										</a>
									@else
										{{ __('user.social_accounts_array.binded') }}

										<div class="btn-group">
											<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
													data-toggle="dropdown"
													aria-haspopup="true"
													aria-expanded="false">
												<i class="fas fa-ellipsis-h"></i>
											</button>
											<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
												<a class="dropdown-item"
												   href="{{ route('users.social_accounts.detach', ['user' => $user->id, 'id' => $account->id]) }}">
													{{ __('user.social_accounts_array.unbind') }}
												</a>
											</div>
										</div>

									@endif
								</td>
							</tr>

							<tr>
								<td>
									{{ __('Facebook') }}
								</td>
								<td>
									@if (empty($account = $user->social_accounts->where('provider', 'facebook')->first()))
										<a class="btn btn-light"
										   href="{{ route('social_accounts.redirect', ['provider' => 'facebook']) }}">
											{{ __('user.social_accounts_array.bind') }}
										</a>
									@else
										{{ __('user.social_accounts_array.binded') }}

										<div class="btn-group">
											<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
													data-toggle="dropdown"
													aria-haspopup="true"
													aria-expanded="false">
												<i class="fas fa-ellipsis-h"></i>
											</button>
											<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
												<a class="dropdown-item"
												   href="{{ route('users.social_accounts.detach', ['user' => $user->id, 'id' => $account->id]) }}">
													{{ __('user.social_accounts_array.unbind') }}
												</a>
											</div>
										</div>

									@endif
								</td>
							</tr>

							<tr>
								<td>
									{{ __('Vk') }}
								</td>
								<td>
									@if (empty($account = $user->social_accounts->where('provider', 'vkontakte')->first()))
										<a class="btn btn-light"
										   href="{{ route('social_accounts.redirect', ['provider' => 'vkontakte']) }}">
											{{ __('user.social_accounts_array.bind') }}
										</a>
									@else
										{{ __('user.social_accounts_array.binded') }}

										<div class="btn-group">
											<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
													data-toggle="dropdown"
													aria-haspopup="true"
													aria-expanded="false">
												<i class="fas fa-ellipsis-h"></i>
											</button>
											<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
												<a class="dropdown-item"
												   href="{{ route('users.social_accounts.detach', ['user' => $user->id, 'id' => $account->id]) }}">
													{{ __('user.social_accounts_array.unbind') }}
												</a>
											</div>
										</div>

									@endif
								</td>
							</tr>

							<tr>
								<td>
									{{ __('Yandex') }}
								</td>
								<td>
									@if (empty($account = $user->social_accounts->where('provider', 'yandex')->first()))
										<a class="btn btn-light"
										   href="{{ route('social_accounts.redirect', ['provider' => 'yandex']) }}">
											{{ __('user.social_accounts_array.bind') }}
										</a>
									@else
										{{ __('user.social_accounts_array.binded') }}

										<div class="btn-group">
											<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
													data-toggle="dropdown"
													aria-haspopup="true"
													aria-expanded="false">
												<i class="fas fa-ellipsis-h"></i>
											</button>
											<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
												<a class="dropdown-item"
												   href="{{ route('users.social_accounts.detach', ['user' => $user->id, 'id' => $account->id]) }}">
													{{ __('user.social_accounts_array.unbind') }}
												</a>
											</div>
										</div>

									@endif
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4  order-md-1 order-0">

			@include ('user.setting.navbar')

		</div>
	</div>

@endsection