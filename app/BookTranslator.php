<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * App\BookTranslator
 *
 * @property int $book_id
 * @property int $translator_id
 * @property int $time
 * @property int|null $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|BookTranslator newModelQuery()
 * @method static Builder|BookTranslator newQuery()
 * @method static Builder|BookTranslator query()
 * @method static Builder|BookTranslator whereBookId($value)
 * @method static Builder|BookTranslator whereCreatedAt($value)
 * @method static Builder|BookTranslator whereOrder($value)
 * @method static Builder|BookTranslator whereTime($value)
 * @method static Builder|BookTranslator whereTranslatorId($value)
 * @method static Builder|BookTranslator whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BookTranslator extends Pivot
{
	public $table = 'book_translators';
}
