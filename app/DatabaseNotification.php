<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Support\Carbon;

/**
 * App\DatabaseNotification
 *
 * @property string $id
 * @property string $type
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property array $data
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $notifiable
 * @method static DatabaseNotificationCollection|static[] all($columns = ['*'])
 * @method static DatabaseNotificationCollection|static[] get($columns = ['*'])
 * @method static Builder|DatabaseNotification newModelQuery()
 * @method static Builder|DatabaseNotification newQuery()
 * @method static Builder|DatabaseNotification query()
 * @method static Builder|DatabaseNotification whereCreatedAt($value)
 * @method static Builder|DatabaseNotification whereData($value)
 * @method static Builder|DatabaseNotification whereId($value)
 * @method static Builder|DatabaseNotification whereNotifiableId($value)
 * @method static Builder|DatabaseNotification whereNotifiableType($value)
 * @method static Builder|DatabaseNotification whereReadAt($value)
 * @method static Builder|DatabaseNotification whereType($value)
 * @method static Builder|DatabaseNotification whereUpdatedAt($value)
 * @mixin Eloquent
 * @method static Builder|DatabaseNotification read()
 * @method static Builder|DatabaseNotification unread()
 */
class DatabaseNotification extends \Illuminate\Notifications\DatabaseNotification
{
    use HasFactory;
}
