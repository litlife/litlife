<style>
	/*

	.colorpicker.colorpicker-2x {
		width: 272px;
	}

	.colorpicker-2x .colorpicker-saturation {
		width: 200px;
		height: 200px;
	}

	.colorpicker-2x .colorpicker-hue,
	.colorpicker-2x .colorpicker-alpha {
		width: 30px;
		height: 200px;
	}

	.colorpicker-2x .colorpicker-alpha,
	.colorpicker-2x .colorpicker-preview {
		background-size: 20px 20px;
		background-position: 0 0, 10px 10px;
	}

	.colorpicker-2x .colorpicker-preview,
	.colorpicker-2x .colorpicker-preview div {
		height: 30px;
		font-size: 16px;
		line-height: 160%;
	}

	.colorpicker-saturation .colorpicker-guide {
		height: 10px;
		width: 10px;
		border-radius: 10px;
		margin: -5px 0 0 -5px;
	}
	*/

</style>

<form role="form" method="POST" class="read_style"
	  action="{{ route('users.settings.read_style', compact('user')) }}">

	@csrf

	<div class="form-group mt-3">
		<div class="form-check">

			<input name="show_sidebar" type="hidden" value="0"/>

			<input id="show_sidebar" name="show_sidebar" @if ($style->show_sidebar) checked @endif
			class="form-check-input {{ $errors->has('show_sidebar') ? 'is-invalid' : '' }}"
				   type="checkbox" value="1" data-default="{{ $default_style->show_sidebar }}"/>

			<label class="form-check-label" for="show_sidebar">
				{{ __('user.read_style_array.show_sidebar') }}
			</label>
		</div>
	</div>

	<div class="form-group">
		<label for="background_color" class="col-form-label">
			{{ __('user.read_style_array.background_color') }}:
		</label>

		<div id="cp1" class="input-group colorpicker-component">
			<input id="background_color" name="background_color" type="text"
				   data-default="{{ $default_style->background_color }}"
				   value="{{ $style->background_color ?? null }}"
				   class="form-control {{ $errors->has('background_color') ? 'is-invalid' : '' }}"/>
			<div class="input-group-append">
				<span class="input-group-text input-group-addon"><i></i></span>
			</div>
		</div>
	</div>

	<div class="form-group{{ $errors->has('font_color') ? ' has-error' : '' }}">
		<label for="font_color" class=" col-form-label">
			{{ __('user.read_style_array.font_color') }}:
		</label>

		<div id="cp2" class="input-group colorpicker-component">
			<input id="font_color" name="font_color" type="text"
				   data-default="{{ $default_style->font_color }}"
				   value="{{ $style->font_color ?? null }}"
				   class="form-control {{ $errors->has('font_color') ? 'is-invalid' : '' }}"/>
			<div class="input-group-append">
				<span class="input-group-text input-group-addon"><i></i></span>
			</div>
		</div>
	</div>

	<div class="form-group{{ $errors->has('card_color') ? ' has-error' : '' }}">
		<label for="card_color" class="col-form-label">
			{{ __('user.read_style_array.card_color') }}:
		</label>

		<div id="cp3" class="input-group colorpicker-component">
			<input id="card_color" name="card_color" type="text"
				   data-default="{{ $default_style->card_color }}"
				   value="{{ $style->card_color ?? null }}"
				   class="form-control {{ $errors->has('card_color') ? 'is-invalid' : '' }}"/>
			<div class="input-group-append">
				<span class="input-group-text input-group-addon"><i></i></span>
			</div>
		</div>
	</div>


	<div class="form-group{{ $errors->has('font') ? ' has-error' : '' }}">
		<label for="font" class="col-form-label">{{ __('user.read_style_array.font') }}:</label>

		<select id="font" name="font" class="form-control {{ $errors->has('font') ? 'is-invalid' : '' }}"
				data-default="{{ $default_style->font }}">
			@foreach (config('litlife.read_allowed_fonts') as $number => $str)
				@if ($str == $style->font)
					<option value="{{ $str }}" selected
							style="@if ($str != 'Default') font-family: '{{ $str }}' @endif">{{ __('user.read_style_array.font_array.'.$str) }}</option>
				@else
					<option value="{{ $str }}"
							style="@if ($str != 'Default') font-family: '{{ $str }}' @endif">{{ __('user.read_style_array.font_array.'.$str) }}</option>
				@endif
			@endforeach
		</select>

	</div>

	<div class="form-group">
		<label for="align" class="col-form-label">{{ __('user.read_style_array.align') }}:</label>
		<select id="align" name="align" class="form-control {{ $errors->has('align') ? 'is-invalid' : '' }}" data-default="{{ $default_style->align }}">
			@foreach (config('litlife.read_text_align') as $number => $str)
				@if ($str == $style->align)
					<option value="{{ $str }}"
							selected>{{ __('user.read_style_array.align_array.'.$str) }}</option>
				@else
					<option value="{{ $str }}">{{ __('user.read_style_array.align_array.'.$str) }}</option>
				@endif
			@endforeach
		</select>
	</div>

	<div class="form-group">
		<label for="inputEmail3" class=" col-form-label">{{ __('user.read_style_array.size') }}:</label>
		<select id="size" name="size" class="form-control {{ $errors->has('size') ? 'is-invalid' : '' }}" data-default="{{ $default_style->size }}">
			@foreach (config('litlife.read_font_size') as $number)
				@if ($number == $style->size)
					<option value="{{ $number }}" selected>{{ $number }}</option>
				@else
					<option value="{{ $number }}">{{ $number }}</option>
				@endif
			@endforeach
		</select>
	</div>


	<button type="submit" class="btn btn-primary">
		{{ __('common.save') }}
	</button>

	<button type="button" class="reset btn btn-light">
		{{ __('common.reset_settings') }}
	</button>

	<div class="output mt-3"></div>

</form>
