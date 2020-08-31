@extends('layouts.app')

@section('content')


	<div class="row">
		<div class="col-md-8 order-md-0 order-1">
			<div class="card md-3">
				<div class="card-body">

					@if ($errors->any())
						<div class="alert alert-danger">
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					@if (session('success'))
						<div class="alert alert-success alert-dismissable">
							{{ session('success') }}
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						</div>
					@endif
					<table class="table table-striped">

						@foreach ($user->emails as $email)

							<tr class="email @if ($email->rescue) info @endif" data-id="{{ $email->id }}">

								<td>
									<span @if (!$email->confirm) class="text-muted" @endif>{{ $email->email }}</span> <br/>
									@if (!$email->confirm)
										<small>{{ __('user_email.not_confirm') }}

											<a href="{{ route('email.send_confirm_token', compact('email')) }}"
											   class="btn btn-sm btn-light">{{ __('user_email.confirm') }}</a>
										</small>
									@else
										<h4>
											@if ($email->show_in_profile)
												<i class="fas fa-eye" data-toggle="tooltip" data-placement="top"
												   title="{{ __('user_email.show_in_profile_tooltip') }}"></i>
											@else
												<i class="fas fa-eye-slash" data-toggle="tooltip" data-placement="top"
												   title="{{ __('user_email.not_show_in_profile_tooltip') }}"></i>
											@endif

											@if ($email->notice)
												<i class="far fa-bell" data-toggle="tooltip" data-placement="top"
												   title="{{ __('user_email.notice_tooltip') }}"></i>
											@endif

											@if ($email->rescue)
												<i class="fas fa-redo-alt" data-toggle="tooltip" data-placement="top"
												   title="{{ __('user_email.rescue_tooltip') }}"></i>
											@endif
										</h4>

									@endif
								</td>

								<td style="width:1%">

									<div class="btn-group" data-toggle="tooltip" data-placement="top"
										 title="{{ __('common.open_actions') }}">
										<button class="btn btn-light dropdown-toggle" type="button"
												id="emailDropdownMenu{{ $email->id }}"
												data-toggle="dropdown"
												aria-haspopup="true"
												aria-expanded="false">
											<i class="fas fa-ellipsis-h"></i>
										</button>

										<div class="dropdown-menu dropdown-menu-right"
											 aria-labelledby="emailDropdownMenu{{ $email->id }}">

											@if ($email->confirm)
												@if ($email->show_in_profile)
													<a class="dropdown-item"
													   href="{{ route('users.emails.hide', compact('user', 'email')) }}">
														<i class="fas fa-eye-slash"></i> &nbsp;
														{{ __('user_email.show_in_profile_disable') }}
													</a>
												@else
													<a class="dropdown-item"
													   href="{{ route('users.emails.show', compact('user', 'email')) }}">
														<i class="fas fa-eye"></i> &nbsp;
														{{ __('user_email.show_in_profile_enable') }}
													</a>
												@endif
												@if ($email->rescue)
													<a class="dropdown-item"
													   href="{{ route('users.emails.unrescue', compact('user', 'email')) }}">
														<i class="far fa-times-circle"></i> &nbsp;
														{{ __('user_email.rescue_disable') }}
													</a>
												@else
													<a class="dropdown-item"
													   href="{{ route('users.emails.rescue', compact('user', 'email')) }}">
														<i class="fas fa-redo-alt"></i> &nbsp;
														{{ __('user_email.rescue_enable') }}
													</a>
												@endif

												@if ($email->notice)

												@else
													@can ('notice_enable', $email)
														<a class="dropdown-item"
														   href="{{ route('users.emails.notifications.enable', compact('user', 'email')) }}">
															<i class="fas fa-at"></i> &nbsp;
															{{ __('user_email.notice_enable') }}
														</a>
													@endcan
												@endif

											@endif
											<a class="dropdown-item"
											   href="{{ route('users.emails.delete', compact('user', 'email')) }}">
												<i class="far fa-trash-alt"></i> &nbsp;
												{{ __('common.delete') }}
											</a>
										</div>

									</div>

								</td>

							</tr>

						@endforeach

					</table>

						<a href="{{ route('users.emails.create', ['user' => $user]) }}" class="btn btn-primary">
						{{ __('user_email.add') }}
					</a>
				</div>
			</div>
		</div>
		<div class="col-md-4  order-md-1 order-0">

			@include ('user.setting.navbar')

		</div>
	</div>


@endsection