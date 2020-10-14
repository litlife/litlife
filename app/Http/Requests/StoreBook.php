<?php

namespace App\Http\Requests;

use App\Book;
use App\Enums\BookComplete;
use BenSampo\Enum\Rules\EnumKey;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBook extends FormRequest
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
		$book = new Book;

		return [
			'title' => 'required|min:2|max:255',
			'genres' => 'array|min:1|required',
			'writers' => 'array|min:1|required',
			'ti_lb' => 'required|exists:languages,code',
			'ti_olb' => 'required|exists:languages,code',
			'year_writing' => 'sometimes|nullable|digits_between:0,4',
			'year_public' => 'sometimes|nullable|digits_between:0,4',
			'pi_year' => 'sometimes|nullable|digits_between:0,4',
			'ready_status' => ['required', new EnumKey(BookComplete::class)],
			'swear' => 'in:' . implode(',', $book->swearArray) . '',
			'translators' => 'sometimes|array|nullable',
			'sequences' => 'sometimes|array|nullable',
			'age' => 'nullable|sometimes|digits_between:0,18',
			'is_si' => 'sometimes|boolean',
			'is_lp' => 'sometimes|boolean',
			'is_collection' => 'sometimes|boolean',
			'images_exists' => 'sometimes|boolean',
			'is_public' => 'sometimes|boolean',
			'keywords' => 'sometimes|array|nullable'
		];
	}

	/**
	 * Configure the validator instance.
	 *
	 * @param Validator $validator
	 * @return void
	 */
	public function withValidator($validator)
	{
		$validator->after(function ($validator) {
			if ($this->isSiLabelIsTrueAndPublishFieldsIsNotEmpty()) {
				$validator->errors()->add('is_si', __('book.if_the_book_is_marked_as_samizdat_then_the_fields_publisher_city_of_printing_year_of_printing_isbn_must_be_empty'));
			}
		});

		$validator->after(function ($validator) {
			if ($this->isSiAndLp()) {
				$validator->errors()->add('is_si', __('book.set_the_label_either_samizdat_or_amateur_translation'));
			}
		});

		$validator->after(function ($validator) {
			if ($this->isNotSiAndNotLpAndPublicationDetailsAreEmpty()) {
				$validator->errors()
					->add('pi_pub', __('If the book was published, please fill in the details (name of the publisher, year of publication, ISBN).'))
					->add('is_si', __('If the book was not published, please set the label Samizdat.'))
					->add('is_lp', __('If the book is an Amateur translation, please mark it as an Amateur translation.'));
			}
		});
	}

	public function isSiLabelIsTrueAndPublishFieldsIsNotEmpty(): bool
	{
		if ($this->is_si) {
			if (trim($this->pi_pub) != '' or
				trim($this->pi_city) != '' or
				trim($this->pi_year) != '' or
				trim($this->pi_isbn) != ''
			)
				return true;
		}

		return false;
	}

	public function isSiAndLp(): bool
	{
		if ($this->is_si and $this->is_lp)
			return true;
		else
			return false;
	}

	public function isPublicationDetailsAreEmpty(): bool
	{
		if (trim($this->pi_pub) == '' and
			trim($this->pi_city) == '' and
			trim($this->pi_year) == '' and
			trim($this->pi_isbn) == ''
		)
			return true;
		else
			return false;
	}

	public function isNotSiAndNotLpAndPublicationDetailsAreEmpty(): bool
	{
		if ($this->is_si)
			return false;

		if ($this->is_lp)
			return false;

		if (!$this->isPublicationDetailsAreEmpty())
			return false;

		return true;
	}

	public function attributes()
	{
		return __('book');
	}
}
