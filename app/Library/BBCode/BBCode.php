<?php


namespace App\Library\BBCode;

use App\Smile;
use Litlife\Url\Exceptions\InvalidArgument;
use Litlife\Url\Url;
use Xbbcode\Xbbcode;

class BBCode
{
	private $Xbbcode;
	private $text;

	function __construct()
	{
		$this->Xbbcode = new Xbbcode;
		$this->Xbbcode->setAutoLinks(true);
	}

	public function getText()
	{
		return $this->text;
	}

	public function setText($text)
	{
		$this->text = $text;
	}

	public function toHtml($text)
	{
		$this->setText($text);

		//$this->autoLinks();

		$this->text = preg_replace_callback("/\[font\=([0-9A-z\,\-\'\" ]+?)\]/iu", function ($matches) {
			return '[font="' . preg_replace('/[\"\']/iu', '', $matches[1]) . '"]';
		}, $this->text);

		$this->text = str_replace('[font=]', '[font]', $this->text);
		$this->text = str_replace('[font defaultattr=]', '[font]', $this->text);

		foreach (Smile::void()
			         //->cacheTags('ckeditor')
			         //->remember(10)
			         ->get() as $smile) {
			$simple_form = empty($smile->simple_form) ? ':' . $smile->name . ':' : $smile->simple_form;

			$smiles[$simple_form] = '<img data-src="' . $smile->url . '"' .
				' class="bb_smile lazyload" width="' . $smile->getWidth() . '" height="' . $smile->getHeight() . '" alt="' .
				htmlspecialchars($smile->description) . '" />';
		}

		$this->Xbbcode->setMnemonics($smiles);
		$this->Xbbcode->setTagHandler('spoiler', 'App\Library\Xbbcode\Tag\Spoiler');
		$this->Xbbcode->setTagHandler('quote', 'App\Library\Xbbcode\Tag\Quote');
		$this->Xbbcode->setTagHandler('list', 'App\Library\Xbbcode\Tag\Ul');
		$this->Xbbcode->setTagHandler('size', 'App\Library\Xbbcode\Tag\Size');
		$this->Xbbcode->setTagHandler('font', 'App\Library\Xbbcode\Tag\Font');
		$this->Xbbcode->setTagHandler('color', 'App\Library\Xbbcode\Tag\Color');
		$this->Xbbcode->setTagHandler('left', 'App\Library\Xbbcode\Tag\Align');
		$this->Xbbcode->setTagHandler('right', 'App\Library\Xbbcode\Tag\Align');
		$this->Xbbcode->setTagHandler('center', 'App\Library\Xbbcode\Tag\Align');
		$this->Xbbcode->setTagHandler('justify', 'App\Library\Xbbcode\Tag\Align');
		$this->Xbbcode->setTagHandler('video', 'App\Library\Xbbcode\Tag\Youtube');
		$this->Xbbcode->setTagHandler('youtube', 'App\Library\Xbbcode\Tag\Youtube');
		$this->Xbbcode->setTagHandler('img', 'App\Library\Xbbcode\Tag\Img');
		$this->Xbbcode->setTagHandler('td', 'App\Library\Xbbcode\Tag\Td');
		$this->Xbbcode->setTagHandler('th', 'App\Library\Xbbcode\Tag\Th');
		$this->Xbbcode->setTagHandler('tr', 'App\Library\Xbbcode\Tag\Tr');
		$this->Xbbcode->setTagHandler('ul', 'App\Library\Xbbcode\Tag\Ul');
		$this->Xbbcode->setTagHandler('ol', 'App\Library\Xbbcode\Tag\Ol');
		$this->Xbbcode->setTagHandler('li', 'App\Library\Xbbcode\Tag\Li');
		$this->Xbbcode->setTagHandler('table', 'App\Library\Xbbcode\Tag\Table');
		$this->Xbbcode->setTagHandler('code', 'App\Library\Xbbcode\Tag\Code');
		$this->Xbbcode->setTagHandler('hr', 'App\Library\Xbbcode\Tag\Hr');
		$this->Xbbcode->setTagHandler('a', 'App\Library\Xbbcode\Tag\A');
		$this->Xbbcode->setTagHandler('url', 'App\Library\Xbbcode\Tag\A');
		$this->Xbbcode->parse($this->text);

		$this->text = @$this->Xbbcode->getHtml();

		$this->text = preg_replace_callback('/\<a(.+?)\>(.*?)\<\/a\>/iu', [$this, 'handleUrls'], $this->text);

		$this->text = $this->tidy($this->text);
		//$text = str_replace('&#160;', ' ', $text);
		/*
				$configuration = [
					'Attr.EnableID' => true,
					'AutoFormat.AutoParagraph' => false,
					'Attr.AllowedClasses' => 'bb,lazyload',
					'HTML.FlashAllowFullScreen' => true,
					'HTML.TidyAdd' => 'heavy',
					'HTML.Allowed' =>
						'*[class|style],strong,i,u,del,sub,sup,div,span,blockquote,hr,a[href],ul,ol,iframe[frameborder|width|height|src],img[src]'
				];

				$this->text = Purify::clean($this->text, $configuration);
		*/
		return $this->text;
	}

