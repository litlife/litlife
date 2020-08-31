@extends('layouts.app')

@section('content')

	<div class="row">
		<div class="col-md-8 order-md-0 order-1">
			<div class="card mb-3">
				<div class="card-body">

					<form role="form" action="{{ $url }}"
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

						<h6 class="ml-1 mb-3">{{ __('notification.on_email', ['email' => optional($user->notice_email)->email]) }}</h6>

						<div class="table-responsive">
							<table class="table table-striped">
								@foreach ($user->email_notification_setting
								->only(['private_message', 'forum_reply', 'comment_reply', 'wall_message', 'wall_reply',
								'news']) as $column => $value)
									<tr>
										<td>
											{{ __('user_setting.'.$column) }}
										</td>
										<td>
											<input name="{{ $column }}" type="hidden" value="0"/>
											<input name="{{ $column }}" type="checkbox" @if ($value) checked @endif />
										</td>
									</tr>
								@endforeach
							</table>
						</div>

						<h6 class="ml-1 mb-3">{{ __('notification.on_site') }}</h6>

						<div class="table-responsive">
							<table class="table table-striped">
								@foreach ($user->email_notification_setting->only(['db_forum_reply', 'db_wall_message', 'db_comment_reply',
								'db_wall_reply', 'db_like', 'db_book_finish_parse', 'db_comment_vote_up']) as $column => $value)
									<tr>
										<td>
											{{ __('user_setting.'.$column) }}
										</td>
										<td>
											<input name="{{ $column }}" type="hidden" value="0"/>
											<input name="{{ $column }}" type="checkbox" @if ($value) checked @endif />
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