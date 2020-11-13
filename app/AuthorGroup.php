<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\AuthorGroup
 *
 * @property int $id
 * @property string|null $last_name
 * @property string|null $first_name
 * @property int|null $create_user_id
 * @property int $count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $authors
 * @property-read \App\User|null $create_user
 * @method static Builder|AuthorGroup newModelQuery()
 * @method static Builder|AuthorGroup newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|AuthorGroup query()
 * @method static Builder|Model void()
 * @method static Builder|AuthorGroup whereCount($value)
 * @method static Builder|AuthorGroup whereCreateUserId($value)
 * @method static Builder|AuthorGroup whereCreatedAt($value)
 * @method static Builder|AuthorGroup whereCreator(\App\User $user)
 * @method static Builder|AuthorGroup whereFirstName($value)
 * @method static Builder|AuthorGroup whereId($value)
 * @method static Builder|AuthorGroup whereLastName($value)
 * @method static Builder|AuthorGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class AuthorGroup extends Model
{
    use UserCreate;

    function authors()
    {
        return $this->hasMany('App\Author', 'group_id');
    }
}
