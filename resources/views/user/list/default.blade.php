<div class="card mb-2">
	<div class="card-body p-2">
		<div class="row">
			<div class="col-md-3 col-lg-2 p-2 col-sm-12 col-3 text-center">
				<x-user-avatar :user="$user" width="50" height="50" class="rounded" style="max-width: 100%;"/>
			</div>
			<div class="col-md-9 col-lg-10 col-sm-12 col-9">
				<div class="d-flex w-100 justify-content-between">
					<div>
						<h3 class="card-title h6 mb-1">
							<x-user-name :user="$user"/>
						</h3>
						<p class="card-text mb-0">

							@if (empty($description))

								@if (!empty($user))
									<small class="text-muted">
										@foreach ($user->getGroupStatus() as $name)
											{{ $name }}{{ $loop->last ? '' : ', ' }}
										@endforeach
									</small>
								@endif

								@if (!empty($user->latest_user_achievements))

									@foreach ($user->latest_user_achievements as $latest_user_achievement)
										<a class="btn btn-light text-muted btn-sm"
										   href="{{ route('achievements.show', ['achievement' => $latest_user_achievement->achievement]) }}"
										   data-toggle="tooltip" data-placement="top"
										   title="{{ $latest_user_achievement->achievement->title }}"
										   style="text-decoration: none;">
											<img src="{{ $latest_user_achievement->achievement->image->fullUrlMaxSize(20, 20) }}"
												 style="max-width:20px; max-height:20px"/>
										</a>
									@endforeach

									@if ($user->latest_user_achievements->count() < $user->achievements_count)
										<a class="btn btn-light text-muted  btn-sm"
										   href="{{ route('users.achievements', ['user' => $user ?: null]) }}"
										   data-toggle="tooltip" data-placement="top"
										   title="{{ __('achievement.show_all') }}">
											{{ __('common.more') }} {{ ($user->achievements_count-$user->latest_user_achievements->count()) }}
										</a>
									@endif

								@endif

							@else
								<small class="text-muted">{{ $description }}</small>
							@endif
						</p>
					</div>
					<div class="ml-auprogressto">
						{{ $dropdown ?? '' }}
					</div>
				</div>

				{{ $slot ?? '' }}
			</div>
		</div>
	</div>
</div>

@isset($rand)
	@if (!empty($loop) and $loop->index == $rand)
		@can('see_ads', \App\User::class)
			<div class="col-12 mb-3">
				<x-ad-block name="adaptive_horizontal"/>
			</div>
		@endcan
	@endif
@endisset