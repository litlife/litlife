<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use ZipArchive;

class ZipRule implements Rule
{
	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Determine if the validation rule passes.
	 *
	 * @param string $attribute
	 * @param mixed $file
	 * @return bool
	 */
	public function passes($attribute, $file)
	{
		if ($file->getMimeType() == 'application/zip') {
			$zip = new ZipArchive;
			$res = $zip->open($file, ZipArchive::CHECKCONS);
			if ($res === TRUE) {
				$zip->close();
				return true;
			} else {
				return false;
			}
		}

		return false;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return __('validation.zip');
	}
}
