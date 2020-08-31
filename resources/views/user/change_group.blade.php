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

	<script type="text/javascript">

		document.addEventListener('DOMContentLoaded', function () {

			$("#groups_id").select2();

		});


	</script>

	<div class="card">
		<div class="card-body">

			<form role="form" action="{{ route('users.groups.update', $user) }}"
				  method="post" enctype="multipart/form-data">

				@csrf
				@method('patch')


				<div class="row form-group{{ $errors->has('groups_id') ? ' has-error' : '' }}">
					{{ Form::label('group_id', __('user.groups_id').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">

						<select id="groups_id" class="form-control" name="groups_id[]" multiple="multiple">

							@foreach ($groups as $group)

								<option value="{{ $group->id }}"
										@if (in_array($group->id, $user_groups)) selected="selected" @endif > {{ $group->name }}</option>

							@endforeach

						</select>

					</div>
				</div>

				<div class="row form-group{{ $errors->has('text_status') ? ' has-error' : '' }}">
					{{ Form::label('text_status', __('user.text_status').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('text_status', old('text_status') ?? $user->text_status, ['class' => 'form-control']) }}
					</div>
				</div>

				<div class="row form-group">
					<div class="col-sm-12">
						<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
					</div>
				</div>

			</form>

		</div>
	</div>
@endsection