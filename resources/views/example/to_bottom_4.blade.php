@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/example.scroll_to_bottom.js', config('litlife.assets_path')) }}"></script>
@endpush

@push ('css')

	<style type="text/css">

		.example-back-to-top {
			cursor: pointer;
			position: fixed;
			bottom: 68px;
			right: 18px;
			display: none;
			z-index: 1000;
			opacity: 0.7;
		}

		.example-back-to-top:hover {
			opacity: 1;
		}

		.example-to-bottom {
			cursor: pointer;
			position: fixed;
			bottom: 19px;
			right: 18px;
			display: none;
			z-index: 1000;
			opacity: 0.7;
		}

		.example-to-bottom:hover {
			opacity: 1;
		}
	</style>
@endpush

@section('content')

	<div class="card">
		<div class="card-body" style="height:2000px">

			<a id="examle-back-to-top" href="#" class="example-back-to-top btn btn-primary btn-lg" role="button"
			   title="{{ __('common.back_to_top') }}"
			   data-toggle="tooltip" data-placement="left">
				<i class="fas fa-caret-up"></i>
			</a>

			<a id="examle-to-bottom" href="#" class="example-to-bottom btn btn-primary btn-lg" role="button"
			   title="Нажмите, чтобы перейти в низ экрана"
			   data-toggle="tooltip" data-placement="left">
				<i class="fas fa-caret-down"></i>
			</a>

		</div>
	</div>



@endsection
