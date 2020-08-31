@extends('layouts.app')

@section('content')

	<div class="row">

		<div class="col-md-8 order-md-0 order-1">
			<div class="card mb-3">
				<div class="card-body">

					<form role="form" action="{{ route('allowance.patch', $user) }}"
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

						@if (session('success'))
							<div class="alert alert-success alert-dismissable">
								{{ session('success') }}
								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							</div>
						@endif
						<div class="table-responsive">
							<table class="table table-striped">

								@foreach ($user->account_permissions->getFillable() as $key)
									<tr>
										<td>
											{{ __('user_setting.'.$key) }}
										</td>
										<td>
											<select name="{{ $key }}" class="form-control">
												@foreach ($user->account_permissions->possible_values[$key] as $value)
													<option value="{{ $value }}"
															@if ($user->account_permissions->$key == $value) selected @endif>
														{{ __('user_setting.'.\App\Enums\UserAccountPermissionValues::getKey($value)) }}
													</option>
												@endforeach
											</select>
										</td>
									</tr>
								@endforeach

							</table>
						</div>

						<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>


					</form>
				</div>
			</div>
		</div>
		<div class="col-md-4  order-md-1 order-0">

			@include ('user.setting.navbar')

		</div>
	</div>


@endsection