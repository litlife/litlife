<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\BookTranslator
 *
 * @method static Builder|BookTranslator newModelQuery()
 * @method static Builder|BookTranslator newQuery()
 * @method static Builder|BookTranslator query()
 * @mixin Eloquent
 */
class BookTranslator extends Pivot
{
    public $table = 'book_translators';
}
