<?php

namespace App\Rules;

use ErrorException;
use Illuminate\Contracts\Validation\Rule;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

class ZipContainsBookFileRule implements Rule
{
	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct()
	{

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
			$zip = new Filesystem(new ZipArchiveAdapter((string)$file));

			$result = transform($zip->listContents(), function ($files) use ($zip) {

				$zipArchiveInstance = $zip->getAdapter()->getArchive();

				foreach ($files as $file) {

					if ($file['path'] == 'mimetype') {
						$stream = $zipArchiveInstance->getStream($file['path']);

						try {
							$contents = stream_get_contents($stream, -1, 0);

							if ($contents == 'application/epub+zip') {
								return true;
							}

						} catch (ErrorException $exception) {
							break;
						}
					}
				}
			});

			if ($result)
				return true;

			// ищем первый файл который соответствует допустимым расширениям
			$file = transform($zip->listContents(), function ($files) use ($zip) {

				$zipArchiveInstance = $zip->getAdapter()->getArchive();

				foreach ($files as $file) {

					if (!isset($file['extension']))
						break;

					$extension = $file['extension'];

					if (!in_array($extension, config('litlife.book_allowed_file_extensions')))
						break;

					$stat = $zipArchiveInstance->statName($file['path']);

					$s = $zipArchiveInstance->getStream($file['path']);

					try {
						$data = stream_get_contents($s, -1, 0);
					} catch (ErrorException $exception) {
						break;
					}

					fclose($s);

					if ($stat['crc'] != crc32($data))
						break;

					return $extension;
				}
			});

			if (!empty($file))
				return true;
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
		return __('validation.zip_book_file');
	}
}
