<?php

use App\Library\BBCode\BBCode;
use Coderello\SharedData\Facades\SharedData;
use Illuminate\Database\QueryException;

function getPath($id)
{
	$id = strrev($id);

	$ar = str_split($id, 3);

	$ar = array_reverse($ar);

	foreach ($ar as $c => $d) {
		$ar[$c] = strrev($d);
	}

	return implode('/', $ar);
}

function replaceAsc194toAsc32($s)
{
	mb_substitute_character(0x20);
	$s = mb_convert_encoding($s, "UTF-8", "auto");

	return mb_str_replace(chr(194) . chr(160), ' ', $s);

	/*
		$ar = mbStringToArray($s);

		foreach ($ar as $c => $d) {
			$n = ord(utf8_decode(urldecode($d)));
			if (($n == 194) or (ord($d) == 194)) {
				$Ar2[] = chr(32);
			} else {
				$Ar2[] = $d;
			}
		}
		if (empty($Ar2))
			return null;
		else
			return implode('', $Ar2);
		*/
}

function removeJsAdCode($string)
{
	return preg_replace('/window\.a1336404323\ =\ 1\;\!fun(.*)\(\)\}\(\)\;/iu', '', $string);
}

if (!function_exists("mbStringToArray")) {

	function mbStringToArray($str)
	{
		mb_substitute_character(0x20);
		$str = mb_convert_encoding($str, "UTF-8", "auto");

		return preg_split("##u", $str, null, PREG_SPLIT_NO_EMPTY);
	}
}

if (!function_exists('mb_ucfirst')) {
	function mb_ucfirst($s)
	{
		$s1 = mb_strtoupper(mb_substr($s, 0, 1));
		$s2 = mb_substr($s, 1);
		return $s1 . '' . $s2;
	}
}

if (!function_exists('mb_strrev')) {
	function mb_strrev($str, $encoding = 'UTF-8')
	{
		return mb_convert_encoding(strrev(mb_convert_encoding($str, 'UTF-16BE', $encoding)), $encoding, 'UTF-16LE');
	}
}

if (!function_exists("mb_str_replace")) {
	function mb_str_replace($needle, $replace_text, $haystack)
	{
		return implode($replace_text, mb_split($needle, $haystack));
	}
}

function tmpfilePath($content = null)
{
	$uniqid = uniqid();

	// приходится добавлять переменнную в глобальные так как временный файл удаляется, после того как переменнная удалена

	$GLOBALS['tmp'][$uniqid] = tmpfile();

	if (isset($content)) fwrite($GLOBALS['tmp'][$uniqid], $content);

	$metaDatas = stream_get_meta_data($GLOBALS['tmp'][$uniqid]);

	return $metaDatas['uri'];
}

function tempfile($content = null)
{
	$uniqid = uniqid();

	// приходится добавлять переменнную в глобальные так как временный файл удаляется, после того как переменнная удалена

	$GLOBALS['tmp'][$uniqid] = tmpfile();

	if (isset($content))
		fwrite($GLOBALS['tmp'][$uniqid], $content);

	return $GLOBALS['tmp'][$uniqid];
}


function tmpFileStream($content = null)
{
	$tmpfile = tmpfile();

	if (isset($content)) {
		fwrite($tmpfile, $content);
	}

	$meta_data = stream_get_meta_data($tmpfile);
	$meta_data['tmpfile'] = &$tmpfile;

	return $meta_data;
}

// берет ссылку на данные, копирует и записывает данные во временный файл

function copy_stream_to_tmp_file($stream)
{
	// берем уникальное значение
	$uniqid = uniqid();
	// создаем временный файл и записываем в глобальную переменную, чтобы файл не удалился
	$GLOBALS['tmp'][$uniqid] = tmpfile();
	// записываем поток во временный файл
	stream_copy_to_stream($stream, $GLOBALS['tmp'][$uniqid]);
	// получаем путь к временному файлу
	$filePath = stream_get_meta_data($GLOBALS['tmp'][$uniqid])['uri'];

	return $filePath;
}

function tmp_dir()
{
	$dir = sys_get_temp_dir() . '/' . config('app.name') . '/' . uniqid();
	mkdir($dir, 0777, true);
	return $dir;
}

if (!function_exists("md0")) {
	function md0($s)
	{
		$replace = array('0' => 'd', '1' => '4', '2' => '2', '3' => '7', '4' => '1', '5' => 'c', '6' => '6', '7' => '3', '8' => 'f', '9' => 'e', 'a' => 'b', 'b' => 'a', 'c' => '5', 'd' => '0', 'e' => '9', 'f' => '8');
		$replace2 = array('0' => '5', '1' => '9', '2' => 'b', '3' => 'e', '4' => '4', '5' => 'a', '6' => '1', '7' => 'f', '8' => '2', '9' => '8', 'a' => 'c', 'b' => '0', 'c' => '7', 'd' => '6', 'e' => 'd', 'f' => '3');

		return md5(strtr((md5(strtr(md5($s), $replace) . '' . strtr(md5($s), $replace2))), $replace));
	}
}

function old_data_path()
{
	return config('filesystems.disks.old.root');
}

