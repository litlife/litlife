@extends('layouts.app')

@section('content')


	@if (empty($auth_logs))

		<div class="alert alert-info">{{ __('auth_logs.nothing_found') }}</div>

	@else

		<div class="card mb-3">
			<div class="card-body">

				<div class="table-responsive">
					<table class="table table-striped">
						<tr>
							<th>#</th>
							<th>{{ trans_choice('user.users', 1) }}</th>
							<th>{{ __('auth_logs.ip') }}</th>
							<th>{{ __('auth_logs.created_at') }}</th>
							<th>{{ __('auth_logs.device') }}</th>
						</tr>
						@foreach ($auth_logs as $log)
							<tr>
								<td>{{ $log->id }}</td>
								<th>
									<x-user-name :user="$log->user"/>
								</th>
								<td><a target="_blank"
									   href="http://www.seogadget.ru/location?addr={{ $log->ip }}">{{ $log->ip }}</a>
									{{ geoip($log->ip)->country }} / {{ geoip($log->ip)->city }}
								</td>
								<td>
									<x-time :time="$log->updated_at"/>
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

		@if ($auth_logs->hasPages())
			{{ $auth_logs->appends(request()->except(['page', 'ajax']))->links() }}
		@endif
	@endif

@endsection