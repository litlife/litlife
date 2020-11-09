<?php

namespace App;

use App\Traits\UserCreate;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\CollectionUser
 *
 * @property int $id
 * @property int $collection_id ID подборки
 * @property int $user_id ID пользователя
 * @property int $create_user_id ID пользователя
 * @property string|null $description Описание
 * @property bool $can_user_manage Может добавлять, редактировать, удалять других пользователей
 * @property bool $can_edit Может редактировать подборку
 * @property bool $can_add_books Может добавлять книги в подборку
 * @property bool $can_remove_books Может удалять книги из подборки
 * @property bool $can_edit_books_description Может редактировать описания книг
 * @property bool $can_comment Может комментировать
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Collection $collection
 * @property-read \App\User $create_user
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser newQuery()
 * @method static \Illuminate\Database\Query\Builder|CollectionUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCanAddBooks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCanComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCanEdit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCanEditBooksDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCanRemoveBooks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCanUserManage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCollectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|CollectionUser withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CollectionUser withoutTrashed()
 * @mixin \Eloquent
 */
class CollectionUser extends Model
{
	use UserCreate;
	use SoftDeletes;

	protected $table = 'collection_users';

	protected $attributes =
		[
			'description' => null,
			'can_user_manage' => false,
			'can_edit' => false,
			'can_add_books' => false,
			'can_remove_books' => false,
			'can_edit_books_description' => false,
			'can_comment' => false
		];

	protected $fillable = [
		'user_id',
		'description',
		'can_user_manage',
		'can_edit',
		'can_add_books',
		'can_remove_books',
		'can_edit_books_description',
		'can_comment'
	];

	public function getPermissions()
	{
		foreach ($this->getAttributes() as $key => $value) {
			if (preg_match('/^can\_/iu', $key)) {
				$array[$key] = $value;
			}
		}

		return $array;
	}

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function collection()
	{
		return $this->belongsTo('App\Collection');
	}
}