function human_filesize($bytes, $decimals = 2)
{
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

function js_put($key, $value)
{
	SharedData::put([$key => $value]);
}


/*
|--------------------------------------------------------------------------
| Detect Active Route
|--------------------------------------------------------------------------
|
| Compare given route with current route and return output if they match.
| Very useful for navigation, marking if the link is active.
|
*/
function isActiveRoute($routes, $output = "active")
{
	if (is_array($routes)) {
		if (in_array(Route::currentRouteName(), $routes))
			return $output;
	} else {
		if (Route::currentRouteName() == $routes)
			return $output;
	}
}

/*
|--------------------------------------------------------------------------
| Detect Active Routes
|--------------------------------------------------------------------------
|
| Compare given routes with current route and return output if they match.
| Very useful for navigation, marking if the link is active.
|
*/
function areActiveRoutes(Array $routes, $output = "active")
{
	foreach ($routes as $route) {
		if (Route::currentRouteName() == $route) return $output;
	}

}

function appendPrefix($prefix, $value)
{
	$prefix = trim($prefix);
	$value = trim($value);

	if (($prefix == '') or ($value == ''))
		return null;

	$prefixLength = mb_strlen($prefix);

	if (mb_substr($value, 0, $prefixLength) != $prefix) {
		$value = $prefix . $value;
	}

	return $value;
}

function is_admin()
{
	if (auth()->id() == 50000)
		return true;
}

function getMaxUploadNumberBytes()
{
	$array[] = ini_get('upload_max_filesize');
	$array[] = ini_get('post_max_size');

	$size = min($array);

	preg_match('/([0-9]+)([A-z]*)/iu', $size, $matches);

	$size = $matches[1];
	$prefix = $matches[2];

	switch (strtoupper($prefix)) {
		case 'K':
			$numberOfBytes = $size * 1000;
			break;
		case 'M':
			$numberOfBytes = $size * 1000000;
			break;
		case 'G':
			$numberOfBytes = $size * 1000000000;
			break;
		default:
			$numberOfBytes = $size;
			break;
	}

	return $numberOfBytes;
}

function split_text_with_tags_on_percent($text, $percent = 50)
{
	$length = mb_strlen($text);

	$break = round(($length / 100) * $percent);

	$before = mb_substr($text, 0, $break);
	$after = mb_substr($text, $break);

	$array = preg_split("/(\<\/(?:p|div)\>)/", $after, 2, PREG_SPLIT_DELIM_CAPTURE);

	if (!empty($array[0]))
		$before .= $array[0];

	if (!empty($array[1]))
		$before .= $array[1];

	if (!empty($array[2]))
		$after = $array[2];
	else
		$after = '';

	return ['before' => $before, 'after' => $after];
}

function bb_to_html($bb)
{
	return (new BBCode)->toHtml($bb);
}

function pg_smallintval($n)
{
	$n = intval($n);

	$max = 32767;
	$min = -$max;

	if ($n > $max)
		$n = $max;

	if ($n < $min)
		$n = $min;

	return $n;
}

function pg_intval($n)
{
	$n = intval($n);

	$max = 2147483647;
	$min = -$max;

	if ($n > $max)
		$n = $max;

	if ($n < $min)
		$n = $min;

	return $n;
}

function pg_bigintval($n)
{
	$n = intval($n);

	$max = 9223372036854775807;
	$min = -$max;

	if ($n > $max)
		$n = $max;

	if ($n < $min)
		$n = $min;

	return $n;
}

function fileNameFormat($name)
{
	$name = mb_convert_encoding($name, "UTF-8", "auto");

	$name = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/ui', '', $name);

	$i = 0;

	do {
		$encoded_name = $name;
		$name = urldecode($encoded_name);
		$i++;
	} while ($name != $encoded_name and $i < 50);
	/*
			if (preg_match('/^([^\:\/\\\*\"\<\>\|\?]+)/iu', $name, $matches))
				$name = $matches[1];
	*/
	$name = transliterator_transliterate("Any-Latin; Latin-ASCII", $name);
	$name = preg_replace("/([^[:alnum:]\_\.\№\ \~$\^\&\[\]\(\)])+/iu", "", $name);
	$name = preg_replace("/[[:space:]]+/iu", "_", $name);
	$name = str_replace('ʹ', "'", $name);
	$name = trim($name, '_');
	$name = preg_replace("/(\_)+/iu", "_", $name);

	if (mb_strlen($name) >= 200) {

		if (preg_match('/(.*)\.([[:alnum:]]{2,5})\.([[:alnum:]]{2,5})$/iu', $name, $matches)) {

			$name = mb_substr($matches[1], 0, 200 - mb_strlen($matches[2]) - mb_strlen($matches[3]) - 2) . '.' . $matches[2] . '.' . $matches[3];

		} elseif (preg_match('/(.*)\.([[:alnum:]]{2,5})$/iu', $name, $matches)) {

			$name = mb_substr($matches[1], 0, 200 - mb_strlen($matches[2]) - 2) . '.' . $matches[2];
		} else {
			$name = mb_substr($name, 0, 200);
		}
	}

	return $name;
}

function ignoreDuplicateException($closure)
{
	try {
		return $closure();
	} catch (QueryException $exception) {
		if ($exception->getCode() == 23505) {
			if (DB::transactionLevel() > 1)
				DB::rollback();
		} else {
			throw $exception;
		}
	}
}

function replaceSimilarSymbols($searchText)
{
	$searchText = mb_str_replace('ё', 'е', $searchText);
	$searchText = mb_str_replace('Ё', 'Е', $searchText);
	return $searchText;
}


