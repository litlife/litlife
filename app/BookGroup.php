<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * App\BookGroup
 *
 * @property int $id
 * @property int|null $create_user_id
 * @property int $books_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Book[] $books
 * @property-read User|null $create_user
 * @property mixed $rate_info
 * @property-read Book|null $main_book
 * @property-read \Illuminate\Database\Eloquent\Collection|Book[] $not_main_books
 * @method static Builder|BookGroup newModelQuery()
 * @method static Builder|BookGroup newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookGroup query()
 * @method static Builder|Model void()
 * @method static Builder|BookGroup whereBooksCount($value)
 * @method static Builder|BookGroup whereCreateUserId($value)
 * @method static Builder|BookGroup whereCreatedAt($value)
 * @method static Builder|BookGroup whereCreator(User $user)
 * @method static Builder|BookGroup whereId($value)
 * @method static Builder|BookGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BookGroup extends Model
{
    use UserCreate;
    use HasRelationships;

    public static function boot()
    {
        static::Creating(function ($model) {
            $model->create_user()->associate(auth()->user());
        });

        parent::boot();
    }

    function not_main_books()
    {
        return $this->hasMany('App\Book', 'group_id')->where('main_in_group', false);
    }

    function main_book()
    {
        return $this->hasOne('App\Book', 'group_id')->where('main_in_group', true);
    }

    public function setBooksCountAttribute($value)
    {
        $this->attributes['books_count'] = intval($value);
    }

    public function getBooksCountAttribute($value)
    {
        return intval($value);
    }

    public function addBook($book, $main = false)
    {
        $book->group_id = $this->id;
        $book->main_in_group = (boolean)$main;
        $book->connected_at = now();
        $book->save();

        $this->refreshBooksCount();
        $this->save();
    }

    public function refreshBooksCount()
    {
        $this->books_count = $this->books()
            ->accepted()
            ->count();
    }

    function books()
    {
        return $this->hasMany('App\Book', 'group_id');
    }

    public function votedForBookUsers()
    {
        return $this->hasManyDeep(User::class, [Book::class, BookVote::class],
            [
                'group_id', // Foreign key on the "users" table.
                'book_id',    // Foreign key on the "posts" table.
                'id'     // Foreign key on the "comments" table.
            ],
            [
                'id', // Local key on the "countries" table.
                'id', // Local key on the "users" table.
                'create_user_id'  // Local key on the "posts" table.
            ])->withPivot('book_votes', ['vote'], 'App\BookVote', 'pivot');
    }

    public function bookVotes()
    {
        return $this->hasManyDeep(BookVote::class, [Book::class],
            [
                'group_id', // Foreign key on the "users" table.
                'book_id',    // Foreign key on the "posts" table.
            ],
            [
                'id', // Local key on the "countries" table.
                'id', // Local key on the "users" table.
            ]);
    }

    public function bookStatuses()
    {
        return $this->hasManyDeep(BookStatus::class, [Book::class],
            [
                'group_id', // Foreign key on the "users" table.
                'book_id',    // Foreign key on the "posts" table.
            ],
            [
                'id', // Local key on the "countries" table.
                'id', // Local key on the "users" table.
            ])
            ->where('book_statuses.status', '!=', 'null');
    }

    public function removeBook($book)
    {
        $book->group_id = null;
        $book->main_in_group = false;
        $book->connected_at = null;
        $book->save();

        $this->refreshBooksCount();
        $this->save();
    }

    public function deleteIfOnlyOneBookRemains()
    {
        if ($this->books_count <= 1) {
            $this->delete();
        }
    }

    public function selectMainBookIfNotMainBookExists()
    {
        if ($this->exists and !$this->hasMainBook()) {
            $main_book = $this->bookWithBestRating();
            $main_book->addToGroup($this, true);
            $main_book->save();
        }
    }

    public function hasMainBook(): bool
    {
        if ($this->main_book) {
            return true;
        } else {
            return false;
        }
    }

    public function bookWithBestRating()
    {
        return Book::where('group_id', $this->id)
            ->orderBy('vote_average', 'desc')
            ->first();
    }

    public function setRateInfoAttribute($array)
    {
        $this->attributes['rate_info'] = $array ? serialize($array) : null;
    }

    public function getRateInfoAttribute($value)
    {
        $rate_info = unserialize($value);

        $max = @max($rate_info) ?? 0;

        $array = [];

        foreach (config('litlife.votes') as $vote) {

            if (isset($rate_info[$vote]) and is_numeric($rate_info[$vote])) {
                $array[$vote] = [
                    'percent' => intval(round((100 * $rate_info[$vote]) / $max, 0)),
                    'count' => $rate_info[$vote]
                ];
            } else {
                $array[$vote] = [
                    'percent' => 0,
                    'count' => 0
                ];
            }
        }

        return $array;
    }
}
