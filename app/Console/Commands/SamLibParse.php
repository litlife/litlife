<?php

namespace App\Console\Commands;

use App\AuthorParsedData;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Litlife\Url\Url;
use Symfony\Component\DomCrawler\Crawler;

class SamLibParse extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'parse:samlib';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Спарсить данные об авторах с других сайтов';

	protected $indexHtml;
	protected $baseUrl;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->indexHtml = '<a href="/a/">А</a> 
<a href="/b/">Б</a> 
<a href="/w/">В</a> 
<a href="/g/">Г</a> 
<a href="/d/">Д</a> 
<a href="/e/">Е</a> 
<a href="/e/index_yo.shtml">Ё</a> 
<a href="/z/index_zh.shtml">Ж</a> 
<a href="/z/">З</a> 
<a href="/i/">И</a> 
<a href="/j/index_ij.shtml">Й</a> 
<a href="/k/">К</a> 
<a href="/l/">Л</a> 
<a href="/m/">М</a> 
<a href="/n/">Н</a> 
<a href="/o/">О</a> 
<a href="/p/">П</a> 
<a href="/r/">Р</a> 
<a href="/s/">С</a> 
<a href="/t/">Т</a> 
<a href="/u/">У</a> 
<a href="/f/">Ф</a> 
<a href="/h/">Х</a> 
<a href="/c/">Ц</a> 
<a href="/c/index_ch.shtml">Ч</a> 
<a href="/s/index_sh.shtml">Ш</a> 
<a href="/s/index_sw.shtml">Щ</a> 
<a href="/x/">Ъ</a> 
<a href="/y/">Ы</a> 
<a href="/x/">Ь</a> 
<a href="/e/index_ae.shtml">Э</a> 
<a href="/j/index_ju.shtml">Ю</a> 
<a href="/j/index_ja.shtml">Я</a> 
<a href="/0/index_0.shtml">0</a> 
<a href="/1/index_1.shtml">1</a> 
<a href="/1/index_1.shtml">1</a> 
<a href="/2/index_2.shtml">2</a> 
<a href="/3/index_3.shtml">3</a> 
<a href="/4/index_4.shtml">4</a> 
<a href="/5/index_5.shtml">5</a> 
<a href="/6/index_6.shtml">6</a> 
<a href="/7/index_7.shtml">7</a> 
<a href="/8/index_8.shtml">8</a> 
<a href="/9/index_9.shtml">9</a> 
<a href="/a/index_a.shtml">A</a> 
<a href="/b/index_b.shtml">B</a> 
<a href="/c/index_c.shtml">C</a> 
<a href="/d/index_d.shtml">D</a> 
<a href="/e/index_e.shtml">E</a> 
<a href="/f/index_f.shtml">F</a> 
<a href="/g/index_g.shtml">G</a> 
<a href="/h/index_h.shtml">H</a> 
<a href="/i/index_i.shtml">I</a> 
<a href="/j/index_j.shtml">J</a> 
<a href="/k/index_k.shtml">K</a> 
<a href="/l/index_l.shtml">L</a> 
<a href="/m/index_m.shtml">M</a> 
<a href="/n/index_n.shtml">N</a> 
<a href="/o/index_o.shtml">O</a> 
<a href="/p/index_p.shtml">P</a> 
<a href="/q/index_q.shtml">Q</a> 
<a href="/r/index_r.shtml">R</a> 
<a href="/s/index_s.shtml">S</a> 
<a href="/t/index_t.shtml">T</a> 
<a href="/u/index_u.shtml">U</a> 
<a href="/v/index_v.shtml">V</a> 
<a href="/w/index_w.shtml">W</a> 
<a href="/x/index_x.shtml">X</a> 
<a href="/y/index_y.shtml">Y</a> 
<a href="/z/index_z.shtml">Z</a>';

		$this->baseUrl = 'http://samlib.ru';
	}

	public function setIndexHtml($html)
	{
		$this->indexHtml = $html;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		foreach ($this->getUrlsFromHtml($this->indexHtml) as $url) {
			$this->parseAuthorsListPage($url);
		}
	}

	public function getUrlsFromHtml($html): array
	{
		$crawler = new Crawler($html, $this->baseUrl);

		$array = [];

		foreach ($crawler->filter('a[href]')->links() as $link) {
			$array[] = $link->getUri();
		}

		return $array;
	}

	public function parseAuthorsListPage($url)
	{
		echo('Парсим список авторов ' . $url . ' ' . " \n");

		$html = $this->getUrlHtml($url);

		foreach ($this->getAuthorsUrls($html) as $authorPageUrl)
			$this->parseAuthorPage($authorPageUrl);
	}

	public function getUrlHtml($url)
	{
		try {
			$client = new Client();
			$response = $client->request('GET', $url);
			$contents = $response->getBody()->getContents();
			return $contents;

		} catch (Exception $exception) {
			return '';
		}
	}

	public function getAuthorsUrls($html)
	{
		$urls = $this->getUrlsFromHtml($html);

		$array = [];

		foreach ($urls as $url) {

			$url = Url::fromString($url);

			if (preg_match('/\/([A-z0-9]{1,1})\/([[:graph:]])/iu', $url->getPath())) {

				if (!preg_match('/\.shtml$/iu', $url->getPath())) {
					$array[] = (string)$url;
				}
			}
		}

		return $array;
	}

	public function parseAuthorPage($authorPageUrl)
	{
		if (!$this->isUrlParsed($authorPageUrl)) {
			$html = $this->getUrlHtml($authorPageUrl);

			$data = $this->parseAuthorData($html);

			$this->insertOrUpdate($authorPageUrl, $data);
		} else {
			echo('Уже существует ' . $authorPageUrl . ' ' . " \n");
		}
	}

	public function isUrlParsed($url)
	{
		$page = AuthorParsedData::where('url', $url)->first();

		if (!empty($page))
			return true;
		else
			return false;
	}

	public function parseAuthorData($html)
	{
		$crawler = new Crawler();
		$crawler->addHtmlContent($html, 'windows-1251');

		$title = $crawler->filter('h3')->first();

		$data['name'] = '';

		if ($title->count()) {
			preg_match('/(.*)\:(.*)/iu', $title->text(), $mathches);

			if (!empty($mathches)) {
				$data['name'] = trim($mathches[1]);
			}
		}

		$data['email'] = trim(pos($crawler->filter('ul > li:contains("Aдpeс")')->each(function (Crawler $node, $i) {
			if (preg_match('/Aдpeс\:(.*)/iu', $node->text(), $matches)) {
				return $matches[1];
			}
		})));

		$data['email'] = mb_strtolower($data['email']);

		$data['city'] = trim(pos($crawler->filter('ul > li:contains("Живет")')->each(function (Crawler $node, $i) {

			if (preg_match('/Живет\:(.*)/iu', $node->text(), $matches)) {
				return $matches[1];
			}
		})));

		$data['rating'] = trim(pos($crawler->filter('ul > li:contains("Рейтинг")')->each(function (Crawler $node, $i) {

			if (preg_match('/Рейтинг\:(.*)/iu', $node->text(), $matches)) {
				return $matches[1];
			}
		})));

		return $data;
	}

	public function insertOrUpdate($url, $data)
	{
		$parsedData = AuthorParsedData::where('url', $url)->first();

		if (empty($parsedData)) {
			$parsedData = new AuthorParsedData;
			$parsedData->url = $url;
		}
		$parsedData->name = $data['name'];
		$parsedData->email = $data['email'];
		$parsedData->city = $data['city'];
		$parsedData->rating = $data['rating'];
		$parsedData->save();

		echo($url . ' ' . $parsedData->name . ' ' . $parsedData->email . " \n");
	}
}
