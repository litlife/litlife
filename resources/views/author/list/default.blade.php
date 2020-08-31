@if(count($authors) > 0)

	<div class="row">
		<div class="col-12">
			@if ($authors->hasPages())
				{{ $authors->appends(request()->except(['page', 'ajax']))->links() }}
			@endif
		</div>
	</div>

	@php
		$rand = rand(5, 15);
	@endphp

	@if ($input['view'] == 'table')

		<div class="table-responsive">
			<table class="table table-bordered table-striped table-hover table-light ">
				<thead>
				<tr>
					<th></th>
					<th></th>
					<th style="width:1%; font-weight: normal;">
						{{ __('author.books_count') }}
					</th>
					<th style="width:1%; font-weight: normal;">
						{{ __('author.vote_average') }}
					</th>
				</tr>
				</thead>
				<tbody>
				@foreach ($authors as $author)
					<tr class="author" data-id="{{ $author->id }}">
						<td>
							@can ('merge', App\Author::class)
								<input type="checkbox"/>
							@endcan
						</td>
						<td>
							<h3 class="h6">
								<x-author-name :author="$author" showOnline="true"/>
							</h3>
						</td>
						<td>
							{{ $author->books_count }}
						</td>
						<td class="break-word-disable">
							{{ round($author->vote_average, 2) }} ({{ $author->votes_count }})
						</td>
					</tr>

				@endforeach
				</tbody>
			</table>
		</div>
	@else

		@foreach ($authors as $author)
			<div class="card mb-2">
				<div class="row no-gutters">
					<div class="col-md-3 col-lg-2 p-2 pl-4 col-sm-12 col-4 text-center d-flex align-items-center">
						<x-author-photo :author="$author" width="100" height="100" class="rounded" style="max-width: 100%;"/>
					</div>
					<div class="col-md-9 col-lg-10 col-sm-12 col-8">
						<div class="card-body">
							<h6 class="card-title">
								<x-author-name :author="$author" showOnline="true"/>
							</h6>
							<p class="card-text mb-1">{{ __('common.vote') }}: {{ round($author->vote_average, 2) }}
								({{ $author->votes_count }})
								@can ('merge', App\Author::class)
									<input type="checkbox"/>
								@endcan
							</p>
							<p class="card-text">{{ __('author.books_count') }}: {{ $author->books_count }}</p>
						</div>
					</div>
				</div>
			</div>

		@endforeach

	@endif

	<div class="row">
		<div class="col-12">
			@if ($authors->hasPages())
				{{ $authors->appends(request()->except(['page', 'ajax']))->links() }}
			@endif
		</div>
	</div>

@else
	<div class="row">
		<div class="col-12">
			<div class="alert alert-info">{{ __('author.nothing_found') }}</div>
		</div>
	</div>
@endif


