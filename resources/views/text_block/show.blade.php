@extends('layouts.app')

@section('content')

	@include('text_block.item', ['name' => $textBlock->name ?? ''])

@endsection