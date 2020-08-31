@if (session('show_greeting'))

	@php
		$hour = now()->timezone(session()->get('geoip')->timezone)->hour;
	@endphp

	<div class="row">
		<div class="col-12">

			<div class="alert alert-success" role="alert">
				<strong>
					@if (in_array($hour, range(6, 10)))
						{{ __('greeting.good_morning', ['name' => auth()->user()->userName]) }}
					@elseif (in_array($hour, range(11, 17)))
						{{ __('greeting.good_day', ['name' => auth()->user()->userName]) }}
					@elseif (in_array($hour, range(18, 22)))
						{{ __('greeting.good_evening', ['name' => auth()->user()->userName]) }}
					@elseif (in_array($hour, [23, 0]) or in_array($hour, range(0, 5)))
						{{ __('greeting.good_night', ['name' => auth()->user()->userName]) }}
					@else
						{{ __('greeting.hello_array.'.rand(0, count(__('greeting.hello_array'))-1)) }}
					@endif
				</strong>
				{!! __('greeting.great_to_see_you.'.rand(0, count(__('greeting.great_to_see_you'))-1), ['name' => auth()->user()->userName]) !!}

				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		</div>
	</div>

	@can('takeSurvey', \App\User::class)
		<div class="alert alert-info d-flex flex-row" role="alert">
			<div class="w-100">
				{{ __('survey.we_suggest_you_take_a_mini_survey') }}

				<a class="alert-link" href="{{ route('surveys.create') }}">
					{{ __('survey.go_to_filling_in') }}
				</a>
			</div>
			<div class="flex-shrink-1 ml-3">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		</div>
	@endcan

@endif

@php
	session(['show_greeting' => false]);
@endphp
