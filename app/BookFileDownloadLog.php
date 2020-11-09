<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\BookFileDownloadLog
 *
 * @property int $id
 * @property int $book_file_id
 * @property int|null $user_id
 * @property string $ip
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\BookFile $book_file
 * @method static Builder|BookFileDownloadLog newModelQuery()
 * @method static Builder|BookFileDownloadLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookFileDownloadLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static Builder|BookFileDownloadLog whereBookFileId($value)
 * @method static Builder|BookFileDownloadLog whereCreatedAt($value)
 * @method static Builder|BookFileDownloadLog whereId($value)
 * @method static Builder|BookFileDownloadLog whereIp($value)
 * @method static Builder|BookFileDownloadLog whereUpdatedAt($value)
 * @method static Builder|BookFileDownloadLog whereUserId($value)
 * @mixin Eloquent
 */
class BookFileDownloadLog extends Model
{
	function book_file()
	{
		return $this->belongsTo('App\BookFile')->any();
	}
}
