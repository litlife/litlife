@extends('layouts.app')

@section('content')

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	<a class="btn btn-primary mb-3" href="{{ route('mailings.create') }}">{{ __('common.add') }}</a>

	<div class="card">
		<div class="card-body">

			<div class="mb-3">
				<a class="btn btn-sm btn-primary"
				   href="{{ route('mailings.index', array_merge(request()->except(['page', 'ajax']),
            ['sort' => 'rating'])) }}">
					Сортировать по рейтингу</a>
				<a class="btn btn-sm btn-primary"
				   href="{{ route('mailings.index', array_merge(request()->except(['page', 'ajax']),
            ['sort' => 'latest_sent'])) }}">
					Последние отправленные</a>
				<a class="btn btn-sm btn-primary"
				   href="{{ route('mailings.index', array_merge(request()->except(['page', 'ajax']),
            ['show' => 'sent'])) }}">
					Показать отправленные
				</a>
				<a class="btn btn-sm btn-primary"
				   href="{{ route('mailings.index', array_merge(request()->except(['page', 'ajax']),
            ['show' => 'waited'])) }}">
					Показать не отправленные
				</a>
			</div>

			<div class="mb-3">
				@if ($mailings->count() < 1)
					<div class="alert alert-info">Ничего не найдено</div>
				@else
					@foreach ($mailings as $mailing)
						<div>
							{{ $mailing->email }}

							{{ $mailing->priority }}

							@if ($mailing->isSent())
								<i class="far fa-check-circle"></i>
								<x-time :time="$mailing->sent_at"/>
							@else
								<i class="far fa-clock"></i>
							@endif
						</div>
					@endforeach

				@endif
			</div>

			@if ($mailings->hasPages())
				{{ $mailings->appends(request()->except(['page', 'ajax']))->links() }}
			@endif
		</div>
	</div>

@endsection