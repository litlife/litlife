<div class="mb-3">
	<ul class="nav nav-pills">
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('support_questions.index') }}"
			   href="{{ route('support_questions.index') }}">
				{{ __('All') }}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('support_questions.unsolved') }}"
			   href="{{ route('support_questions.unsolved') }}">
				{{ __('New questions') }}

				@if ($count = \App\SupportQuestion::getNumberOfNewQuestions())
					<span class="badge badge-primary">{{ $count }}</span>
				@endif
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('support_questions.in_process_of_solving') }}"
			   href="{{ route('support_questions.in_process_of_solving') }}">
				{{ __('In process') }}

				@if ($count = \App\SupportQuestion::getNumberInProcess())
					<span class="badge badge-light">{{ $count }}</span>
				@endif
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('support_questions.solved') }}"
			   href="{{ route('support_questions.solved') }}">
				{{ __('Solved questions') }}

				@if ($count = \App\SupportQuestion::getNumberOfSolved())
					<span class="badge badge-light">{{ $count }}</span>
				@endif
			</a>
		</li>
	</ul>
</div>