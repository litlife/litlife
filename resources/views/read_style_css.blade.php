@push('css')
	@php
		use App\UserReadStyle;if (auth()->check()) $readStyle = auth()->user()->readStyle; else $readStyle = (new UserReadStyle());
	@endphp

	<style type="text/css">
		.book_text {
			@if ($readStyle->font != 'Default')    font-family: {{ $readStyle->font }};
			@endif
   font-size: {{ $readStyle->size }}px;
			text-align: {{ $readStyle->align }};
			color: {{ $readStyle->font_color }};
		}

		.card {
			background-color: {{ $readStyle->card_color }};
		}

		body {
			background-color: {{ $readStyle->background_color }};
		}
	</style>
@endpush