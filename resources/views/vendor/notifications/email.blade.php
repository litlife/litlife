@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level == 'error')
# Whoops!
@else
# Hello!
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
switch ($level) {
case 'success':
$color = 'green';
break;
case 'error':
$color = 'red';
break;
default:
$color = 'blue';
}
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{!! $line  !!}
@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
{{ __('notification.sincerely_yours') }}, [{{ __('app.name') }}]({{ route('home') }})!
@endif

[{{ __('Answers to frequently asked questions') }}]({{ route('faq') }}) &nbsp;
[{{ __('Forum') }}]({{ route('forums.index') }}) &nbsp;
[{{ __('Guide') }}]({{ route('topics.show', ['topic' => 2837]) }})

{{-- Subcopy --}}
@isset($actionText)
@component('mail::subcopy')
{{ __('notification.subcopy', ['actionText' => $actionText, 'actionUrl' => $actionUrl]) }}
@endcomponent
@endisset
@endcomponent
