@extends('layouts.app')

@push('scripts')

@endpush
@push('css')

@endpush

@section('content')

	<div class="row">
		<div class="col-md-8 order-md-0 order-1">

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

			<div class="card mb-3 ">
				<div class="card-body">

					<form action="{{ route('users.settings.site_appearance.update', $user) }}" role="form" method="POST">
						@csrf
						<div class="form-group{{ $errors->has('font_size_px') ? ' has-error' : '' }}">
							<label for="font_size_px" class="col-form-label">{{ __('user_setting.font_size_px') }}:</label>
							{{ Form::select('font_size_px', array_combine(range(config('litlife.font_size.min'), config('litlife.font_size.max')), range(config('litlife.font_size.min'), config('litlife.font_size.max'))), $user->setting->font_size_px ?? null, ['class' => 'form-control']) }}
							<small class="form-text text-muted">
								{{ __('user_setting.font_size_px_helper') }}
							</small>
						</div>

						<div class="form-group{{ $errors->has('font_family') ? ' has-error' : '' }}">
							<label for="font_family" class="col-form-label">{{ __('user_setting.font_family') }}:</label>
							<select id="font_family" name="font_family" class="form-control">
								<option value="">{{ __('common.default') }}</option>
								@foreach (config('litlife.available_fonts') as $number => $str)
									@if ($str == $user->setting->font_family)
										<option value="{{ $str }}" selected
												style="font-family: '{{ $str }}'">{{ __('user.read_style_array.font_array.'.$str) }}</option>
									@else
										<option value="{{ $str }}"
												style="font-family: '{{ $str }}'">{{ __('user.read_style_array.font_array.'.$str) }}</option>
									@endif
								@endforeach
							</select>
						</div>

						<button type="submit" class="btn btn-primary">
							{{ __('common.save') }}
						</button>
					</form>

				</div>
			</div>

		</div>
		<div class="col-md-4  order-md-1 order-0">
			@include ('user.setting.navbar')
		</div>
	</div>

@endsection



