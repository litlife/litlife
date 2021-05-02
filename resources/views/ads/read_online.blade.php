<div id="read_online_ad" class="collapse show float-right col-md-6 col-lg-5 mb-2 pl-md-5 pr-md-0 text-right">

	<div class="text-center mb-2">

		<button type="button" data-toggle="collapse" data-target="#read_online_ad" aria-expanded="false"
				aria-controls="read_online_ad" class="btn btn-light btn-sm">
			{{ __('Close') }}
		</button>

		<button id="removeAdsButton" type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#removeAds">
			{{ __('How to disable ads?') }}
		</button>

	</div>

	<x-ad-block name="read_online"/>
</div>