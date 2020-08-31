<div class="col-lg-6 col-md-12 col-sm-12 d-flex flex-row shadow-sm rounded py-2 px-3" data-id="{{ $user->id }}">

	<div class="mr-3" style="width:65px">
		<x-user-avatar :user="$user" width="50" height="50" style="max-width: 100%;"/>
	</div>

	<div class="w-100">
		<div class="mb-2">
			<x-user-name :user="$user"/>
		</div>
		<div class="">
			@if (!empty($user->new_messages))
				<a class="btn btn-primary" href="{{ route('users.messages.index', ['user' => $user]) }}">
					{{ __('message.new_messages') }}: {{ $user->new_messages }}
				</a>
			@else
				<a class="btn btn-light" href="{{ route('users.messages.index', ['user' => $user]) }}">
					{{ __('common.dialog') }}
				</a>
			@endif
		</div>
	</div>
</div>


