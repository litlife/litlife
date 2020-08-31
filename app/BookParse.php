<?php

namespace App;

use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\BookParse
 *
 * @property int $id
 * @property int $book_id ID книги над которой производилось действие
 * @property string|null $started_at Время начала парсинга
 * @property Carbon|null $succeed_at Время когда процедура успешно завершилась
 * @property Carbon|null $failed_at Время когда когда произошла ошибка во время процедуры
 * @property array|null $parse_errors Ошибки которые появились при обработке
 * @property array|null $options Опции которые будут отправлены в обработчик
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $waited_at
 * @property int|null $create_user_id
 * @property-read \App\Book $book
 * @property-read \App\User|null $create_user
 * @method static Builder|BookParse failedParse()
 * @method static Builder|BookParse newModelQuery()
 * @method static Builder|BookParse newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookParse query()
 * @method static Builder|BookParse succeedParse()
 * @method static Builder|Model void()
 * @method static Builder|BookParse waited()
 * @method static Builder|BookParse whereBookId($value)
 * @method static Builder|BookParse whereCreateUserId($value)
 * @method static Builder|BookParse whereCreatedAt($value)
 * @method static Builder|BookParse whereCreator(\App\User $user)
 * @method static Builder|BookParse whereFailedAt($value)
 * @method static Builder|BookParse whereId($value)
 * @method static Builder|BookParse whereOptions($value)
 * @method static Builder|BookParse whereParseErrors($value)
 * @method static Builder|BookParse whereStartedAt($value)
 * @method static Builder|BookParse whereSucceedAt($value)
 * @method static Builder|BookParse whereUpdatedAt($value)
 * @method static Builder|BookParse whereWaitedAt($value)
 * @mixin Eloquent
 */
class BookParse extends Model
{
	use UserCreate;

	protected $attributes = [
		'waited_at' => null,
		'started_at' => null,
		'succeed_at' => '2018-06-14 15:12:16',
		'failed_at' => null,
		'parse_errors' => null,
		'created_at' => null,
		'updated_at' => null
	];

	protected $casts = [
		'options' => 'array',
		'parse_errors' => 'array'
	];

	protected $dates = [
		'start_at',
		'succeed_at',
		'failed_at',
		'created_at',
		'updated_at',
		'waited_at'
	];

	public function book()
	{
		return $this->belongsTo('App\Book', 'book_id', 'id')
			->any();
	}

	public function scopeWaited($query)
	{
		return $query->whereNotNull('waited_at')
			->whereNull('started_at')
			->whereNull('failed_at')
			->whereNull('succeed_at');
	}

	public function scopeSucceedParse($query)
	{
		return $query->whereNotNull('succeed_at');
	}

	public function scopeFailedParse($query)
	{
		return $query->whereNotNull('failed_at');
	}

	/*
	* Не отправлена на обработку
	*/
	public function isReseted()
	{
		if (empty($this->waited_at) and empty($this->started_at) and empty($this->failed_at) and empty($this->succeed_at))
			return true;
		else
			return false;
	}

	/*
	* Ожидает ли обработки
	*/
	public function isWait()
	{
		if (!empty($this->waited_at) and empty($this->started_at) and empty($this->failed_at) and empty($this->succeed_at))
			return true;
		else
			return false;
	}

	/*
	* Находится ли в процессе обработки
	*/

	public function isProgress()
	{
		if (!empty($this->started_at) and empty($this->failed_at) and empty($this->succeed_at))
			return true;
		else
			return false;
	}

	/*
	 * Распарсена ли книга
	 */

	public function isParsed()
	{
		return $this->isSucceed();
	}

	/*
	 * Парсинг произошел с ошибкой
	 */

	public function isSucceed()
	{
		if (isset($this->succeed_at))
			return true;
		else
			return false;
	}

	public function isFailed()
	{
		if (isset($this->failed_at))
			return true;
		else
			return false;
	}

	public function isParseOnlyPages()
	{
		return (bool)(in_array('only_pages', $this->options ?? []));
	}

	public function parseOnlyPages()
	{
		$this->options = ['only_pages'];
		$this->save();
	}

	public function start()
	{
		$this->started_at = now();
		$this->failed_at = null;
		$this->succeed_at = null;
		$this->save();
	}

	public function success()
	{
		$this->succeed_at = now();
		$this->failed_at = null;
		$this->save();
	}

	public function failed($error)
	{
		$this->succeed_at = null;
		$this->failed_at = now();
		$this->parse_errors = $error;
		$this->save();
	}

	public function reset()
	{
		$this->waited_at = null;
		$this->started_at = null;
		$this->succeed_at = null;
		$this->failed_at = null;
		$this->parse_errors = null;
		$this->save();
	}

	public function wait()
	{
		$this->waited_at = now();
		$this->started_at = null;
		$this->succeed_at = null;
		$this->failed_at = null;
		$this->parse_errors = null;
		$this->save();
	}

	public function getErrors()
	{
		return $this->parse_errors;
	}
}

