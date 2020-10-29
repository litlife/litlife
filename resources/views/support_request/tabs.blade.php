<div class="mb-3">
	<ul class="nav nav-pills">
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('support_requests.index') }}"
			   href="{{ route('support_requests.index') }}">
				{{ __('All') }}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('support_requests.unsolved') }}"
			   href="{{ route('support_requests.unsolved') }}">
				{{ __('Unresolved') }}

				@if ($count = \App\SupportRequest::getNumberOfUnsolved())
					<span class="badge badge-primary">{{ $count }}</span>
				@endif
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('support_requests.in_process_of_solving') }}"
			   href="{{ route('support_requests.in_process_of_solving') }}">
				{{ __('In process') }}

				@if ($count = \App\SupportRequest::getNumberInProcess())
					<span class="badge badge-light">{{ $count }}</span>
				@endif
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('support_requests.solved') }}"
			   href="{{ route('support_requests.solved') }}">
				{{ __('Solved requests') }}

				@if ($count = \App\SupportRequest::getNumberOfSolved())
					<span class="badge badge-light">{{ $count }}</span>
				@endif
			</a>
		</li>
	</ul>
</div>