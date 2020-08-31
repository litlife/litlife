@extends('layouts.app')

@section('content')

	<div class="card">
		<div class="card-body">


			<form role="form" action="{{ route('text_blocks.store', ['name' => $name]) }}"
				  method="post" enctype="multipart/form-data">

				@csrf


				@if ($errors->any())
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				@include('ckeditor')

				<div class=" form-group">

					<label class="col-form-label"></label>

					<input name="name" class="form-control" value="{{ $name }}" readonly="readonly">

				</div>

				<div class="form-group">
					<label class="col-form-label">{{ __('text_block.show_status') }}</label>

					<select name="show_for_all" class="form-control">
						@foreach (\App\Enums\TextBlockShowEnum::toSelectArray() as $key => $value)
							<option value="{{ $key }}">{{ __('text_block.show_status_array.'.$value) }}</option>
						@endforeach
					</select>
				</div>

				<div class=" form-group">
					<label class="col-form-label">{{ __('text_block.text') }}</label>

					<textarea class="editor form-control" name="text">{{ old('text') }}</textarea>
				</div>


				<button type="submit" class="btn btn-primary">{{ __('common.create') }}</button>

			</form>
		</div>
	</div>

@endsection
