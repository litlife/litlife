@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/preview.comment.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@php(!empty($level) ?: $level = 0)

	@component('components.comment', get_defined_vars())

		@slot('anchor')

		@endslot

		@slot('data_attributes')

		@endslot

		@slot('avatar')
			<x-user-avatar :user="$item->create_user" width="50" height="50"/>
		@endslot

		<h6 class="mb-2">
			<x-user-name :user="$item->create_user"/>

			@if (isset($parent))
				{{ trans_choice('post.answer', $item->create_user->gender ?? 'unknown') }}
				<x-user-name :user="$parent->create_user"/>
			@endif

			<x-time :time="$item->created_at"/>

			@if (!empty($is_fixed))
				<i class="fas fa-thumbtack"></i>
			@endif
		</h6>

		<div class="mb-2">
			<div class="html_box imgs-fluid " style=" max-height: 600px; overflow-x:auto; overflow-y:hidden;">

				ddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd

				{!! $item->text !!}
			</div>
		</div>

		@if (empty($no_button_panel))

			<div class="btn-margin-bottom-1">

				@include('like.item', ['item' => $item, 'like' => pos($item->likes) ?: null, 'likeable_type' => 'blog'])

				@can('reply', $item)
					<a class="btn btn-light btn-reply" data-toggle="tooltip" data-placement="top"
					   title="{{ __('common.reply') }}"
					   href="{{ route('users.blogs.create', ['user' => $item->owner, 'parent' => $item]) }}">
						<i class="far fa-comment"></i>
					</a>
				@endcan

				@if (empty($no_child_toggle))

					<button class="btn btn-light close_descendants"
							data-toggle="tooltip" data-placement="top" title="{{ __('common.hide_replies') }}"
							@if ($item->children_count < 1 or !$item->isHaveDescendant($descendants)) style="display:none;" @endif>
						<i class="far fa-comments"></i>

						<span class="counter">{{ $item->children_count }}</span>
					</button>

					<button class="btn btn-light open_descendants"
							data-toggle="tooltip" data-placement="top" title="{{ __('common.show_replies') }}"
							@if ($item->children_count < 1 or $item->isHaveDescendant($descendants)) style="display:none;" @endif>
						<i class="fas fa-comments"></i>

						<span class="counter">{{ $item->children_count }}</span>
					</button>

				@endif

				@if (!empty($parent))
					<a href="#blog_{{ $parent->id }}" class="btn btn-light"
					   data-toggle="tooltip" data-placement="top" title="{{ __('common.go_to_parent') }}">
						<i class="fas fa-arrow-up"></i>
					</a>
				@endif

				<button class="btn btn-light btn-compress" style="display: none;"
						data-toggle="tooltip" data-placement="top" title="{{ __('common.compress') }}">
					<i class="fas fa-compress"></i>
				</button>

				<button class="btn btn-light btn-expand" style="display: none;"
						data-toggle="tooltip" data-placement="top" title="{{ __('common.expand') }}">
					<i class="fas fa-expand"></i>
				</button>

				@if (!empty($go_to_button))
					<a href="{{ route('users.blogs.go', ['user' => $item->owner, 'blog' => $item]) }}"
					   data-toggle="tooltip" data-placement="top" title="{{ __('blog.go_to') }}"
					   class="btn btn-light">
						<i class="fas fa-angle-right"></i>
					</a>
				@endif

				<button class="btn btn-light share" data-toggle="tooltip"
						data-title="{{ e($item->getShareTitle()) }}"
						data-description="{{ e($item->getShareDescription()) }}"
						data-url="{{ route('users.blogs.go', ['user' => $item->owner, 'blog' => $item]) }}"
						data-placement="top" title="{{ __('blog.share') }}">
					<i class="far fa-share-square"></i>
				</button>

				<div class="dropdown d-inline-block" data-toggle="tooltip" data-placement="top"
					 title="{{ __('common.open_actions') }}">
					<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton_{{ $item->id }}"
							data-toggle="dropdown"
							aria-haspopup="true"
							aria-expanded="false">
						<i class="fas fa-ellipsis-h"></i>
					</button>

					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton_{{ $item->id }}">
						<a class="delete pointer dropdown-item text-lowercase" href="javascript:void(0)" disabled="disabled"
						   data-loading-text="{{ __('common.deleting') }}"
						   @cannot ('delete', $item) style="display:none;"@endcannot>
							{{ __('common.delete') }}
						</a>

						<a class="restore pointer dropdown-item text-lowercase" href="javascript:void(0)" disabled="disabled"
						   data-loading-text="{{ __('common.restoring') }}"
						   @cannot ('restore', $item) style="display:none;"@endcannot>
							{{ __('common.restore') }}
						</a>

						@can('update', $item)
							<a class="btn-edit dropdown-item text-lowercase pointer"
							   href="{{ route('users.blogs.edit', ['user' => $item->owner, 'blog' => $item]) }}">
								{{ __('common.edit') }}
							</a>
						@endcan

						@can('fix', $item)
							<a class="dropdown-item text-lowercase"
							   href="{{ route('users.blogs.fix', ['user' => $item->owner, 'blog' => $item]) }}">
								{{ __('common.fix') }}
							</a>
						@endcan

						@can('unfix', $item)
							<a class="dropdown-item text-lowercase"
							   href="{{ route('users.blogs.unfix', ['user' => $item->owner, 'blog' => $item]) }}">
								{{ __('common.unfix') }}
							</a>
						@endcan

						<a class="dropdown-item text-lowercase" target="_blank"
						   href="{{ route("users.comments.who_likes", ['id' => $item->id, 'type' => 'blog']) }}">
							{{ __('blog.who_likes') }}
						</a>

						<a class="abuse dropdown-item text-lowercase" target="_blank"
						   href="{{ route("complains.report", ['type' => 'blog', 'id' => $item->id]) }}">
							{{ __('common.complain') }}
						</a>

						<a class="get_link pointer dropdown-item text-lowercase" href="javascript:void(0)"
						   href="{{ route('users.blogs.go', ['user' => $item->owner, 'blog' => $item]) }}"
						   data-href="{{ route('users.blogs.go', ['user' => $item->owner, 'blog' => $item]) }}">
							{{ __('common.link_to_message') }}
						</a>

						@can('see_technical_information', $item)
							<a class="get_user_agent pointer dropdown-item text-lowercase" href="javascript:void(0)"
							   data-link="{{ route('user_agents.show', ['model' => 'blog', 'id' => $item->id]) }}">
								{{ __('common.device_info') }}
							</a>
						@endcan

					</div>
				</div>
			</div>

		@endif

		@slot('descendants')

		@endslot

	@endcomponent


@endsection