<button class="btn btn-light share" data-toggle="tooltip"
		data-title="{{ e($item->getShareTitle()) }}" data-description="{{ e($item->getShareDescription()) }}"
		data-url="{{ $item->getShareUrl() }}"
		data-placement="top" title="{{ $item->getShareTooltip() }}">
	<i class="far fa-share-square"></i>
</button>