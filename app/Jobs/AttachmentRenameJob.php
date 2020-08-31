<?php

namespace App\Jobs;

use App\Attachment;
use App\Book;
use App\Section;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Litlife\Url\Url;

class AttachmentRenameJob
{
	use Dispatchable;

	protected $book;
	protected $attachment;
	protected $name;
	protected $sections;

	/**
	 * Create a new job instance.
	 *
	 * @param Book $book
	 * @param Attachment $attachment
	 * @param string $name
	 * @return void
	 */
	public function __construct(Book $book, Attachment $attachment, $name)
	{
		$this->book = $book;
		$this->attachment = $attachment;
		$this->name = $name;
		$this->oldName = $this->attachment->name;
		$this->sections = $this->book->sections()->get();
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		DB::transaction(function () {

			foreach ($this->sections as $section) {
				$this->replaceImageUrlsInSections($section);
			}

			$this->attachment->rename($this->name);
			$this->attachment->save();

			foreach ($this->sections as $section) {
				$section->save();
			}
		});
	}

	/**
	 * Заменить ссылки в текстах глав
	 *
	 * @param Section $section
	 * @return void
	 */
	private function replaceImageUrlsInSections(Section &$section)
	{
		$changed = false;

		$section->content = $section->getContentHandeled();
		$section->contentChanged = false;

		$body = $section->dom()->getElementsByTagName('body')->item(0);

		$nodeList = $section->xpath()->query("//img[@src]", $body);

		$attachment_url = Url::fromString($this->attachment->url);

		if ($nodeList->length) {

			foreach ($nodeList as $node) {

				$src = Url::fromString($node->getAttribute("src"));

				if (
					($src->getHost() == $attachment_url->getHost()) and
					($src->getDirname() == $attachment_url->getDirname()) and
					($src->getBasename() == $attachment_url->getBasename())
				) {
					$node->setAttribute('src', $attachment_url->withBasename($this->name));
					$node->setAttribute('alt', $this->name);
					$changed = true;
				}
			}
		}

		$content = '';

		foreach ($body->childNodes as $node) {
			$content .= $section->dom()->saveXML($node);
		}

		if ($changed)
			$section->content = $content;
	}
}
