@extends('layouts.app')

@section('content')

	<div class="alert alert-warning" role="alert">
		<p class="mb-3">{{ __('common.away_warning', ['host' => $host]) }} <u>{{ Str::limit($url, 100) }}</u></p>
		<button id="go" class="btn btn-primary">
			{{ __('common.away_warning_button_text') }}
		</button>
	</div>

	@push('body_append')
		<script type="text/javascript">
			$(function () {
				$("#go").unbind('click').on('click', function () {
					document.location.href = decodeHtml('{{ $url }}');
				});

				function decodeHtml(html) {
					var txt = document.createElement("textarea");
					txt.innerHTML = html;
					return txt.value;
				}
			});
		</script>
	@endpush

@endsection
