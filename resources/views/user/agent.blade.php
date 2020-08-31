@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	<div class="row">
		<div class="col-12 d-flex">

			@if (empty($user_agent))
				{{ __('user_agent.not_found') }}
			@else
				<dl>
					<dt>{{ __('user_agent.type') }}</dt>
					<dd>
						@if ($user_agent->parsed->isMobile())
							{{ __('user_agent.mobile') }}
						@endif
						@if ($user_agent->parsed->isTablet())
							{{ __('user_agent.tablet') }}
						@endif
						@if ($user_agent->parsed->isDesktop())
							{{ __('user_agent.desktop') }}
						@endif
						@if ($user_agent->parsed->isBot())
							{{ __('user_agent.bot') }}
						@endif
					</dd>
					<dt>{{ __('user_agent.browser_name') }}</dt>
					<dd>{{ $user_agent->parsed->browserName() }}</dd>
					<dt>{{ __('user_agent.platform') }}</dt>
					<dd>{{ $user_agent->parsed->platformName() }}</dd>
					<dt>{{ __('user_agent.device') }}</dt>
					<dd>{{ $user_agent->parsed->deviceFamily() }} {{ $user_agent->parsed->deviceModel() }} {{ $user_agent->parsed->mobileGrade() }}</dd>
					<dt>{{ __('user_agent.agent') }}</dt>
					<dd>{{ $user_agent->value }}</dd>
				</dl>
			@endif
		</div>
	</div>

@endsection