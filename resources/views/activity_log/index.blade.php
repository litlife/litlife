@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	<div class="card">
		<div class="card-body p-0">

			<ul class="list-group list-group-flush">
				@foreach ($activityLogs as $activityLog)
					<li class="list-group-item ">
						<div class="d-flex w-100 justify-content-between">
							<div>
								<h6>
									@switch ($activityLog->causer_type)

										@case ('user')
										@if (!empty($activityLog->causer))
											<x-user-name :user="$activityLog->causer"/>
										@endif
										@break

										@default
										@if (empty($activityLog->causer_type) and empty($activityLog->causer_id))
											{{ __('common.unknown') }}
										@else
											{{ $activityLog->causer_type }} {{ $activityLog->causer_id }}
										@endif
										@break

									@endswitch

									{{ __('activity_log.description_subject_type.'.$activityLog->subject_type.'.'.$activityLog->description) }}

									@switch ($activityLog->subject_type)

										@case ('book')
										<x-book-name :book="$activityLog->subject"/>
										@break

										@case ('author')
										<x-author-name :author="$activityLog->subject"/>
										@break

										@case ('author_biography')
										<a href="{{ route('authors.show', $activityLog->subject) }}">{{ $activityLog->subject->name }}</a>
										@break

										@case ('author_photo')
										<x-author-name :author="$activityLog->subject->author"/>
										@break

										@case ('book_file')

										@if (!empty($activityLog->subject->book))
											<a href="{{ route('books.show', $activityLog->subject->book) }}">
												{{ $activityLog->subject->book->title }}
											</a>
										@endif

										{{ $activityLog->subject->format }}
										{{ $activityLog->subject->name }}

										@break

										@case ('group')
										<a href="{{ route('groups.show', $activityLog->subject) }}">{{ $activityLog->subject->name }}</a>
										@break

										@case ('sequence')
										@include('sequence.name', ['sequence' => $activityLog->subject])
										@break

									@endswitch
								</h6>

								<div>
									@if ($activityLog->subject_type == 'book')
										@if ($activityLog->description == 'add_to_private')
											@if (!empty($activityLog->getExtraProperty('reason')))
												{{ __('activity_log.reason') }}: {{ $activityLog->getExtraProperty('reason') }}
											@endif
										@endif

										@if ($activityLog->description == 'deleted')
											@if (!empty($activityLog->getExtraProperty('reason')))
												{{ __('activity_log.reason') }}: {{ $activityLog->getExtraProperty('reason') }}
											@endif
										@endif
									@endif

									@if ($changes = $activityLog->getChanges())
										@foreach ($changes as $column => $array)
											{{ __($activityLog->subject_type.'.'.$column) }}: {{ $array['old'] }}
											=> {{ $array['new'] }} <br/>
										@endforeach
									@endif
								</div>

							</div>
							<small>
								<x-time :time="$activityLog->created_at"/>
							</small>
						</div>
					</li>
				@endforeach
			</ul>
		</div>
	</div>
	@if ($activityLogs->hasPages())
		<div class="row mt-3">
			<div class="col-12">
				{{ $activityLogs->appends(request()->except(['page', 'ajax']))->links() }}
			</div>
		</div>
	@endif

@endsection