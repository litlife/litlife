<div class="row mb-3">
	<div class="col-12">
		<ul class="nav nav-pills nav-justified flex-sm-row flex-column">
			<li role="presentation" class="nav-item">
				<a class="nav-link {{ isActiveRoute('home.latest_books') }}"
				   href="{{ route('home.latest_books') }}">
					{{ __('home.lastest_books') }}
				</a>
			</li>
			<li role="presentation" class="nav-item ">
				<a class="nav-link {{ isActiveRoute(['home.popular_books', 'home']) }}"
				   href="{{ route('home.popular_books', ['period' => 'week']) }}">
					{{ __('home.popular_books') }}
				</a>
			</li>
			<li role="presentation" class="nav-item ">
				<a class="nav-link {{ isActiveRoute('home.latest_comments') }}" href="{{ route('home.latest_comments') }}">
					{{ __('home.latest_comments') }}
					@if (cache('comments_count'))
						<span class="badge">{{ cache('comments_count') }}</span>
					@else

					@endif
				</a>
			</li>
			<li role="presentation" class="nav-item ">
				<a class="nav-link {{ isActiveRoute('home.latest_posts') }}" href="{{ route('home.latest_posts') }}">
					{{ __('home.latest_posts') }}

					@if (cache('posts_count'))
						<span class="badge">{{ cache('posts_count') }}</span>
					@else

					@endif
				</a>
			</li>
			<li role="presentation" class="nav-item ">
				<a class="nav-link {{ isActiveRoute('home.latest_wall_posts') }}"
				   href="{{ route('home.latest_wall_posts') }}">
					{{ __('home.latest_wall_posts') }}
				</a>
			</li>

		</ul>
	</div>
</div>