<div class="d-flex">
	<div class="w-100">
		<div class="d-flex w-100 justify-content-between">
			<h6 class="mb-1">{{ $collection->title }}</h6>
		</div>
		<p class="mb-1">
			{{ $collection->description }}
		</p>
	</div>
	<input id="collection_id" type="hidden" name="collection_id" value="{{ $collection->id }}"/>
</div>