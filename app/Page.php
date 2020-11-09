<?php

namespace App;

use App\Model as Model;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Litlife\Url\Url;

// use IgnorableObservers\IgnorableObservers;

/**
 * App\Page
 *
 * @property int $id
 * @property int $section_id
 * @property string $content
 * @property int $page
 * @property array|null $html_tags_ids Массив всех id html тегов, которые содержатся в тексте
 * @property int $book_id
 * @property int|null $character_count
 * @property int|null $book_page Номер страницы с начала книги
 * @property-read \App\Book $book
 * @property-read mixed $content_handled
 * @property-read mixed $content_handled_splited
 * @property-read \App\Section $section
 * @method static Builder|Page inLinksIdSections($array)
 * @method static Builder|Page newModelQuery()
 * @method static Builder|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static Builder|Page whereBookId($value)
 * @method static Builder|Page whereBookPage($value)
 * @method static Builder|Page whereCharacterCount($value)
 * @method static Builder|Page whereContent($value)
 * @method static Builder|Page whereHtmlTagsIds($value)
 * @method static Builder|Page whereId($value)
 * @method static Builder|Page wherePage($value)
 * @method static Builder|Page whereSectionId($value)
 * @mixin Eloquent
 */
class Page extends Model
{
	public $timestamps = false;
	public $dom;
	public $xpath;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'content'
	];

	protected $casts = [
		'html_tags_ids' => 'array'
	];

	protected $perPage = 1;

	public function section()
	{
		return $this->belongsTo('App\Section');
	}

	public function book()
	{
		return $this->belongsTo('App\Book');
	}

	public function scopeInLinksIdSections($query, $array)
	{
		$array = (array)$array;

		return $query->whereRaw('jsonb_exists_any("html_tags_ids", array[' . implode(',', array_fill(0, count($array), '?')) . '])', [$array]);
	}

	public function addHtmlId($id)
	{
		if (!empty($id)) {
			$arr = $this->html_tags_ids ?? [];
			$arr[] = $id;
			$this->html_tags_ids = $arr;
		}
	}

	public function getHtmlIds()
	{
		return $this->html_tags_ids;
	}

	public function isDomSet()
	{
		return (boolean)$this->dom;
	}

	public function xpath()
	{
		if ($this->xpath instanceof DOMXPath)
			return $this->xpath;
		else
			return $this->xpath = new DOMXPath($this->dom());
	}

	public function dom()
	{
		if ($this->dom instanceof DOMDocument)
			return $this->dom;
		else {
			return $this->dom = new DOMDocument();
		}
	}

	public function setDOM($dom)
	{
		$this->dom = $dom;
		$this->xpath = new DOMXPath($this->dom());
	}

	public function setContentAttribute($content)
	{
		$content = trim($content);

		$this->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $content);
		$this->xpath = new DOMXPath($this->dom());

		$this->attributes['content'] = $content;
	}

	public function getBodyXHTML()
	{
		$xhtml = '';

		$body = $this->dom()->getElementsByTagName('body')->item(0);

		foreach ($this->xpath->query('//*') as $node) {
			if ($node instanceof DOMElement) {
				if ($node->childNodes->length < 1) {
					if (!in_array($node->nodeName, ['area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img',
						'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'])) {
						$node->appendChild($this->dom()->createTextNode(''));
					}
				}
			}
		}

		foreach ($body->childNodes as $node) {
			$xhtml .= $this->dom()->saveXML($node);
		}

		return $xhtml;
	}

	public function getContentHandledAttribute()
	{
		$value = transform($this->content, function ($value) {

			preg_match_all('/\<a\ (?:.*)href(?:.*)\=(?:.*)\"(.*)\"(?:.*)\>/iuU', $value, $m);

			$urls = $m[1];

			foreach ($urls as $url) {
				$fragments[] = Url::fromString($url)->getFragment();
			}

			if (!empty($fragments) and isset($this->section->book_id)) {
				$pages = Page::inLinksIdSections($fragments)
					->select('id', 'section_id', 'page', 'html_tags_ids', 'book_id')
					->with('section')
					->where('book_id', $this->section->book_id)
					->get();

				$sections = Section::parametersIn('id', $fragments)
					->where('book_id', $this->section->book_id)
					->get();

				$value = preg_replace_callback('/\<a\ (.*)href(.*)\=(.*)\"(.*)\"(.*)\>/iuU', function ($m) use (&$pages, &$sections) {

					$fragment = Url::fromString($m[4])->getFragment();


					foreach ($pages as $page) {

						if (in_array($fragment, $page->html_tags_ids ?? [])) {

							if (!empty($page->section)) {
								if ($page->section->type == 'section') {
									$url = route('books.sections.show', [
										'book' => $page->book_id,
										'section' => $page->section->inner_id,
										'page' => $page->page
									]);

									$url .= $fragment ? '#' . $fragment : '';

									$str = '<a data-type="section" data-section-id="' . $page->section->inner_id . '" ' .
										$m[1] . 'href' . $m[2] . '=' . $m[3] . '"' . $url . '"' . $m[5] . '>';

									return $str;
								} elseif ($page->section->type == 'note') {
									$url = route('books.notes.show', [
										'book' => $page->book_id,
										'note' => $page->section->inner_id,
										'page' => $page->page
									]);

									$url .= $fragment ? '#' . $fragment : '';

									$str = '<a data-type="note" data-section-id="' . $page->section->inner_id . '" ' .
										$m[1] . 'href' . $m[2] . '=' . $m[3] . '"' . $url . '"' . $m[5] . '>';

									return $str;
								}
							}
						}
					}

					return '<a ' . $m[1] . 'href' . $m[2] . '=' . $m[3] . '"' . $m[4] . '"' . $m[5] . '>';

				}, $value);
			}

			return $value;
		});

		// append class img-fluid
		$value = preg_replace_callback('/\<img(.*)\>/iuU', function ($m) {
			$s = $m[1];
			if (preg_match('/(.*)class(?:[[:space:]]*)=(?:[[:space:]]*)"([^\"]*)"(.*)/iu', $s, $m)) {
				return '<img ' . $m[1] . ' class="img-fluid ' . $m[2] . '" ' . $m[3] . '>';
			} else {
				return '<img class="img-fluid" ' . $s . '>';
			}
		}, $value);

		return $value;
	}

	public function getContentHandeled()
	{
		return $this->content_handled;
	}

	public function getContentHandledSplitedAttribute()
	{
		return split_text_with_tags_on_percent($this->contentHandled, rand(30, 60));
	}

}
