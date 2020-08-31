@extends('layouts.app')

@section('content')

	@foreach ($versions as $version)

		<div class="card">
			<div class="card-body">

				<a href="{{ route('text_blocks.show', ['name' => $version->name, 'id' => $version->id]) }}">Версия {{ $version->id }}</a>

				Редактировал:
				<x-user-name :user="$version->create_user"/>

				<x-time :time="$version->created_at"/>

			</div>
		</div>

	@endforeach

@endsection
