@extends('layouts.app')

@section('content')

	@include('user.auth_logs.navbar')

	@if (empty($auth_fails))

		<div class="alert alert-info">{{ __('auth_logs.nothing_found') }}</div>
	@else

		<div class="card md-3">
			<div class="card-body">

				<div class="table-responsive">
					<table class="table table-striped">
						<tr>
							<th>#</th>
							<th>{{ __('auth_logs.password') }}</th>
							<th>{{ __('auth_logs.ip') }}</th>
							<th>{{ __('auth_logs.created_at') }}</th>
							<th>{{ __('auth_logs.device') }}</th>
						</tr>
						@foreach ($auth_fails as $log)
							<tr>
								<td>{{ $log->id }}</td>
								<td>{{ $log->password }}</td>
								<td>
									<a target="_blank"
									   href="http://www.seogadget.ru/location?addr={{ $log->ip }}">{{ $log->ip }}</a>

									{{ geoip($log->ip)->country }} / {{ geoip($log->ip)->state_name }} /
									{{ geoip($log->ip)->city }}

								</td>
								<td>
									<x-time :time="$log->created_at"/>
								</td>
								<td>
									@isset (optional($log->user_agent)->value)

										@if ($log->user_agent->parsed->isMobile())
											{{ __('user_agent.mobile') }}
										@endif
										@if ($log->user_agent->parsed->isTablet())
											{{ __('user_agent.tablet') }}
										@endif
										@if ($log->user_agent->parsed->isDesktop())
											{{ __('user_agent.desktop') }}
										@endif
										@if ($log->user_agent->parsed->isBot())
											{{ __('user_agent.bot') }}
										@endif

										{{ $log->user_agent->parsed->browserName() }}
										{{ $log->user_agent->parsed->platformName() }}
										{{ $log->user_agent->parsed->deviceFamily() }} {{ $log->user_agent->parsed->deviceModel() }} {{ $log->user_agent->parsed->mobileGrade() }}
										<br/>
										{{ $log->user_agent->value }}

									@endisset
								</td>
							</tr>
						@endforeach
					</table>
				</div>
			</div>
		</div>

		@if ($auth_fails->hasPages())
			{{ $auth_fails->appends(request()->except(['page', 'ajax']))->links() }}

		@endif
	@endif

@endsection