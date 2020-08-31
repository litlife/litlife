<div class="row">
	<div class="col-12">
		<div @if ($section->getSectionId()) id="{{ $section->getSectionId() }}" @endif class="text cke_editable">
			{!! $section->contentHandled !!}
		</div>
	</div>
</div>

@if (auth()->check())

	<style type="text/css">

		.text {
			font-family: {{ auth()->user()->readStyle->font ?? config('litlife.read_default_font') }};
			font-size: {{ auth()->user()->readStyle->size ?? config('litlife.read_default_size') }}px;
			text-align: {{ auth()->user()->readStyle->read_default_align ?? config('litlife.read_default_align') }};
			background-color: {{ auth()->user()->readStyle->background_color ?? config('litlife.read_default_background_color') }};
			color: {{ auth()->user()->readStyle->font_color ?? config('litlife.read_default_font_color') }};
		}

		body {

			background-color: {{ auth()->user()->readStyle->background_color ?? config('litlife.read_default_background_color') }};
		}

	</style>

@endif