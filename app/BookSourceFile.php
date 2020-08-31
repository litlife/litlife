<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\BookSourceFile
 *
 * @property int $book_file_id
 * @property string|null $source_file_name
 * @property mixed|null $error
 * @property int|null $failed_job_id
 * @method static Builder|BookSourceFile newModelQuery()
 * @method static Builder|BookSourceFile newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookSourceFile query()
 * @method static Builder|Model void()
 * @method static Builder|BookSourceFile whereBookFileId($value)
 * @method static Builder|BookSourceFile whereError($value)
 * @method static Builder|BookSourceFile whereFailedJobId($value)
 * @method static Builder|BookSourceFile whereSourceFileName($value)
 * @mixin Eloquent
 */
class BookSourceFile extends Model
{

}
