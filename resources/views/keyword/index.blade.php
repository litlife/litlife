@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/keywords.index.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	@can('create', \App\Keyword::class)
		<a href="{{ route('keywords.create') }}" class="btn btn-primary mb-3">
			{{ __('common.create') }}
		</a>
	@endcan

	@if(count($keywords) > 0)

		{{ $keywords->appends(request()->except(['page', 'ajax']))->links() }}
		<div class="table-responsive">
			<table class="table table-striped table-light">
				<tr data-item="keyword">
					<th>
						{{ __('keyword.text') }}
					</th>
					<th>
						{{ trans_choice('book.books', 2) }}
					</th>
					<th>

					</th>
				</tr>

				@foreach ($keywords as $keyword)
					<tr data-item="keyword">
						<td>
							<a data-text href="{{ route('books', ['kw' => $keyword->text]) }}">{{ $keyword->text }}</a>
						</td>
						<td>
							{{ $keyword->count }}
						</td>
						<td>
							<div class="dropdown" data-toggle="tooltip" data-placement="top"
								 title="{{ __('common.open_actions') }}">
								<button id="dropdownKeyword_{{ $keyword->id }}" class="btn btn-light dropdown-toggle"
										type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<i class="fas fa-ellipsis-h"></i>
								</button>
								<div class="dropdown-menu dropdown-menu-right"
									 aria-labelledby="dropdownKeyword_{{ $keyword->id }}">
									@can('update', $keyword)
										<a class="btn-edit dropdown-item text-lowercase pointer"
										   target="_blank"
										   href="{{ route('keywords.edit', ['keyword' => $keyword]) }}">
											{{ __('common.edit') }}
										</a>
									@endcan
									<a class="delete pointer dropdown-item text-lowercase"
									   href="{{ route('keywords.destroy', ['keyword' => $keyword]) }}" disabled="disabled"
									   data-loading-text="{{ __('common.deleting') }}"
									   @cannot ('delete', $keyword) style="display:none;"@endcannot>
										{{ __('common.delete') }}
									</a>

									<a class="restore pointer dropdown-item text-lowercase"
									   href="{{ route('keywords.destroy', ['keyword' => $keyword]) }}" disabled="disabled"
									   data-loading-text="{{ __('common.restoring') }}"
									   @cannot ('restore', $keyword) style="display:none;"@endcannot>
										{{ __('common.restore') }}
									</a>
								</div>
							</div>
						</td>
					</tr>
				@endforeach
			</table>
		</div>
		{{ $keywords->appends(request()->except(['page', 'ajax']))->links() }}

	@else
		<div class="alert alert-info">
			{{ __('keyword.nothing_found') }}
		</div>
	@endif


@endsection