@can ('view', App\AdminNote::class)
	<div class="row mb-3">
		<div class="col-12">
			@if (!empty($object->latest_admin_notes) and $object->latest_admin_notes->count() > 0)

				<div class="alert alert-warning p-0 mb-1" role="alert">
					<ul class="list-group list-group-flush">
						@foreach ($object->latest_admin_notes as $note)
							<li class="list-group-item" style="background-color: transparent">

								<x-time :time="$note->created_at ?? null "/>
								<x-user-name :user="$note->create_user"/>
								:

								{!! $note->text !!}
							</li>
						@endforeach
					</ul>
				</div>

			@endif

			<a class="btn btn-sm btn-light"
			   href="{{ route('admin_notes.create', ['type' => $object->admin_notes()->getMorphClass(), 'id' => $object->id]) }}">
				{{ __('admin_note.create') }}
			</a>

			@if ($object->admin_notes_count > 0)
				<a class="btn btn-sm btn-light"
				   href="{{ route('admin_notes.index', ['type' => $object->admin_notes()->getMorphClass(), 'id' => $object->id]) }}">
					{{ __('admin_note.show_all') }}

					@if ($object->admin_notes_count > 2)
						<span class="badge badge-light">{{ $object->admin_notes_count }}</span>
					@endif
				</a>
			@endif
		</div>
	</div>
@endcan