<div class="row form-group{{ $errors->has($name) ? ' has-error' : '' }}">
	{{ Form::label($name, __('author.'.$name).' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
	<div class="col-md-9 col-lg-10">
		{{ Form::text($name, old($name) ?: $item->$name, ['class' => 'form-control']) }}
	</div>
</div>