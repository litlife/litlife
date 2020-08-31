<div class="table-responsive">
	<table class="table">
		<thead class="thead-light">
		<tr>
			<th>ID</th>
			<th>{{ __('keyword.text') }}</th>
			<th>{{ __('model.book') }}</th>
			<th>{{ __('book_keyword.create_user') }}</th>
			<th>{{ __('book_keyword.created_at') }}</th>
			<th>{{ __('book_keyword.rating') }}</th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		@foreach ($keywords as $keyword)
			<tr class="keyword" data-id="{{ $keyword->id }}" data-book-id="{{ $keyword->book_id }}">
				<td>
					{{ $keyword->id }}
				</td>
				<td>
					<a href="{{ route('books', ['kw' => $keyword->text ]) }}">{{ $keyword->keyword->text }}</a>
				</td>
				<td>
					<x-book-name :book="$keyword->book"/>
				</td>
				<td>
					<x-user-name :user="$keyword->create_user"/>
				</td>
				<td>
					<x-time :time="$keyword->created_at"/>
				</td>
				<td>
					{{ $keyword->rating }}
				</td>
				<td>
					<div class="buttons">
						@can ('approve', $keyword)
							<button type="button" data-loading-text="{{ __('common.approving') }}"
									class="approve btn btn-success">
								{{ __('common.approve') }}
							</button>
						@endcan
						@can ('delete', $keyword)
							<button type="button" data-loading-text="{{ __('common.deleting') }}"
									class="delete btn btn-danger">
								{{ __('common.delete') }}
							</button>
						@endcan
					</div>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
</div>