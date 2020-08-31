<div class="item card" data-id="{{ $achievement->id }}">
	@if (!empty($achievement->image))
		<img class="card-img-top lazyload"
			 data-src="{{ $achievement->image->fullUrlMaxSize(350, 350) }}" alt="">
	@endif
	<div class="card-body">
		<h5 class="card-title">
			<a href="{{ route('achievements.show', compact('achievement')) }}">
				{{ $achievement->title }}
			</a>
		</h5>
		<p class="card-text">{{ $achievement->description }}</p>

		<div class="btn-group dropdown">
			<button class="btn btn-light dropdown-toggle" type="button" id="achievement_{{ $achievement->id }}"
					data-toggle="dropdown"
					aria-haspopup="true"
					aria-expanded="false">
				<i class="fas fa-ellipsis-h"></i>
			</button>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="achievement_{{ $achievement->id }}">
				<a class="delete text-lowercase dropdown-item pointer" disabled="disabled"
				   data-loading-text="{{ __('common.deleting') }}..."
				   @cannot ('delete', $achievement) style="display:none;"@endcannot>
					{{ __('common.delete') }}
				</a>

				<a class="restore text-lowercase dropdown-item pointer" disabled="disabled"
				   data-loading-text="{{ __('common.restoring') }}"
				   @cannot ('restore', $achievement) style="display:none;"@endcannot>
					{{ __('common.restore') }}
				</a>

				@can ('update', $achievement)
					<a class="dropdown-item text-lowercase" href="{{ route('achievements.edit', $achievement) }}">
						{{ __('common.edit') }}
					</a>
				@endcan

			</div>
		</div>
	</div>
	@if (!empty($achievement->created_at))
		<div class="card-footer">
			<small
					class="text-muted">{{ __('achievement.created_at') }}
				<x-time :time="$achievement->created_at"/>
			</small>
		</div>
	@endif
</div>
