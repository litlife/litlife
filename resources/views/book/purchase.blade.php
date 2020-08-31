@extends('layouts.app')

@push('css')

@endpush

@push('scripts')

@endpush

@section('content')

	@if (count($errors) > 0)
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@if (session('success'))
		<div class="alert alert-success">
			{{ session('success') }}
		</div>
	@endif

	@if (session('info'))
		<div class="alert alert-info">
			{{ session('info') }}
		</div>
	@endif

	<div class="card mb-2">
		<div class="card-body">

			@if (auth()->user()->balance >= $book->price)
				<a class="btn btn-primary" href="{{ route('books.buy', ['book' => $book]) }}">

					Оплатить {{ $book->price }} р. из кошелька на сайте
				</a>

				<hr/>
			@endif

			Вы можете оплатить книгу через платежную систему, которая вам будет удобнее:
			<form class="" role="form" action="{{ route('books.buy.deposit', ['book' => $book]) }}"
				  method="post" enctype="multipart/form-data">

				@csrf

				<input id="sum" name="sum" type="hidden" disabled="disabled" value="{{ $book->price }}"/>

				<fieldset class="form-group">
					@foreach (config('unitpay.allowed_payment_types') as $type)

						@if ($type == 'mc')
							@foreach (config('unitpay.allowed_mobile_payment_types') as $type)
								<div class="form-check">
									<input class="form-check-input" type="radio" name="payment_type" id="gridRadios{{ $type }}"
										   value="mc">
									<label class="form-check-label" for="gridRadios{{ $type }}">
										{{ __('user_payment_transaction.payment_types_array.'.$type) }}

										~ {{ config('unitpay.deposit_comissions.'.$type) }}%
									</label>
								</div>
							@endforeach
						@else
							<div class="form-check">
								<input class="form-check-input" type="radio" name="payment_type" id="gridRadios{{ $type }}"
									   value="{{ $type }}">
								<label class="form-check-label" for="gridRadios{{ $type }}">
									{{ __('user_payment_transaction.payment_types_array.'.$type) }}

									~ {{ config('unitpay.deposit_comissions.'.$type) }}%
								</label>
							</div>
						@endif

					@endforeach
				</fieldset>

				<button type="submit" class="btn btn-primary">{{ __('user_payment_transaction.go_to_pay') }}</button>

			</form>
			<br/>
			{!! __('user_purchases.buying_a_book_you_agree_to_the_rules_of_buying_books') !!}
		</div>
	</div>

@endsection