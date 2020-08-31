@extends('layouts.app')

@section('content')

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<div class="card">
		<div class="card-body">


			<form role="form" action="{{ route('groups.store') }}"
				  method="post" enctype="multipart/form-data">

				@csrf


				<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
					{{ Form::label('name', __('user_group.name').' ') }}

					{{ Form::text('name', old('name'), ['class' => 'form-control']) }}
				</div>

				<div class="form-check mb-1">
					<input class="form-check-input" type="checkbox" id="checkAll">

					<label class="form-check-label" for="checkAll">
						{{ __('common.select_all') }}
					</label>
				</div>

				<div class="form-check">
					<input type="hidden" value="0" name="show"/>

					<input class="form-check-input" type="checkbox" value="1"
						   name="show" id="show" {{ (old('show') or $group->show) ? 'checked' : '' }} />

					<label class="form-check-label" for="show">
						{{ __('user_group.show') }}
					</label>
				</div>

				@foreach (collect($group->getAttributes())->only($group->getPermissions()) as $name => $value)

					<div class="form-check mb-1">
						<input type="hidden" value="0" name="{{ $name }}"/>

						<input class="form-check-input" type="checkbox" value="1"
							   name="{{ $name }}" id="{{ $name }}" {{ (old($name) or $value) ? 'checked' : '' }} />

						<label class="form-check-label" for="{{ $name }}">
							{{ __('user_group.'.$name) }}
						</label>
					</div>

				@endforeach

				<button type="submit" class="btn btn-primary">{{ __('common.create') }}</button>

			</form>

			<script type="text/javascript">
				document.addEventListener('DOMContentLoaded', function () {
					$("#checkAll").click(function () {
						$(".form-check-input").prop('checked', $(this).prop('checked'));
					});
				});
			</script>

		</div>
	</div>

@endsection