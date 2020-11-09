<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\AuthorParsedData
 *
 * @property int $id
 * @property string $url Ссылка на страницу автора
 * @property string $name Имя автора
 * @property string $email Почта автора
 * @property string $city Город автора
 * @property string $rating Рейтинг
 * @property string|null $created_at
 * @property string|null $updated_at
 * @method static Builder|AuthorParsedData newModelQuery()
 * @method static Builder|AuthorParsedData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|AuthorParsedData query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static Builder|AuthorParsedData whereCity($value)
 * @method static Builder|AuthorParsedData whereCreatedAt($value)
 * @method static Builder|AuthorParsedData whereEmail($value)
 * @method static Builder|AuthorParsedData whereId($value)
 * @method static Builder|AuthorParsedData whereName($value)
 * @method static Builder|AuthorParsedData whereRating($value)
 * @method static Builder|AuthorParsedData whereUpdatedAt($value)
 * @method static Builder|AuthorParsedData whereUrl($value)
 * @mixin Eloquent
 */
class AuthorParsedData extends Model
{
	public $timestamps = false;

	public function setUrlAttribute($s)
	{
		$this->attributes['url'] = mb_substr(trim(mb_strtolower($s)), 0, 255);
	}

	public function setNameAttribute($s)
	{
		$this->attributes['name'] = mb_substr(trim($s), 0, 255);
	}

	public function setEmailAttribute($s)
	{
		$this->attributes['email'] = mb_substr(trim(mb_strtolower($s)), 0, 100);
	}

	public function setCityAttribute($s)
	{
		$this->attributes['city'] = mb_substr(trim($s), 0, 30);
	}

	public function setRatingAttribute($s)
	{
		$this->attributes['rating'] = mb_substr(trim($s), 0, 10);
	}

	public function getEmailAttribute($s)
	{
		$s = str_replace(' ', '', $s);
		$s = str_replace('[at]', '@', $s);
		$s = str_replace('[dot]', '.', $s);
		$s = str_replace('[sobaka]', '@', $s);
		$s = str_replace('[co6aka]', '@', $s);
		$s = str_replace('[@]', '.', $s);
		$s = str_replace('[]', '@', $s);
		$s = str_replace('[', '', $s);
		$s = str_replace(']', '', $s);

		return $s;
	}

	public function getAbsoluteRating()
	{
		$rating = $this->attributes['rating'];

		if (preg_match('/\*/iu', $rating)) {
			$array = explode('*', $rating);

			list($vote, $count) = $array;

			$rating = $vote * $count;
		}

		return intval($rating);
	}

	public function isEmailVaild()
	{
		if (filter_var($this->email, FILTER_VALIDATE_EMAIL))
			return true;
		else
			return false;
	}
}
