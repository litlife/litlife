@extends('layouts.app')

@section('content')

	<div class="card">
		<div class="card-body">


			<form role="form" action="{{ route('text_blocks.update', ['name' => $textBlock->name]) }}"
				  method="post" enctype="multipart/form-data">

				@csrf
				@method('patch')

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

					<input name="name" class="form-control" value="{{ $textBlock->name }}" readonly="readonly">

				</div>

				<div class="form-group">

					<label class="col-form-label">{{ __('text_block.show_status') }}</label>

					<select name="show_for_all" class="form-control">
						@foreach (\App\Enums\TextBlockShowEnum::toSelectArray() as $key => $value)
							@if ($key == (old('show_for_all') ?: $textBlock->show_for_all))
								<option value="{{ $key }}" selected>{{ __('text_block.show_status_array.'.$value) }}</option>
							@else
								<option value="{{ $key }}">{{ __('text_block.show_status_array.'.$value) }}</option>
							@endif
						@endforeach
					</select>

				</div>

				<div class="form-group">

					<label class="col-form-label">{{ __('text_block.text') }}</label>

					<textarea class="editor form-control"
							  name="text">{{ old('text') ?? $textBlock->text  }}</textarea>

				</div>

				<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>

			</form>

		</div>
	</div>

@endsection
