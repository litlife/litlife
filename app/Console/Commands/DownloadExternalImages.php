<?php

namespace App\Console\Commands;

use App\Image;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\Relation;
use Imagick;
use ImagickException;
use Litlife\Url\Url;
use Spatie\Url\Exceptions\InvalidArgument;

class DownloadExternalImages extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'images:download_external {model} {id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Скачать все не локальные картинки';
	protected $model;
	protected $column_name;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();


	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->info("start");

		Imagick::setResourceLimit(Imagick::RESOURCETYPE_MEMORY, 256);
		Imagick::setResourceLimit(Imagick::RESOURCETYPE_MAP, 256);

		$model = $this->argument('model');
		$id = $this->argument('id');

		$map = Relation::morphMap();

		switch ($model) {
			default:
				$this->column_name = 'bb_text';
				break;
		}

		if (isset($map[$model])) {
			$className = $map[$model];

			$this->model = $className::any()->findOrFail($id);

			$text = $this->model->{$this->column_name};

			$text = preg_replace_callback('/\[img(?:\=([0-9]+)x([0-9]+))?\](.+?)\[\/img\]/iu', [$this, 'tagImg'], $text);

			$this->model->{$this->column_name} = $text;
			$this->model->external_images_downloaded = true;
			$this->model->save();
		}
	}

	private function tagImg($array)
	{
		list ($string, $width, $height, $url) = $array;

		$url = trim($url);

		$this->info('Image: ' . mb_substr($url, 0, 200) . ' width:' . $width . ' height: ' . $height);

		if (empty($url))
			return $string;

		// проверяем, если изображение закодировано в base64
		if (preg_match('/^data\:image\/(?:[A-z]+);base64,(.*)/iu', $url, $matches)) {
			list (, $base64) = $matches;

			try {
				$image = new Image;
				$image->openImage($base64, 'base64');
			} catch (ImagickException $exception) {
				$this->info("Invalid base 64 image contents");
				return false;
			}

			$this->info('Valid image from base64 ');
		} else {

			// проверяем, нужно ли изображение скачивать или оно находится на серверах сайта

			try {
				$host = Url::fromString($url)->getHost();
			} catch (InvalidArgument $exception) {

				$this->info("Wrong url $url");
			}

			if ((!empty($host))
				and
				(!in_array($host, config('litlife.site_hosts')))) {

				$this->info("The external image with url " . $url);

				try {
					$image = new Image;
					$image->openImage($url, 'url');
				} catch (ClientException $exception) {
					$this->info("Can't download image cause error: " . $exception->getResponse()->getStatusCode() . " " . $exception->getResponse()->getReasonPhrase());
					return $string;
				} catch (ConnectException $exception) {
					$this->info("Can't download image cause error: " . $exception->getMessage());
					return $string;
				} catch (RequestException $exception) {
					$this->info("Can't download image cause request exception: " . $exception->getMessage());
					return $string;
				} catch (ImagickException $exception) {
					$this->info("Failed open image " . $exception->getMessage());
					return $string;
				} catch (Exception $exception) {
					$this->info("Failed open image: " . $exception->getMessage());
					return $string;
				}
			}
		}

		if (!empty($image)) {
			if (!in_array(mb_strtolower($image->getImagick()->getImageFormat()), ['jpeg', 'jpg', 'png', 'gif'])) {
				$this->info('Unsupport format ' . $image->getImagick()->getImageFormat());
				return false;
			}

			$create_user = $this->model->create_user()->any()->first();

			if (!empty($create_user))
				$image_exists = $create_user->images()
					->sha256Hash($image->getImagick()->getImageSignature())
					->first();
			else
				$image_exists = Image::sha256Hash($image->getImagick()->getImageSignature())
					->first();

			if (!empty($image_exists)) {
				$image = $image_exists;
			} else {
				$this->info("Add Image ");

				if (!empty($this->model->sender_id))
					$image->create_user_id = $this->model->sender_id;
				else
					$image->create_user_id = $this->model->create_user_id;

				$image->storage = config('filesystems.default');
				$image->created_at = $this->model->created_at;
				$image->save();

				$this->info("Image url " . $image->url . "");
			}

			if (($width) and ($height))
				return '[img=' . $width . 'x' . $height . ']' . $image->url . '[/img]';
			else {
				return '[img]' . $image->url . '[/img]';
			}
		}

		return $string;
	}

}
