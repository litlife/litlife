@extends('layouts.app')

@push('scripts')

@endpush

@push('body_append')

	<script type="text/javascript">
		$(function () {
			setTimeout(function () {
				location.reload();
			}, 15000);
		});
	</script>

@endpush

@section('content')

	@include ('book.create.tab')

	@if ($book->parse->isWait())
		<div class="alert alert-info" role="alert">
			<i class="fas fa-spinner fa-spin"></i> &nbsp;
			{{ __('book.parse.wait') }}
		</div>
	@elseif ($book->parse->isProgress())
		<div class="alert alert-info" role="alert">
			<i class="fas fa-spinner fa-spin"></i> &nbsp;
			{{ __('book.parse.progress') }}
		</div>
	@endif

@endsection
