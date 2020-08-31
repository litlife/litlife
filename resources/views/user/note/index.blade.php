@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/users.notes.index.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')


	<a class="btn btn-primary mb-3" href="{{ route('users.notes.create', $user) }}">{{ __('common.create') }}</a>


	@if (isset($notes) and $notes->count())

		@foreach ($notes as $item)
			<div class="user-notes">

				<div class="item card mb-3" data-id="{{ $item->id }}">
					<div class="html_box card-body ">
						{!! $item->text !!}
					</div>

					<div class="card-footer d-flex">
						<div class="w-100">
							@if ($item->created_at == $item->updated_at)
								<small
										class="text-muted">{{ trans_choice('user_note.created', 1) }}
									<x-time :time="$item->created_at"/>
								</small>
							@else
								<small
										class="text-muted">{{ trans_choice('user_note.updated', 1) }}
									<x-time :time="$item->updated_at"/>
								</small>
							@endif
						</div>
						<div class="ml-2 flex-shrink-1">
							<div class="btn-group" data-toggle="tooltip" data-placement="top"
								 title="{{ __('common.open_actions') }}">
								<button class="btn btn-light btn-sm dropdown-toggle" type="button" id="dropdownMenuButton"
										data-toggle="dropdown"
										aria-haspopup="true"
										aria-expanded="false">
									<i class="fas fa-ellipsis-h"></i>
								</button>
								<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
									<a class="delete pointer dropdown-item text-lowercase" href="javascript:void(0)"
									   disabled="disabled"
									   data-loading-text="{{ __('common.deleting') }}"
									   @cannot ('delete', $item) style="display:none;"@endcannot>
										{{ __('common.delete') }}
									</a>

									<a class="restore pointer dropdown-item text-lowercase" href="javascript:void(0)"
									   disabled="disabled"
									   data-loading-text="{{ __('common.restoring') }}"
									   @cannot ('restore', $item) style="display:none;"@endcannot>
										{{ __('common.restore') }}
									</a>

									@can('update', $item)
										<a class="btn-edit dropdown-item text-lowercase pointer"
										   href="{{ route('notes.edit', ['note' => $item]) }}">
											{{ mb_strtolower(__('common.edit')) }}
										</a>
									@endcan
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		@endforeach

		@if (isset($notes) and $notes->hasPages())
			{{ $notes->appends(request()->except(['page', 'ajax']))->links() }}
		@endif

	@else
		<p class="alert alert-info">{{ __('user_note.nothing_found') }}</p>
	@endif

@endsection
