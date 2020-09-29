@extends('section.view')

@section ('text')

	<div @if ($section->getSectionId()) id="{{ $section->getSectionId() }}"
		 @endif class="book_text @if ($book->copy_protection) noselect @endif">

		@if ($pages->currentPage() < 2)
			<h4 @if ($section->getTitleId()) id="{{ $section->getTitleId() }}" @endif class="mb-4 font-weight-bold">
				{{ $section->title }}</h4>
		@endif

		@foreach ($pages as $page)
			@php ($array = $page->content_handled_splited)

			{!! $array['before'] !!}

			@can ('see_ads', \App\User::class)
				@can('display_ads', $page)
					<x-ad-block name="read_online"/>
				@endcan
			@endcan

			{!! $array['after'] !!}
		@endforeach
	</div>

@endsection

