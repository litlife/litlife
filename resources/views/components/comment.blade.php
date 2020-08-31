@php
	if (empty($level)) $level = 0;
@endphp
<div @isset($item) data-id="{{ $item->id }}" @endisset {{ $data_attributes ?? '' }} data-level="{{ $level }}"
	 class="item @if ($level < 1) card mb-2 @else col-12 @endif ">
	@if ($level < 1)
		<div class="card-body"> @endif
			<div class="row mt-0">
				<div data-self
					 class="component-comment col-12 d-lg-flex d-md-flex flex-sm-row @if ($level > 0) border-left shadow-top p-3  @endif {{ $block_css ?? '' }}">

					{{ $anchor ?? '' }}

					<div class="component-comment-left">
						<div class="mb-md-3 mb-sm-3 mb-3 text-md-center mr-3">
							{{ $avatar }}
						</div>
					</div>

					<div class="component-comment-right flex-grow-1">
						{{ $slot }}
					</div>
				</div>
				@isset($descendants)
					<div class="descendants w-100 @if ($level < 3) pl-xl-5 pl-lg-5 pl-md-5 pl-sm-4 pl-3 @endif @if (0 < $level and $level < 3) border-left-dashed @endif">
						{{ $descendants }}
					</div>
				@endisset
			</div>
			@if ($level < 1)
		</div>
	@endif
</div>
