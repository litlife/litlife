@component('user.list.default', ['user' => $user, 'collectionUser' => $collectionUser ?? null, 'description' => $collectionUser->description])

	@isset ($collectionUser)
		<div class="mt-1 mb-1">
			@foreach ($collectionUser->getPermissions() as $name => $value)
				@if ($value == true)
					<small>{{ __('collection_user.'.$name) }}</small> <br/>
				@endif
			@endforeach
		</div>
	@endisset

	@slot('dropdown')
		<div class="ml-auprogressto">
			<div class="btn-group" data-toggle="tooltip" data-placement="top"
				 title="{{ __('common.open_actions') }}">
				<button class="btn btn-light dropdown-toggle" type="button"
						id="dropdownMenuBook_{{ $user->id }}"
						data-toggle="dropdown"
						aria-haspopup="true"
						aria-expanded="false">
					<i class="fas fa-ellipsis-v"></i>
				</button>
				<div class="dropdown-menu dropdown-menu-right"
					 aria-labelledby="dropdownMenuBook_{{ $user->id }}">
					@can('editUser', $collection)
						<a class="dropdown-item detach text-lowercase"
						   href="{{ route('collections.users.edit', ['collection' => $collection, 'user' => $user]) }}">
							{{ __('common.edit') }}
						</a>
					@endcan

					@can('deleteUser', $collection)
						<a class="dropdown-item text-lowercase"
						   href="{{ route('collections.users.delete', ['collection' => $collection, 'user' => $user]) }}">
							{{ __('common.delete') }}
						</a>
					@endcan
				</div>
			</div>
		</div>
	@endslot

@endcomponent