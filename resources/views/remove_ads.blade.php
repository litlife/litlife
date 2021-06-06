@extends('layouts.app')

@section('text')

	<h5>{{ __('How to disable ads?') }}</h5>
	<hr/>
	<p>У вас есть возможность отключить все рекламные блоки на ЛитЛайфе. Для это нужно купить любую понравившуюся книгу у наших писателей.</p>
	<p>Мы отключим для вас рекламу на срок, который будет равен цене покупки.
		К примеру: если вы купили книгу на сумму 42 рубля, то рекламные объявления не будут отображаться в течение 42 дней.
		Количество дней суммируется. Далее пример: вы купили еще одну книгу на сумму 58 рублей, значит реклама будет отключена еще на 58 дней, то есть в
		сумме на 100 дней = (42 + 58) и т. д.
	</p>
	<p>Покупая книгу вы поддержите труд наших писателей, а так же наш сайт, так как мы получаем от 10% до 30% от суммы проданной книги.</p>

	@isset (\Illuminate\Support\Facades\Auth::user()->data->ads_disabled_until)
		<p>Мы отключили для вас показ рекламных блоков до
			{{ optional(\Illuminate\Support\Facades\Auth::user()->data->ads_disabled_until)->diffForHumans() }}</p>
	@endisset

	<p class="font-weight-bold">На страницах книги, которую вы купили, реклама никогда не показывается и не будет.</p>

	<hr/>

	<div class="text-center">
		<a href="{{ route('books', ['paid_access' => 'paid_only', 'order' => 'rating_month_desc']) }}" class="btn btn-primary" target="_blank">
			Просмотреть список лучших книг за этот месяц
		</a>
	</div>

@endsection

@section('content')

	<div class="card">
		<div class="card-body">
			@yield('text')
		</div>
	</div>

@endsection