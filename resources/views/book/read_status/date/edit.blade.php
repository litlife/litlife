<div class="errors">

</div>

<form action="{{ route('books.read_status.date.update', ['book' => $book]) }}" method="post">

	@csrf
	@method('patch')

	<div class="d-flex flex-row mb-1">
		<label class="col-form-label col-3 text-truncate">
			{{ __('Year') }}
		</label>

		<select name="year" class="form-control">
			@foreach (range(1900, now()->year) as $year)
				<option value="{{ $year }}" @if ($user_updated_at->year == $year) selected @endif>
					{{ $year }}
				</option>
			@endforeach
		</select>
	</div>

	<div class="d-flex flex-row mb-1">
		<label class="col-form-label col-3 text-truncate">
			{{ __('Month') }}
		</label>

		<select name="month" class="form-control">
			@foreach (range(1, 12) as $month)
				<option value="{{ $month }}"
						@if ($user_updated_at->month == $month) selected @endif>
					{{ __("date.month.".$month) }}
				</option>
			@endforeach
		</select>
	</div>

	<div class="d-flex flex-row mb-1">
		<label class="col-form-label col-3 text-truncate">
			{{ __('Day') }}
		</label>

		<select name="day" class="form-control">
			@foreach (range(1, 31) as $day)
				<option value="{{ $day }}"
						@if ($user_updated_at->day == $day) selected @endif>{{ $day }}</option>
			@endforeach
		</select>

	</div>

	<div class="d-flex flex-row mb-1">
		<label class="col-form-label col-3 text-truncate">
			{{ __('Hour') }}
		</label>
		<select name="hour" class="form-control">
			@foreach (range(0, 23) as $hour)
				<option value="{{ $hour }}"
						@if ($user_updated_at->hour == $hour) selected @endif>{{ $hour }}</option>
			@endforeach
		</select>

	</div>

	<div class="d-flex flex-row mb-1">
		<label class="col-form-label col-3 text-truncate">
			{{ __('Minute') }}
		</label>

		<select name="minute" class="form-control">
			@foreach (range(0, 59) as $minute)
				<option value="{{ $minute }}"
						@if ($user_updated_at->minute == $minute) selected @endif>{{ $minute }}</option>
			@endforeach
		</select>
	</div>

	<div class="d-flex flex-row mb-1">
		<label class="col-form-label col-3 text-truncate">
			{{ __('Second') }}
		</label>

		<select name="second" class="form-control">
			@foreach (range(0, 59) as $second)
				<option value="{{ $second }}"
						@if ($user_updated_at->second == $second) selected @endif>{{ $second }}</option>
			@endforeach
		</select>
	</div>

	<div class="mt-3">
		<button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
	</div>

</form>