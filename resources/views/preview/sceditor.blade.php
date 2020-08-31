@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	<div class="card">
		<div class="card-body">
			<form role="form" method="POST">
				<div class="form-group">
               <textarea id="sceditor" class="sceditor form-control" name="sceditor"
						 rows="{{ config('litlife.textarea_rows') }}"></textarea>
				</div>
			</form>
		</div>
	</div>

@endsection