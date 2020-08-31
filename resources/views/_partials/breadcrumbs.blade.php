<div class="breadcrumb-scroll rounded-bottom">
	<div class="breadcrumb" @if (count($breadcrumbs)) itemscope itemtype="http://schema.org/BreadcrumbList" @endif>
		@if (count($breadcrumbs))
			<meta itemprop="numberOfItems" content="{{ count($breadcrumbs) }}"/>
			@foreach ($breadcrumbs as $number => $breadcrumb)
				@if ($breadcrumb->url && !$loop->last)
					<div class="breadcrumb-item d-inline-block" itemprop="itemListElement" itemscope
						 itemtype="http://schema.org/ListItem">
						<a itemprop="item" href="{{ $breadcrumb->url }}" class="d-inline-block">
							<h2 itemprop="name" class="h6 d-inline-block font-weight-normal text-truncate mb-0"
								style="max-width: 400px;">
								{{ $breadcrumb->title }}
							</h2>
						</a>
						<meta itemprop="position" content="{{ $number + 1 }}"/>
					</div>
				@else
					<div class="active breadcrumb-item d-inline-block" itemprop="itemListElement" itemscope
						 itemtype="http://schema.org/ListItem">

						<a itemprop="item" href="{{ $breadcrumb->url }}" class="d-inline-block">
							<h2 itemprop="name" class="h6 d-inline-block font-weight-normal text-truncate mb-0"
								style="max-width: 400px;">
								{{ $breadcrumb->title }}
							</h2>
						</a>
						<meta itemprop="position" content="{{ $number + 1 }}"/>
					</div>
				@endif
			@endforeach
		@endif
	</div>
</div>