	public function tidy($html)
	{
		$tidy = new \tidy();

		$config = [
			'clean' => false,
			'merge-divs' => true,
			'merge-spans' => true,
			'output-xhtml' => true,
			'drop-empty-paras' => false,
			'join-styles' => true,
			'join-classes' => true,
			'repeated-attributes' => 'keep-last',
			'wrap-attributes' => false,
			'wrap' => 0,
			'anchor-as-name' => true,
			'indent-attributes' => false,
			'indent' => false
		];

		$html = $tidy->repairString($html, $config);
		$html = $tidy->parseString($html, $config);

		$html = trim($tidy->Body()->value);
		$html = ltrim($html, '<body>');
		$html = rtrim($html, '</body>');
		$html = trim($html);

		$html = str_replace("\n", '', $html);

		return $html;
	}

	/*
		public function autoLinks()
		{
			$this->text = preg_replace_callback('#(?xi)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#iu', function ($matches) {

				$match = $matches[1];

				$url = Url::fromString($match);

				if (empty($url->getScheme()))
					$url = $url->withScheme('http');

				return '[url='.$url.']'.$match.'[/url]';

			}, $this->text);
		}
		*/

	public function handleUrls($array)
	{
		list ($string, $attributes, $text) = $array;

		if (preg_match('/(.*)href\=\"(.+?)\"(.*)/iu', $attributes, $matches)) {
			try {
				$url = @Url::fromString($matches[2]);
			} catch (InvalidArgument $exception) {
				return $text;
			}

			$app_host = parse_url(config('app.url'), PHP_URL_HOST);

			if ($url and $url->getHost()) {
				if (!in_array(mb_strtolower($url->getExtension()), ['jpg', 'jpeg', 'png', 'gif'])) {
					if ($url->getHost() != $app_host and $url->getHost() != 'www.' . $app_host) {

						$away_url = @Url::fromString('/away')
							->withQueryParameter('url', $matches[2]);

						$attributes = $matches[1] . 'href="' . $away_url . '"' . $matches[3] . '';

						if (!preg_match('/target\=\"\_blank\"/iu', $attributes)) {
							$attributes .= ' target="_blank"';
						}
					} else {
						$attributes = preg_replace('/ target\=\"\_blank\"/iu', '', $attributes);
					}
				}
			}
		}

		if (preg_match('/(.*)class\=\"(.+?)\"(.*)/iu', $attributes, $matches)) {

			if (!preg_match('/bb/iu', $matches[2])) {
				$attributes = $matches[1] . 'class="bb"' . $matches[3];
			}
		} else {
			$attributes = ' class="bb"' . $attributes;
		}

		return '<a' . $attributes . '>' . $text . '</a>';
	}
}
