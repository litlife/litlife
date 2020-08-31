@extends('layouts.app')

@push('scripts')

@endpush

@section ('content')

	@include('collection.show_navbar')

	@can('createUser', $collection)
		<div class="mb-3">
			<a href="{{ route('collections.users.create', $collection) }}"
			   class="btn btn-primary">{{ __('collection.add_user') }}</a>
		</div>
	@endcan

	@include('user.list.default', ['user' => $collection->create_user, 'description' => 'Создатель'])

	@foreach ($collectionUsers as $collectionUser)
		@include('collection.user.item', ['user' => $collectionUser->user, 'collectionUser' => $collectionUser])
	@endforeach

@endsection

