@extends('layouts.app')

@push('scripts')


@endpush

@section('content')

	@if(!empty($participations) and count($participations) > 0)


		<div class="list-group">

			@foreach ($participations as $participation)

				@if (!empty($participation->interlocutor))
					<a href="{{ route('users.messages.index', ['user' => $participation->interlocutor]) }}"
					   class="list-group-item list-group-item-action d-flex flex-row align-items-start "
					   data-id="{{ $participation->interlocutor->id }}"
					   data-conversation-id="{{ $participation->conversation->id }}">

						<div class="ml-2 mr-3 text-center" style="width:50px">
							<x-user-avatar :user="$participation->interlocutor ?? null" href="0" width="50" height="50"/>
						</div>

						<div class="w-100">
							<div class="d-flex justify-content-between mb-2">
								<h6 class="mb-1">
									<x-user-name :user="$participation->interlocutor" href="0"/>
								</h6>

								@if (!empty($participation->latest_message))
									<small>
										<x-time :time="$participation->latest_message->created_at"/>
									</small>
								@endif
							</div>

							<p class="mb-2">
								@if (!empty($participation->latest_message) and !empty($text = $participation->latest_message->getPreviewText(100)))
									@if ($participation->latest_message->create_user->id == $user->id)
										{{ __('common.you')  }}: {{  $text  }}
									@else
										@if (!empty($participation->new_messages_count))
											<strong>
												{{ $text }}
												<span class="badge badge-primary badge-pill">{{ $participation->new_messages_count }}</span>
											</strong>
										@else
											{{ $text }}
										@endif
									@endif
								@endif
							</p>
						</div>
					</a>

				@endif
			@endforeach

		</div>

		@if ($participations->hasPages())
			<div class="row mt-3">
				<div class="col-12">
					{{ $participations->appends(request()->except(['page', 'ajax']))->links() }}
				</div>
			</div>
		@endif

	@else
		<div class="row">
			<div class="col-12">
				<div class="alert alert-info">{{ __('message.nothing_found') }}</div>
			</div>
		</div>
	@endif





@endsection