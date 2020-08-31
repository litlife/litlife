@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/users.achievements.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')



	@can ('attach', App\Achievement::class)

		<div class="card mb-3">
			<div class="card-body">

				<form class="mr-2" role="form" method="POST"
					  action="{{ route('users.achievements.attach', compact('user')) }}"
					  enctype="multipart/form-data">
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

					<div class="form-row align-items-center">
						<div class="col-auto mb-xs-3">
							<label class="mr-sm-2 sr-only" for="achievement">{{ __('achievement.title') }}</label>

							<select id="achievement" name="achievement" class="select custom-select mr-sm-2" required
									data-placeholder="{{ __('achievement.please_choose') }}">
								<option value="">{{ __('achievement.please_choose') }}</option>
							</select>
						</div>
						<div class="col-auto">
							<button type="submit" class="btn btn-primary">{{ __('common.attach') }}</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	@endcan



	@if (!empty($user_achievements) and $user_achievements->count())
		<div class="achievements card-columns">
			@foreach ($user_achievements as $user_achievement)
				<div class="item card" data-id="{{ $user_achievement->id }}">
					@if (!empty($user_achievement->achievement->image))
						<img class="card-img-top lazyload"
							 src="{{ $user_achievement->achievement->image->fullUrlMaxSize(350, 350) }}" alt="">
					@endif
					<div class="card-body">
						<h5 class="card-title">
							<a href="{{ route('achievements.show', ['achievement' => $user_achievement->achievement]) }}">
								{{ $user_achievement->achievement->title }}
							</a>
						</h5>
						<p class="card-text">{{ $user_achievement->achievement->description }}</p>

						<div class="btn-group dropdown">
							<button class="btn btn-light dropdown-toggle" type="button"
									id="user_achievement_{{ $user_achievement->id }}"
									data-toggle="dropdown"
									aria-haspopup="true"
									aria-expanded="false">
								<i class="fas fa-ellipsis-h"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-right"
								 aria-labelledby="user_achievement_{{ $user_achievement->id }}">
								@can ('detach', $user_achievement->achievement)
									<a class="dropdown-item"
									   href="{{ route('users.achievements.detach', ['user' => $user, 'achievement' => $user_achievement->achievement]) }}">
										{{ __('common.detach') }}
									</a>
								@endcan

								@can ('update', $user_achievement->achievement)
									<a class="dropdown-item"
									   href="{{ route('achievements.edit', $user_achievement->achievement) }}">
										{{ __('common.edit') }}
									</a>
								@endcan
							</div>
						</div>
					</div>
					@if (!empty($user_achievement->created_at))
						<div class="card-footer">
							<small
									class="text-muted">{{ __('achievement.attached_at') }}
								<x-time :time="$user_achievement->created_at"/>
							</small>
						</div>
					@endif

				</div>
			@endforeach
		</div>
	@else

		<div class="alert alert-info">{{ __('achievement.nothing_found') }}</div>

	@endif


	@if ($user_achievements->hasPages())
		<div class="row">
			<div class="col-12">
				{{ $user_achievements->appends(request()->except(['page', 'ajax']))->links() }}
			</div>
		</div>
	@endif

@endsection