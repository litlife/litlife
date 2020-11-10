<?php

namespace Tests\Feature;

use App\UrlShort;
use App\User;
use Tests\TestCase;

class QrCodeTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testIsOk()
	{
		$user = User::factory()->create();

		$this->get(route('qrcode', ['str' => route('profile', ['user' => $user->id])]))
			->assertOk();
	}

	public function testLongData()
	{
		$text = 'https://litlife.club/books?size=200&str=https%3A%2F%2Flitlife.club%2Fbooks%3Fsearch%3D%26exclude_genres%255B%255D%3D194%26exclude_genres%255B%255D%3D195%26exclude_genres%255B%255D%3D191%26exclude_genres%255B%255D%3D83%26exclude_genres%255B%255D%3D263%26exclude_genres%255B%255D%3D248%26exclude_genres%255B%255D%3D251%26exclude_genres%255B%255D%3D184%26exclude_genres%255B%255D%3D207%26exclude_genres%255B%255D%3D48%26exclude_genres%255B%255D%3D144%26exclude_genres%255B%255D%3D259%26exclude_genres%255B%255D%3D200%26exclude_genres%255B%255D%3D30%26exclude_genres%255B%255D%3D26%26exclude_genres%255B%255D%3D25%26exclude_genres%255B%255D%3D21%26exclude_genres%255B%255D%3D35%26exclude_genres%255B%255D%3D75%26exclude_genres%255B%255D%3D190%26exclude_genres%255B%255D%3D156%26exclude_genres%255B%255D%3D87%26exclude_genres%255B%255D%3D47%26exclude_genres%255B%255D%3D49%26exclude_genres%255B%255D%3D45%26exclude_genres%255B%255D%3D27%26exclude_genres%255B%255D%3D205%26exclude_genres%255B%255D%3D192%26exclude_genres%255B%255D%3D50%26exclude_genres%255B%255D%3D31%26exclude_genres%255B%255D%3D181%26exclude_genres%255B%255D%3D29%26exclude_genres%255B%255D%3D216%26exclude_genres%255B%255D%3D258%26exclude_genres%255B%255D%3D151%26exclude_genres%255B%255D%3D228%26exclude_genres%255B%255D%3D249%26exclude_genres%255B%255D%3D266%26exclude_genres%255B%255D%3D255%26exclude_genres%255B%255D%3D268%26exclude_genres%255B%255D%3D215%26exclude_genres%255B%255D%3D270%26exclude_genres%255B%255D%3D262%26exclude_genres%255B%255D%3D252%26exclude_genres%255B%255D%3D146%26exclude_genres%255B%255D%3D198%26exclude_genres%255B%255D%3D264%26exclude_genres%255B%255D%3D224%26exclude_genres%255B%255D%3D212%26exclude_genres%255B%255D%3D196%26exclude_genres%255B%255D%3D217%26exclude_genres%255B%255D%3D211%26exclude_genres%255B%255D%3D193%26exclude_genres%255B%255D%3D185%26exclude_genres%255B%255D%3D32%26exclude_genres%255B%255D%3D253%26exclude_genres%255B%255D%3D247%26exclude_genres%255B%255D%3D23%26exclude_genres%255B%255D%3D226%26exclude_genres%255B%255D%3D203%26exclude_genres%255B%255D%3D51%26exclude_genres%255B%255D%3D186%26exclude_genres%255B%255D%3D197%26exclude_genres%255B%255D%3D213%26exclude_genres%255B%255D%3D220%26exclude_genres%255B%255D%3D260%26exclude_genres%255B%255D%3D227%26exclude_genres%255B%255D%3D163%26exclude_genres%255B%255D%3D153%26exclude_genres%255B%255D%3D102%26exclude_genres%255B%255D%3D208%26exclude_genres%255B%255D%3D189%26exclude_genres%255B%255D%3D267%26exclude_genres%255B%255D%3D187%26exclude_genres%255B%255D%3D225%26exclude_genres%255B%255D%3D201%26exclude_genres%255B%255D%3D84%26exclude_genres%255B%255D%3D250%26exclude_genres%255B%255D%3D265%26exclude_genres%255B%255D%3D86%26exclude_genres%255B%255D%3D222%26exclude_genres%255B%255D%3D261%26exclude_genres%255B%255D%3D209%26exclude_genres%255B%255D%3D219%26exclude_genres%255B%255D%3D223%26exclude_genres%255B%255D%3D46%26exclude_genres%255B%255D%3D214%26exclude_genres%255B%255D%3D269%26exclude_genres%255B%255D%3D99%26exclude_genres%255B%255D%3D37%26exclude_genres%255B%255D%3D78%26exclude_genres%255B%255D%3D204%26exclude_genres%255B%255D%3D85%26exclude_genres%255B%255D%3D229%26exclude_genres%255B%255D%3D279%26exclude_genres%255B%255D%3D149%26exclude_genres%255B%255D%3D34%26exclude_genres%255B%255D%3D81%26exclude_genres%255B%255D%3D80%26exclude_genres%255B%255D%3D145%26exclude_genres%255B%255D%3D36%26exclude_genres%255B%255D%3D33%26exclude_genres%255B%255D%3D101%26exclude_genres%255B%255D%3D74%26exclude_genres%255B%255D%3D276%26exclude_genres%255B%255D%3D210%26exclude_genres%255B%255D%3D103%26exclude_genres%255B%255D%3D82%26exclude_genres%255B%255D%3D221%26exclude_genres%255B%255D%3D98%26exclude_genres%255B%255D%3D28%26exclude_genres%255B%255D%3D140%26exclude_genres%255B%255D%3D100%26exclude_genres%255B%255D%3D188%26exclude_genres%255B%255D%3D24%26language%3DRU%26originalLang%3D%26rs%3D%26order%3Ddate_down%26view%3Dgallery%26author_gender%3D%26si%3D%26lp%3D%26read_access%3D%26download_access%3Dopen%26CoverExists%3D%26AnnotationExists%3D%26write_year_after%3D%26write_year_before%3D%26publish_year_after%3D%26publish_year_before%3D%26pages_count_min%3D%26pages_count_max%3D%26publish_city%3D%26comments_exists%3D';

		$response = $this->get(route('qrcode', ['str' => $text]))
			->assertOk();

		$url = UrlShort::latest()->first();

		$response->assertViewHas(['str' => $url->getShortUrl()]);
	}

	public function testNullValue()
	{
		$this->get(route('qrcode'))
			->assertStatus(400);
	}

	public function testWrongScheme()
	{
		$text = route('qrcode');

		$text = 'ns' . $text;

		$response = $this->get(route('qrcode', ['str' => $text]))
			->assertStatus(400);
	}

	public function test400ErrorIfRouteIsNotExists()
	{
		$text = 'https://litlife.club/-1" and 6=3 or 1=1+(SELECT 1 and ROW(1,1)>(SELECT COUNT(*),CONCAT(CHAR(95),CHAR(33),CHAR(64),CHAR(52),CHAR(100),CHAR(105),CHAR(108),CHAR(101),CHAR(109),CHAR(109),CHAR(97),0x3a,FLOOR(RAND(0)*2))x FROM INFORMATION_SCHEMA.COLLATIONS GROUP BY x)a)+"';

		$response = $this->get(route('qrcode', ['str' => $text]))
			->assertStatus(400);
	}
}
