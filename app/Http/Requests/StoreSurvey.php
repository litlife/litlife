<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurvey extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'do_you_read_books_or_download_them' => 'string|nullable',
			'what_file_formats_do_you_download' => 'array|nullable',
			'how_improve_download_book_files' => 'string|nullable',
			'how_do_you_rate_the_convenience_of_reading_books_online' => 'string|nullable',
			'how_to_improve_the_convenience_of_reading_books_online' => 'string|nullable',
			'how_do_you_rate_the_convenience_and_functionality_of_search' => 'string|nullable',
			'how_to_improve_the_convenience_of_searching_for_books' => 'string|nullable',
			'how_do_you_rate_the_site_design' => 'string|nullable',
			'how_to_improve_the_site_design' => 'string|nullable',
			'how_do_you_assess_the_work_of_the_site_administration' => 'string|nullable',
			'how_improve_the_site_administration' => 'string|nullable',
			'what_do_you_like_on_the_site' => 'string|nullable',
			'what_you_dont_like_about_the_site' => 'string|nullable',
			'what_you_need_on_our_site' => 'string|nullable',
			'what_site_features_are_interesting_to_you' => 'array|nullable'
		];
	}


	public function attributes()
	{
		return __('survey');
	}
}
