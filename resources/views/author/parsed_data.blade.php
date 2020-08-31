@foreach ($datas as $data)
	@if ($data->isEmailVaild()) {{ $data->email }} {{ $data->getAbsoluteRating() }} {{ $data->name }}
	@endif
@endforeach

