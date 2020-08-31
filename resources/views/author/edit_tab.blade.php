<div class="row mb-3">
	<div class="col-12">
		<ul class="nav nav-pills">
			<li class="nav-item">
				<a class="nav-link {{ isActiveRoute('authors.edit') }}"
				   href="{{ url('/authors/'.$author->id.'/edit') }}">
					{{ __('author.descrition') }}
				</a>
			</li>

			@can ('viewManagers', $author)
				<li class="nav-item">
					<a class="nav-link {{ isActiveRoute('authors.managers') }}"
					   href="{{ url('/authors/'.$author->id.'/managers') }}">
						{{ __('author.authors_and_editors') }}
					</a>
				</li>
			@endcan

		</ul>
	</div>
</div>