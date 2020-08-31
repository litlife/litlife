@extends('layouts.app')

@section('content')

	<form>
		<div class="form-group">
			<input id="fileupload" type="file" name="upload" data-url="{{ route('images.store') }}"
				   size="{{ ByteUnits\Metric::bytes(config('litlife.max_image_size'))->numberOfBytes() }}"
				   accept="image/jpeg, image/png, image/gif">
			<small id="fileuploadHelpBlock" class="form-text text-muted">
				{{ __('common.max_size') }}: {{ ByteUnits\Metric::kilobytes(config('litlife.max_image_size'))->format() }}
			</small>
			<small id="fileuploadHelpBlock" class="form-text text-muted">
				{{ __('image.supported_formats') }}: {{ implode(', ', config('litlife.support_images_formats')) }}
			</small>
		</div>
	</form>

@endsection