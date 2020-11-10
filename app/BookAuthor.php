<?php

namespace App;

use App\Enums\AuthorEnum;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * App\BookAuthor
 *
 * @property int $book_id
 * @property int $author_id
 * @property int|null $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $type Автор или переводчик или редактор и тп
 * @property-read Author $author
 * @property-read Book $book
 * @method static Builder|BookAuthor newModelQuery()
 * @method static Builder|BookAuthor newQuery()
 * @method static Builder|BookAuthor query()
 * @method static Builder|BookAuthor whereAuthorId($value)
 * @method static Builder|BookAuthor whereBookId($value)
 * @method static Builder|BookAuthor whereCreatedAt($value)
 * @method static Builder|BookAuthor whereOrder($value)
 * @method static Builder|BookAuthor whereType($value)
 * @method static Builder|BookAuthor whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BookAuthor extends Pivot
{
    public $table = 'book_authors';

    public $timestamps = true;

    public function book()
    {
        return $this->belongsTo('App\Book')->any();
    }

    public function author()
    {
        return $this->belongsTo('App\Author')->any();
    }

    public function getTypeKey()
    {
        return AuthorEnum::getKey($this->type);
    }
}
