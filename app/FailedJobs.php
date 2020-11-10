<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\FailedJobs
 *
 * @property int $id
 * @property string $connection
 * @property string $queue
 * @property string $payload
 * @property string $exception
 * @property string $failed_at
 * @property-read Book|null $book
 * @method static Builder|FailedJobs inBook($bookId)
 * @method static Builder|FailedJobs newModelQuery()
 * @method static Builder|FailedJobs newQuery()
 * @method static Builder|FailedJobs query()
 * @method static Builder|FailedJobs whereConnection($value)
 * @method static Builder|FailedJobs whereException($value)
 * @method static Builder|FailedJobs whereFailedAt($value)
 * @method static Builder|FailedJobs whereId($value)
 * @method static Builder|FailedJobs wherePayload($value)
 * @method static Builder|FailedJobs whereQueue($value)
 * @mixin Eloquent
 */
class FailedJobs extends Authenticatable
{

    protected $table = 'failed_jobs';

    function __construct()
    {
        $this->preg = preg_quote('\"App' . chr(92) . '\Book\";s:2:\"id\";i:') . '([0-9]+);';
    }

    // приходится использовать вот такой костыльный костыль чтобы найти запись которая принадлежит определенной книге

    public function scopeInBook($query, $bookId)
    {
        //return $query->whereRaw('"payload" ~* ' . "'" . preg_quote($s) . "'");
        return $query->whereRaw("substring(\"payload\" from '" . $this->preg . "') = '" . $bookId . "'");
    }

    public function book()
    {
        return $this->hasOne('App\Book')->whereRaw("substring(\"payload\" from '" . $this->preg . "') = book.b_id");
    }

}
