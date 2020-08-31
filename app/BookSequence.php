<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\BookSequence
 *
 * @property int $book_id
 * @property int $sequence_id
 * @property int|null $number
 * @property int|null $order
 * @method static Builder|BookSequence newModelQuery()
 * @method static Builder|BookSequence newQuery()
 * @method static Builder|BookSequence query()
 * @method static Builder|BookSequence whereBookId($value)
 * @method static Builder|BookSequence whereNumber($value)
 * @method static Builder|BookSequence whereOrder($value)
 * @method static Builder|BookSequence whereSequenceId($value)
 * @mixin Eloquent
 */
class BookSequence extends Pivot
{
	public $table = 'book_sequences';

}

?>