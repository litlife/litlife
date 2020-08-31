@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/users.show.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	<div class="row" itemscope itemtype="http://schema.org/Person">

		<div class="col-md-4 col-lg-3">

			@section ('avatar')
				<div class="card mb-2">
					<div class="card-body text-center">

						<x-user-avatar :user="$user" width="180" height="300"
									   class="img-fluid rounded avatar pointer lazyload"
									   href="{{ route('users.avatar.show', $user) }}"
									   style="max-width: 100%;"/>

					</div>
				</div>
			@show

			<div class="card mb-2">


				<div class="list-group list-group-flush">

					@can('write_private_messages', $user)
						<a class="list-group-item" href="{{ route('users.messages.index', ['user' => $user]) }}">
							<i class="far fa-paper-plane"></i> {{ __('message.send') }}
						</a>
					@endcan

					@can('subscribe', $user)
						<a class="list-group-item" href="{{ route('users.subscribe', compact('user')) }}">
							{{ __('common.subscribe') }}
						</a>
					@elsecan('unsubscribe', $user)
						<a class="list-group-item"
						   href="{{ route('users.unsubscribe', compact('user')) }}">
							{{ __('common.unsubscribe') }}
						</a>
					@endcan

					@can('unblock', $user)
						<a class="list-group-item"
						   href="{{ route('users.unblock', compact('user')) }}">
							{{ __('common.unblock') }}
						</a>
					@endcan

					@section ('relations')
						@can ('view_relations', $user)
							<a class="list-group-item"
							   href="{{ route('users.friends', compact('user')) }}">
								{{ __('common.friends') }}
								<span class="badge badge-light float-right">{{ $user->friends_count }}</span>
							</a>
							<a class="list-group-item"
							   href="{{ route('users.subscriptions', compact('user')) }}">
								{{ __('common.subscriptions') }}

								<span class="badge badge-light float-right">{{ $user->subscriptions_count  }}</span>
							</a>
							<a class="list-group-item"
							   href="{{ route('users.subscribers', compact('user')) }}">
								{{ __('common.subscribers') }}
								<span class="badge badge-light float-right">{{ $user->subscribers_count }}</span>
							</a>
						@endcan
					@show
				</div>
			</div>

		</div>
		<div class="col-md-8 col-lg-9">
			<div class="card mb-2">
				<div class="card-header">
					<div class="d-flex w-100 justify-content-between">

						<h2 class="inline break-word h5">
							<x-user-name :user="$user" itemprop="name"/>
						</h2>
						<div class="ml-auto">
							<div class="btn-group dropdown" data-toggle="tooltip" data-placement="top"
								 title="{{ __('common.open_actions') }}">
								<button class="btn btn-light dropdown-toggle" type="button" id="user_{{ $user->id }}"
										data-toggle="dropdown"
										aria-haspopup="true" aria-expanded="false">
									<i class="fas fa-ellipsis-h"></i>
								</button>
								<div class="dropdown-menu dropdown-menu-right" role="menu"
									 aria-labelledby="user_{{ $user->id }}">

									@can ('update', $user)
										<a class="dropdown-item text-lowercase"
										   href="{{ route('users.edit', compact('user')) }}">
											<i class="far fa-edit"></i>
											{{ __('user.edit_profile') }}
										</a>
									@endcan

									@can ('attach_achievement', $user)
										<a class="dropdown-item text-lowercase"
										   href="{{ route('users.achievements', compact('user')) }}">
											{{ __('user.edit_achievements') }}
										</a>
									@endcan

									@if ($user->on_moderate)
										@can ('removeFromModerate', $user)
											<a class="dropdown-item text-lowercase"
											   href="{{ route('users.moderations.remove', compact('user')) }}">
												{{ __('user.remove_from_moderation') }}
											</a>
										@endcan
									@else
										@can ('addOnModerate', $user)
											<a class="dropdown-item text-lowercase"
											   href="{{ route('users.moderations.add', compact('user')) }}">
												{{ __('user.add_on_moderation') }}
											</a>
										@endcan
									@endif

									@can ('suspend', $user)
										<a class="dropdown-item text-lowercase"
										   href="{{ route('users.suspend', compact('user')) }}">
											{{ __('user.suspend') }}
										</a>
									@elsecan('unsuspend', $user)
										<a class="dropdown-item text-lowercase"
										   href="{{ route('users.unsuspend', compact('user')) }}">
											{{ __('user.unsuspend') }}
										</a>
									@endcan

									@can ('delete', $user)
										<a class="dropdown-item text-lowercase"
										   href="{{ route('users.delete', compact('user')) }}">
											<i class="fas fa-trash"></i>
											{{ __('user.delete') }}
										</a>
									@elsecan('restore', $user)
										<a class="dropdown-item text-lowercase"
										   href="{{ route('users.restore', compact('user')) }}">
											{{ __('common.restore') }}
										</a>
									@endcan

									@can('create', App\AdminNote::class)
										<a class="dropdown-item text-lowercase"
										   href="{{ route('admin_notes.create', ['type' => 'user', 'id' => $user->id]) }}">
											{{ __('user.create_admin_note') }}
										</a>
									@endcan

									@can ('watch_activity_logs', $user)
										<a class="dropdown-item text-lowercase" href="{{route('users.activity_logs', $user) }}">
											{{ __('user.logs') }}
										</a>
									@endcan

									@can ('change_group', $user)
										<a class="dropdown-item text-lowercase" href="{{ route("users.groups.edit", $user) }}">
											{{ __('user.change_user_group') }}
										</a>
									@endcan

									@can ('ban', $user)
										<a class="dropdown-item text-lowercase" href="{{ route("users.ban", $user) }}">
											<i class="fas fa-ban"></i> {{ __('user.ban_a_user') }}
										</a>
									@endcan

									@can ('refresh_counters', $user)
										<a class="dropdown-item text-lowercase"
										   href="{{ route("users.refresh_counters", $user) }}">
											{{ __('common.refresh_counters') }}
										</a>
									@endcan

									@can('block', $user)
										<a class="dropdown-item text-lowercase"
										   href="{{ route('users.block', compact('user')) }}">
											<i class="fa fa-ban"></i>
											{{ __('user.add_to_blacklist') }}
										</a>
									@elsecan('unblock', $user)
										<a class="dropdown-item text-lowercase"
										   href="{{ route('users.unblock', compact('user')) }}">
											{{ __('user.remove_from_the_blacklist') }}
										</a>
									@endcan

								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-body">


					@section ('achievements')
						@if (!empty($user->latest_user_achievements))
							<div class="row mb-2">
								<div class="col-12 btn-margin-bottom-1">
									@foreach ($user->latest_user_achievements as $latest_user_achievement)
										@include('achievement.badge', ['user_achievement' => $latest_user_achievement])
									@endforeach

									@if ($user->latest_user_achievements->count() < $user->achievements_count)
										<a class="btn btn-light text-muted"
										   href="{{ route('users.achievements', ['user' => $user ?: null]) }}"
										   data-toggle="tooltip" data-placement="top" title="{{ __('achievement.show_all') }}">
											{{ __('common.more') }} {{ ($user->achievements_count-$user->latest_user_achievements->count()) }}
										</a>
									@endif
								</div>
							</div>
						@endif
					@show
					@section ('admin_note')
						@include('admin_note.item', ['object' => $user, 'type' => 'user'])
					@show

					@can('see_ip', $user)
						<div class="row mb-2">
							<div class="col-12">
								<span class="font-weight-bold small">{{ __('user.latest_auth_ip') }}:</span>
								@if (!empty($last_auth_log))
									{{ $last_auth_log->ip ?? '' }}
									<a target="_blank"
									   href="http://www.seogadget.ru/location?addr={{ $last_auth_log->ip }}">{{ geoip($last_auth_log->ip)->country }}
										/ {{ geoip($last_auth_log->ip)->state_name }}
										/ {{ geoip($last_auth_log->ip)->city }}</a>
									<a href="{{ route('users.auth_logs', $user) }}"
									   target="_blank">{{ __('user.latest_user_auth') }}</a>
									<a href="{{ route('all_users_auth_logs', ['ip' => $last_auth_log->ip]) }}"
									   target="_blank">{{ __('user.latest_users_auth_for_ip') }}</a>
								@endif
							</div>
						</div>
					@endif
					@section ('description')
						<div class="row mb-2">
							<div class="col-lg-6">
								@if (!empty($user->groups))
									<div class=" ">
										@foreach ($user->groups->where('show', true) as $group)
											<a href="{{ route('users', ['group' => $group->id]) }}">{{ $group->name }}</a>{{ $loop->last ? '' : ', ' }}
										@endforeach
									</div>
								@endif

								@if (!empty($user->text_status))
									<div class=" ">
										<span class="font-weight-bold small"> {{ __('user.text_status') }}:</span>
										{{ $user->text_status }}
									</div>
								@endif

								@if ($emails->count())
									<div>
										<span class="font-weight-bold small">{{ trans_choice('user.email', $emails->count()) }}:</span>

										@foreach ($emails as $email)
											<div class="d-inline-block">
												<a href="mailto:{{ $email->email }}">{{ $email->email }}</a>
											</div>{{ $loop->last ? '' : ', ' }}
										@endforeach

										@can ('view_email_list', $user)
											<a href="{{ route("users.emails.index", $user) }}">
												{{ __('common.edit') }}
											</a>
										@endcan
									</div>
								@endif

								<div class=" ">
									<span class="font-weight-bold small">{{ __('user.gender') }}:</span> {{ __('gender.'.$user->gender) }}
									<meta itemprop="gender" content="{{ $user->gender }}">
								</div>
								<div class=" ">

									@if ($user->isOnline())
										<span class="font-weight-bold small">{{ __('common.now_online') }}</span>
									@elseif ($user->last_activity_at)
										<span class="font-weight-bold small">{{ __('user.last_activity_at') }}:</span>
										<x-time :time="$user->last_activity_at"/>
									@endif
								</div>
								<div class=" ">
									<span class="font-weight-bold small">{{ __('user.created_at') }}:</span>
									<x-time :time="$user->created_at"/>
								</div>
								@if (!empty($user->born_date_format))
									<div class=" ">
										<span class="font-weight-bold small">{{ __('user.born_date') }}:</span>
										{{ $user->born_date_format }}
									</div>
								@endif
								@if (!empty($user->city))
									<div class=" ">
										<span class="font-weight-bold small">{{ __('user.city') }}:</span>
										{{ $user->city }}
									</div>
								@endif
							</div>
							<div class="col-lg-6">
								@if (!empty($user->book_read_count))
									<div class=" ">
										<a href="{{ route('users.books.readed', ['user' => $user]) }}"><span
													class="font-weight-bold small">
                            @if (auth()->id() == $user->id)
													{{ trans_choice('user.my_read_status_array.readed', $user->gender) }}:
												@else
													{{ trans_choice('user.read_status_with_gender_array.readed', $user->gender) }}:
												@endif</span>
											{{ $user->book_read_count }}</a>
									</div>
								@endif

								@if (!empty($user->book_read_later_count))
									<div class=" ">
										<a href="{{ route('users.books.read_later', ['user' => $user]) }}">
                                            <span class="font-weight-bold small">
                            @if (auth()->id() == $user->id)
													{{ trans_choice('user.my_read_status_array.read_later', $user->gender) }}
													:
												@else
													{{ trans_choice('user.read_status_with_gender_array.read_later', $user->gender) }}
													:
												@endif</span>
											{{ $user->book_read_later_count }}</a>
									</div>
								@endif

								@if (!empty($user->book_read_now_count))
									<div class=" ">
										<a href="{{ route('users.books.read_now', ['user' => $user]) }}">
                                            <span class="font-weight-bold small">
                            @if (auth()->id() == $user->id)
													{{ trans_choice('user.my_read_status_array.read_now', $user->gender) }}
													:
												@else
													{{ trans_choice('user.read_status_with_gender_array.read_now', $user->gender) }}
													:
												@endif</span>
											{{ $user->book_read_now_count }}</a>
									</div>
								@endif

								@if (!empty($user->book_not_complete_count))
									<div class=" ">
										<a href="{{ route('users.books.read_not_complete', ['user' => $user]) }}">
                                            <span class="font-weight-bold small">
                            @if (auth()->id() == $user->id)
													{{ trans_choice('user.my_read_status_array.read_not_complete', $user->gender) }}
													:
												@else
													{{ trans_choice('user.read_status_with_gender_array.read_not_complete', $user->gender) }}
													:
												@endif</span>
											{{ $user->book_not_complete_count }}</a>
									</div>
								@endif
								<div class=" ">
									<a href="{{ route('users.books.comments', ['user' => $user]) }}">
										<span class="font-weight-bold small">{{ __('user.comments_count') }}:</span>
										{{ $user->comment_count }}</a></div>
								<div class=" ">
									<a href="{{ route('users.posts', ['user' => $user]) }}">
										<span class="font-weight-bold small">{{ __('user.posts_count') }}:</span>
										{{ $user->forum_message_count }}</a>
								</div>
								<div class=" ">
									<a href="{{ route('users.topics', ['user' => $user]) }}">
										<span class="font-weight-bold small">{{ __('user.topics_count') }}:</span>
										{{ $user->topics_count }}</a>
								</div>
								<div class=" ">
									<a href="{{ route("users.votes", ['user' => $user, 'view' => 'table']) }}">
										<span class="font-weight-bold small">{{ __('user.book_vote_count') }}:</span>
										{{ $user->book_rate_count }}</a>
								</div>
								@can('view_created', $user)
									<div class=" ">
										<a href="{{ route("users.books.created", ['user' => $user, 'order' => 'date_down']) }}">
											<span class="font-weight-bold small">{{ __('user.created_books') }}:</span>
											{{ $user->data->created_books_count ?? '0' }}</a>
									</div>
								@endcan
							</div>

							<div class="col-12 mt-3">
								@if (!empty($user->data->about_self))
									<div class=" ">
										<span class="font-weight-bold small">{{ __('user.about_self') }}:</span> {{ $user->data->about_self }}
									</div>
								@endif

								@if (!empty($user->data->i_love))
									<div class=" ">
										<span class="font-weight-bold small"> {{ __('user.i_love') }}:</span> {{ $user->data->i_love }}
									</div>
								@endif

								@if (!empty($user->data->i_hate))
									<div class=" ">
										<span class="font-weight-bold small">{{ __('user.i_hate') }}:</span> {{ $user->data->i_hate }}
									</div>
								@endif

								@if (!empty($user->data->favorite_authors))
									<div class=" ">
										<span class="font-weight-bold small"> {{ __('user.favorite_authors') }}:</span> {{ $user->data->favorite_authors }}
									</div>
								@endif

								@if (!empty($user->data->favorite_genres))
									<div class=" ">
										<span class="font-weight-bold small">{{ __('user.favorite_genres') }}:</span> {{ $user->data->favorite_genres }}
									</div>
								@endif

								@if (!empty($user->data->favorite_music))
									<div class=" ">
										<span class="font-weight-bold small">{{ __('user.favorite_music') }}:</span> {{ $user->data->favorite_music }}
									</div>
								@endif

								@if (!empty($user->data->favorite_quote))
									<div class=" ">
										<span class="font-weight-bold small">{{ __('user.favorite_quote') }}:</span> {{ $user->data->favorite_quote }}
									</div>
								@endif

								@if ($managers->count() > 0)

									@if ($authors = $managers->where('character', 'author') and count($authors) > 0)
										<div class="">
											<span class="font-weight-bold small">{{ __('user.author_pages') }}</span>:
											@foreach ($authors as $manager)
												<x-author-name :author="$manager->manageable" showOnline="0"/>{{ $loop->last ? '' : ', ' }}
											@endforeach
										</div>
									@endif

									@if ($editors = $managers->where('character', 'editor') and count($editors) > 0)
										<div class="">
											<span class="font-weight-bold small">{{ __('user.edited_pages') }}</span>:
											@foreach ($editors as $manager)
												<x-author-name :author="$manager->manageable" showOnline="0"/>{{ $loop->last ? '' : ', ' }}
											@endforeach
										</div>
									@endif
								@endif

							</div>
						</div>
					@show
				</div>
			</div>
		</div>
	</div>

@section ('blog')

	@if (!$user->trashed())

		@if ((isset($top_blog_record)) and ($blogs->currentPage() == 1))
			<div class="top-blogs" data-user-id="{{ $user->id }}">
				@include('blog.list.default', ['item' => $top_blog_record, 'descendants' => $descendants ?? null, 'is_fixed' => true])
			</div>
		@endif

		@can('writeOnWall', $user)
			@include('blog.create_form', compact('user'))
		@endcan

		<div class="blogs" data-user-id="{{ $user->id }}">
			@include('blog.index')
		</div>

	@endif

@show

@endsection