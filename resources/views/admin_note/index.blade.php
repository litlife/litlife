@extends('layouts.app')

@section('content')

	<div class="card">
		<div class="card-body">
			@if (!empty($notes) and $notes->count() > 0)

				<ul class="list-group list-group-flush mb-2">
					@foreach ($notes as $note)
						<li class="list-group-item" style="background-color: transparent">

							<x-time :time="$note->created_at ?? null "/>
							<x-user-name :user="$note->create_user"/>
							:

							{!! $note->text !!}

							<a href="{{ route('admin_notes.edit', $note->id) }}">{{ __('common.edit') }}</a>
							<a href="{{ route('admin_notes.delete', $note->id) }}">{{ __('common.delete') }}</a>
						</li>
					@endforeach
				</ul>

			@else

				<div class="alert alert-info">
					{{ __('admin_note.nothing_found') }}
				</div>

			@endif

		</div>
	</div>
	@if ($notes->hasPages())
		{{ $notes->appends(request()->except(['page', 'ajax']))->links() }}

	@endif


@endsection
