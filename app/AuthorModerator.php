<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\AuthorModerator
 *
 * @property-read \App\User $create_user
 * @property-read \App\User|null $user
 * @method static Builder|AuthorModerator newModelQuery()
 * @method static Builder|AuthorModerator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|AuthorModerator query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static Builder|AuthorModerator whereCreator(\App\User $user)
 * @mixin Eloquent
 */
class AuthorModerator extends Model
{
	use UserCreate;

	public $typeArray = [
		'3' => 'editor',
		'4' => 'author'
	];

	public static function boot()
	{
		static::Creating(function ($model) {
			$model->create_user()->associate(auth()->user());
		});

		parent::boot();
	}

	/*
		public function setTypeAttribute($value)
		{
			foreach ($this->typeArray as $code => $val) {
				if ($value == $val)
					$this->attributes['type'] = $code;
			}
		}

		public function getTypeAttribute($value)
		{
			foreach ($this->typeArray as $code => $val) {
				if ($code == $value)
					return $val;
			}
		}
		*/

	public function user()
	{
		return $this->hasOne('App\User', 'id', 'user_id');
	}
}
