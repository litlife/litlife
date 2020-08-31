@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	<div class="card">
		<div class="card-body">
			Часто используемые шрифты:

			<ul class="list-group list-group-flush mb-3">
				@foreach ($fonts as $item)
					<li class="list-group-item">
						{{ config('litlife.read_allowed_fonts.'.$item->font) }} -
						{{ $item->count }}
					</li>
				@endforeach
			</ul>

			Часто используемые размеры шрифтов:

			<ul class="list-group list-group-flush mb-3">
				@foreach ($sizes as $item)
					<li class="list-group-item">
						{{ $item->size }} -
						{{ $item->count }}
					</li>
				@endforeach
			</ul>

			Часто используемые цвет шрифта:

			<ul class="list-group list-group-flush mb-3">
				@foreach ($fontColors as $item)
					<li class="list-group-item">
						{{ $item->font_color }} -
						{{ $item->count }}
					</li>
				@endforeach
			</ul>

			Часто используемые цвет фона карты:

			<ul class="list-group list-group-flush mb-3">
				@foreach ($cardColors as $item)
					<li class="list-group-item">
						{{ $item->card_color }} -
						{{ $item->count }}
					</li>
				@endforeach
			</ul>

			Часто используемые цвет фона:

			<ul class="list-group list-group-flush mb-3">
				@foreach ($backgroundColor as $item)
					<li class="list-group-item">
						{{ $item->background_color }} -
						{{ $item->count }}
					</li>
				@endforeach
			</ul>

			Выравнивание шрифта:

			<ul class="list-group list-group-flush mb-3">
				@foreach ($aligns as $item)
					<li class="list-group-item">
						{{ config('litlife.read_text_align.'.$item->align) }} -
						{{ $item->count }}
					</li>
				@endforeach
			</ul>

		</div>
	</div>

@endsection
