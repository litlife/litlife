<?php

namespace App\Console\Commands;

use App\Author;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;

class FillAuthorSite extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'author_site:fill';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';
	private $domain;
	private $user;
	private $password;
	private $counter = 0;
	private $author;
	private $client;

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
		$this->domain = $this->ask('Enter domain:');
		//$this->domain = 'http://pushkin-aleksandr.ru/';
		//$user = $this->ask('Enter username:');
		$this->user = 'admin';
		//$password = $this->ask('Enter password:');
		$this->password = '3pf3p7f7112l27d2S';

		$author_id = $this->ask('Enter author id:');
		//$author_id = 483;

		$this->domain = parse_url($this->domain, PHP_URL_HOST);

		$this->info('domain: ' . $this->domain . "\n");
		$this->info('user: ' . $this->user . "\n");
		$this->info('author_id: ' . $author_id . "\n");

		$this->client = new Client();

		$this->delete_posts();
		$this->author = Author::findOrFail($author_id);

		foreach ($this->author->books()->readAndDownloadAccess()->orderBy('sum_of_votes', 'desc')->get() as $book) {
			$this->book($book);
		}
	}

	private function delete_posts()
	{
		$response = $this->client->request('GET', 'http://' . $this->domain . '/wp-json/wp/v2/posts', [
			'form_params' => [
				'per_page' => '100000'
			],
		]);

		$json = json_decode($response->getBody(), true);

		if (!empty($json)) {
			foreach ($json as $post) {
				$response = $this->client->request('DELETE', 'http://' . $this->domain . '/wp-json/wp/v2/posts/' . $post['id'], [
					'auth' => [$this->user, $this->password]
				]);
			}
		}
	}

	public function book($book)
	{
		$content = '';

		if (!empty($book->cover)) {

			$content .= '<a target="_blank" href="https://litlife.club/bd/?b=' . $book->id . '">' .
				'<img style="max-width: 200px; float: left; padding: 0 15px 15px 0;" src="' . $book->cover->fullUrl . '" />' .
				'</a>';
		}

		if (!empty($book->genres)) {

			$content .= '<b>Жанр:</b> ';

			$array = [];

			foreach ($book->notMain()->genres as $genre) {
				$array[] = '<a target="_blank" href="https://litlife.club/bs/?g=sg' . $genre->id . '&amp;hc=1&amp;rs=1%7C0">' . $genre->name . '</a>';
			}

			$content .= ' ' . implode(', ', $array) . ' ';
		}

		$content .= "\n\r";

		if (!empty($book->annotation)) {
			if (!empty($annotation = $book->annotation->getContent())) {
				$content .= strip_tags($annotation);
				$content .= "\n\r";
			}
		}

		$content .= ' <a target="_blank" href="https://litlife.club/br/?b=' . $book->id . '">Читать книгу онлайн</a>';

		//dump($book->title);

		//dump($content);

		if (!$this->isPostExists($book->title)) {
			$this->addPost($book->title, $content);

			$this->info('Book added ' . $book->id . ' ');
		}
	}

	public function isPostExists($search)
	{
		$search = trim(mb_strtolower($search));

		$response = $this->client->request('GET', 'http://' . $this->domain . '/wp-json/wp/v2/posts', [
			'auth' => [$this->user, $this->password],
			'form_params' => [
				'search' => trim($search)
			],
		]);

		$json = json_decode($response->getBody(), true);

		if (empty($json))
			return false;

		foreach ($json as $post) {
			if (trim(mb_strtolower($post['title']['rendered'])) == $search)
				return TRUE;

			if (trim(mb_strtolower(html_entity_decode($post['title']['rendered']))) == trim(mb_strtolower(html_entity_decode($search))))
				return TRUE;
		}

		return FALSE;
	}

	private function addPost($title, $content)
	{
		$title = trim($title);
		$content = trim($content);

		try {

			$response = $this->client->request('post', 'http://' . $this->domain . '/wp-json/wp/v2/posts', [
				'auth' => [$this->user, $this->password],
				'form_params' => [
					'title' => $title,
					'content' => $content,
					'status' => 'publish',
					'categories' => '2',
					'date' => date('Y-m-d H:i:s', (time() - ($this->counter * rand(60400, 80000))))
				],
				'allow_redirects' => [
					'max' => 10,        // allow at most 10 redirects.
					'strict' => true,      // use "strict" RFC compliant redirects.
					'referer' => true,      // add a Referer header
					'track_redirects' => true
				]
			]);

		} catch (RequestException $e) {
			//echo Psr7\str($e->getRequest());
			dd($e->getMessage());
		}

		$post = json_decode($response->getBody(), true);

		$this->counter++;
	}
}
