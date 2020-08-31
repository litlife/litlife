<?php

namespace App\Jobs;

use App\Post;
use DOMDocument;
use Exception;
use Illuminate\Foundation\Bus\Dispatchable;
use Intervention\Image\Facades\Image;

class PostImageDimensioning
{
	use Dispatchable;

	protected $post;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(Post $post)
	{
		$this->post = $post;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */

	public function handle()
	{
		if ($this->post->image_size_defined) return;

		libxml_use_internal_errors(true);

		$dom = new DOMDocument();
		$dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $this->post->html_text);

		$nodes = $dom->getElementsByTagName('img');

		if (!empty($nodes)) {
			foreach ($nodes as $node) {
				$this->node($node);
			}
		}

		$body = $dom->getElementsByTagName('body')->item(0);

		$text = null;

		if (isset($body->childNodes)) {
			foreach ($body->childNodes as $childNode) {
				$text .= $dom->saveHTML($childNode);
			}
		}

		$this->post->html_text = $text;
		$this->post->image_size_defined = true;
		$this->post->save();
	}

	private function node(&$node)
	{
		if ($node->hasAttribute('src')) {
			$src = $node->getAttribute('src');
		}

		if (empty($src))
			return;

		if ($node->hasAttribute('width'))
			$width = $node->getAttribute('width');

		if ($node->hasAttribute('height'))
			$height = $node->getAttribute('height');

		if ($node->hasAttribute('style')) {
			$style = $node->getAttribute('style');

			$results = [];
			preg_match_all("/([\w-]+)\s*:\s*([^;]+)\s*;?/", $style, $matches, PREG_SET_ORDER);
			foreach ($matches as $match) {
				$results[$match[1]] = $match[2];
			}

			if (!empty($results['width']))
				$width = $results['width'];

			if (!empty($results['height']))
				$height = $results['height'];
		}

		if (!empty($width) or !empty($height))
			return;

		try {
			$img = Image::make($src);
			$width = $img->width();
			$height = $img->height();
		} catch (Exception $exception) {

		}

		if (empty($width) and empty($height))
			return;

		$node->setAttribute('width', $width);
		$node->setAttribute('height', $height);

		echo($this->post->id . ' ' . $src . ' ' . $width . ' ' . $height . " \n");
	}
}
