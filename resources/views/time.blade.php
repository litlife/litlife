@if (!empty($time))
	@php($time = $time->timezone(session()->get('geoip')->timezone))
	<span data-toggle="tooltip" data-placement="top" style="cursor:pointer"
		  title="{{ $time->diffForHumans() }}. {{ $time->formatLocalized('%A') }}">
        @if (empty($hide_hour_minute))
			{{ $time->formatLocalized('%e %B %Y %H:%M') }}
		@else
			{{ $time->formatLocalized('%e %B %Y') }}
		@endif
            </span>
@endif