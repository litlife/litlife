<?php

use App\Smile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

class SmilesSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public $Arr;

	public function run()
	{

		DB::transaction(function () {

			$old_path = public_path() . '/plugin/bbcode/images/smiles';

			$this->Arr = array(
				array('1.gif', 'Very we!', ':D', 'Pr' => 2),
				array('2.gif', 'Well', ':)', 'Pr' => 2),
				array('3.gif', 'Not so', ':(', 'Pr' => 3),
				array('4.gif', 'Eyes in a heap', ':heap:'),
				array('5.gif', 'Really?', ':ooi:'),
				array('6.gif', 'So-so', ':so:'),
				array('7.gif', 'It is surprised', ':surp:'),
				array('8.gif', 'Again', ':ag:'),
				array('9.gif', 'I roll!', ':ir:'),
				array('10.gif', 'I hesitate', ':oops2:'),
				array('11.gif', 'To you', ':P', 'Pr' => 2),
				array('12.gif', 'Tears', ':cry:'),
				array('13.gif', 'I am malicious', ':rage:'),
				array('14.gif', 'All ok', ':B', 'Pr' => 2),
				array('15.gif', 'Not precisely', ':roll:'),
				array('16.gif', 'To wink', ':wink:'),
				array('17.gif', 'Yes', ':yes:'),
				array('18.gif', 'Has bothered', ':bot:'),
				array('19.gif', 'Ridiculously', ':z)', 'Pr' => 2),
				array('20.gif', 'Here', ':arrow:', 't' => 'hidden'),
				array('21.gif', 'Attention', ':vip:'),
				array('22.gif', 'I congratulate', ':Heppy:'),
				array('23.gif', 'I think', ':think:'),
				array('24.gif', 'Farewell', ':bye:'),
				array('25.gif', 'Perfectly', ':roul:'),
				array('26.gif', 'Fingers', ':pst:'),
				array('27.gif', 'Poorly', ':o:'),
				array('28.gif', 'Veal closed', ':closed:', 't' => 'hidden'),
				array('29.gif', 'Censorship', ':cens:'),
				array('30.gif', 'Features', ':tani:'),
				array('31.gif', 'Applause', ':appl:'),
				array('32.gif', 'I do not know', ':idnk:'),
				array('33.gif', 'Singing', ':sing:'),
				array('34.gif', 'Shock', ':shock:'),
				array('35.gif', 'To give up', ':tgu:', 't' => 'more'),
				array('36.gif', 'Respect', ':res:'),
				array('37.gif', 'Alcohol', ':alc:'),
				array('38.gif', 'The lamer', ':lam:'),
				array('39.gif', 'Boxing', ':box:'),
				array('40.gif', 'Tomato', ':tom:'),
				array('41.gif', 'Cheerfully', ':lol:', 't' => 'hidden'),
				array('42.gif', 'The villain', ':vill:', 't' => 'more'),
				array('43.gif', 'Idea', ':idea:'),
				array('44.gif', 'Oops!', ':oops:'),
				array('45.gif', 'The big rage', ':E', 'Pr' => 2),
				array('46.gif', 'Sex', ':sex:', 't' => 'more'),
				array('47.gif', 'Horns', ':horns:'),
				array('48.gif', 'Love me', ':love:', 't' => 'hidden'),
				array('49.gif', 'Happy birthday', ':poz:'),
				array('50.gif', 'Roza', ':roza:'),
				array('51.gif', 'Megaphone', ':meg:', 't' => 'hidden'),
				array('52.gif', 'The DJ', ':dj:'),
				array('53.gif', 'Rules', ':rul:', 't' => 'more'),
				array('54.gif', 'OffLine', ':offln:', 't' => 'hidden'),
				array('55.gif', 'Spider', ':sp:', 't' => 'hidden'),
				array('56.gif', 'Storm of applause', ':stapp:'),
				array('57.gif', 'Beautiful girl', ':girl:', 't' => 'hidden'),
				array('58.gif', 'Heart', ':heart:'),
				array('59.gif', 'Kiss', ':kiss:'),
				array('60.gif', 'Spam', ':spam:', 't' => 'more'),
				array('61.gif', 'Party', ':party:'),
				array('62.gif', 'Song', ':ser:', 't' => 'more'),
				array('63.gif', 'Dream', ':eam:'),
				array('64.gif', 'Gift', ':gift:', 't' => 'hidden'),
				array('65.gif', 'I adore', ':adore:'),
				array('66.gif', 'Pie', ':pie:'),
				array('67.gif', 'Egg', ':egg:', 't' => 'hidden'),
				array('68.gif', 'Concert', ':cnrt:', 't' => 'more'),
				array('69.gif', 'Off Topic', ':oftop:', 't' => 'more'),
				array('70.gif', 'Football', ':foo:'),
				array('71.gif', 'Cellular', ':mob:', 't' => 'hidden'),
				array('72.gif', 'Not hooligan', ':hoo:', 't' => 'more'),
				array('73.gif', 'Together', ':tog:'),
				array('74.gif', 'Pancake', ':pnk:'),
				array('75.gif', 'Party Time', ':pati:', 't' => 'more'),
				array('76.gif', 'I here', ':-({|=:', 'Pr' => 2),
				array('77.gif', 'Head about a wall', ':haaw:'),
				array('78.gif', 'Angel', ':angel:'),
				array('79.gif', 'killer', ':kil:', 't' => 'more'),
				array('80.gif', 'Cemetery', ':died:'),
				array('81.gif', 'Coffee', ':cof:'),
				array('82.gif', 'Forbidden fruit', ':fruit:'),
				array('83.gif', 'To tease', ':tease:'),
				array('84.gif', 'Devil', ':evil:'),
				array('85.gif', 'Excellently', ':exc:'),
				array('86.gif', 'Not I, and he', ':niah:', 't' => 'hidden'),
				array('87.gif', 'Studio', ':Head:'),
				array('88.gif', 'Girl', ':gl:', 't' => 'hidden'),
				array('89.gif', 'Pomegranate', ':granat:', 't' => 'more'),
				array('90.gif', 'Gangster', ':gans:'),
				array('91.gif', 'User', ':user:'),
				array('92.gif', 'New year', ':ny:', 't' => 'hidden'),
				array('93.gif', 'Megavolt', ':mvol:'),
				array('94.gif', 'In a boat', ':boat:'),
				array('95.gif', 'Phone', ':phone:', 't' => 'hidden'),
				array('96.gif', 'Cop', ':cop:'),
				array('97.gif', 'Smoking', ':smok:'),
				array('98.gif', 'Bicycle', ':bic:', 't' => 'more'),
				array('99.gif', 'Ban?', ':ban:', 't' => 'more'),
				array('100.gif', 'Bar', ':bar:'),
				array('101.gif', 'blush2', ':blush2:'),
			);


			for ($a = 1; $a < 40; $a++) {
				//$this->Arr[] = array('ny'.$a.'.gif', 'ny'.$a.'', ':ny'.$a.':', 't' => 'hidden');
				$this->Arr[] = array('ny' . $a . '.gif', 'ny' . $a . '', ':ny' . $a . ':', 't' => 'more', 'ny' => true);
			}


			$MoreNumberAr = array(
				200, 201, 202, 209, 211, 213, 214, 217, 218, 219,
				220, 222, 224, 251
			);

			$ExcludeAr = array(254);

			for ($a = 200; $a <= 279; $a++) {
				if (!in_array($a, $ExcludeAr)) {
					$Arr = array('' . $a . '.gif', 'sm' . $a . '');

					$this->Arr[] = $Arr;
				}
			}

			$this->Arr[] = array('253.png', 'sm253', ':sm253:', 't' => 'more');
			$this->Arr[] = array('252.png', 'sm254', ':sm254:', 't' => 'more');


			foreach ($this->Arr as $smile) {
				$path = $old_path . '/' . $smile[0];

				if (File::exists($path)) {
					$img = Image::make($path);

					$parameters = ['width' => $img->width(), 'height' => $img->height()];


					$smiles = new Smile;
					$smiles->name = $smile[0];
					$smiles->description = $smile[1];
					$smiles->simple_form = $smile[2] ?? null;
					$smiles->parameters = $parameters;

					if (isset($smile['ny']))
						$smiles->for = 'NewYear';

					$smiles->save();
				}


				/*
				DB::table('smiles')->insert([
					'name' => $smile[0],
					'description' => $smile[1],
					'simple_form' => $smile[2] ?? null,
					'parameters' => $parameters
				]);
				*/
			}

			\Illuminate\Support\Facades\Artisan::call('smile:create_json_file');
		});
	}
}
