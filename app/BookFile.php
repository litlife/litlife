<?php

namespace App;

use App\Enums\CacheTags;
use App\Enums\StatusEnum;
use App\Scopes\CheckedScope;
use App\Traits\CheckedItems;
use App\Traits\Storable;
use App\Traits\UserCreate;
use Eloquent;
use Emgag\Flysystem\Hash\HashPlugin;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Litlife\Url\Url;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\BookFile
 *
 * @property int $book_id
 * @property string $name
 * @property int $size
 * @property string $format
 * @property int $file_size
 * @property int|null $old_add_time
 * @property int|null $create_user_id
 * @property string $md5
 * @property bool $original
 * @property int $id
 * @property int $old_hide
 * @property int $old_hide_time
 * @property int $old_hide_user
 * @property int $old_version
 * @property int $download_count
 * @property int $old_download_count_update_time
 * @property string|null $comment
 * @property int|null $number
 * @property int|null $old_edit_time
 * @property int|null $old_edit_user
 * @property int|null $old_name_change
 * @property int|null $old_action
 * @property mixed|null $old_error
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $old_accepted_at
 * @property string|null $old_sent_for_review_at
 * @property string $storage
 * @property string|null $dirname
 * @property bool $source
 * @property int|null $check_user_id ID пользователя который проверил
 * @property int|null $status
 * @property string|null $status_changed_at
 * @property int|null $status_changed_user_id
 * @property string|null $old_rejected_at
 * @property bool|null $auto_created Создан ли файл сайтом, после редактирования онлайн текста
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Activity[] $activities
 * @property-read \App\User|null $add_user
 * @property-read \App\Book $book
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Complain[] $complaints
 * @property-read \App\User|null $create_user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookFileDownloadLog[] $download_logs
 * @property-read mixed $encoded_name
 * @property mixed $extension
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read mixed $path_file
 * @property-read mixed $url
 * @property-write mixed $show_status
 * @property-read \App\User|null $status_changed_user
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile acceptedAndSentForReview()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile acceptedOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile acceptedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile any()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile anyNotTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile automaticCreation()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile checked()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile checkedAndOnCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile checkedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile onCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile onlyChecked()
 * @method static Builder|BookFile onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile orderStatusChangedAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile orderStatusChangedDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile private ()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile sentOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile unaccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile unchecked()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereAutoCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereCheckUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereDirname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereDownloadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereMd5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOldAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOldAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOldAddTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOldDownloadCountUpdateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOldEditTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOldEditUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOldError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOldHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOldHideTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOldHideUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOldNameChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOldRejectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOldSentForReviewAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOldVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereStatusChangedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereStatusIn($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereStatusNot($status)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereStorage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereUpdatedAt($value)
 * @method static Builder|BookFile withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile withUnchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|BookFile withoutCheckedScope()
 * @method static Builder|BookFile withoutTrashed()
 * @mixin Eloquent
 */
class BookFile extends Model
{
	use SoftDeletes;
	use UserCreate;
	use CheckedItems;
	use LogsActivity;
	use Storable;

	protected static $recordEvents = [];

	protected static $logOnlyDirty = true;

	protected static $submitEmptyLogs = false;

	public $folder = '_bf';

	public $zip = false;

	public $stream;

	protected $fillable = [
		'comment',
		'number'
	];

	protected $casts = [
		'error' => 'object',
		'auto_created' => 'boolean'
	];

	protected $attributes =
		[
			'status' => StatusEnum::Private
		];

	public static function boot()
	{
		parent::boot();

		//static::addGlobalScope(new CheckedScope);
		//static::addGlobalScope(new NotConnectedScope);
	}

	static function getCachedOnModerationCount()
	{
		return Cache::tags([CacheTags::BookFilesOnModerationCount])->remember('count', 3600, function () {
			return self::sentOnReview()->count();
		});
	}

	static function flushCachedOnModerationCount()
	{
		Cache::tags([CacheTags::BookFilesOnModerationCount])->pull('count');
	}

	protected static function bootStorable()
	{
		static::creating(function ($query) {
			if (empty($query->attributes['storage']))
				$query->attributes['storage'] = config('litlife.disk_for_files');
		});
	}

	public function scopeAny($query)
	{
		return $query->withoutGlobalScope(CheckedScope::class)->withTrashed();
	}

	public function scopeAnyNotTrashed($query)
	{
		return $query->withoutGlobalScope(CheckedScope::class);
	}

	function book()
	{
		return $this->belongsTo('App\Book')->any();
	}

	function add_user()
	{
		return $this->hasOne('App\User', 'id', 'create_user_id');
	}

	public function setShowStatusAttribute($statusName)
	{

	}

	public function complaints()
	{
		return $this->morphMany('App\Complain', 'complainable');
	}

	public function setFormatAttribute($format)
	{
		$this->attributes['format'] = mb_strtolower($format);
	}

	public function getPathFileAttribute()
	{
		return getPath($this->book_id) . '/' . $this->folder . '/' . $this->name;
	}

	public function open($source, $extension = null)
	{
		if (is_string($source) and is_file($source)) {
			$source = Url::fromString($source);

			$fileSystem = new Filesystem(new Local($source->getDirname()));
			$fileSystem->addPlugin(new HashPlugin);
			$mimeType = $fileSystem->getMimetype($source->getBasename());
			$basename = $source->getBasename();

			if (isset($extension))
				$this->extension = $extension;

			if ($source->getExtension() != 'zip') {
				if (empty($this->extension) and !empty($source->getExtension()))
					$this->extension = $source->getExtension();
			}

			if ($mimeType == 'application/zip') {

				$zipFileSystem = new Filesystem(new ZipArchiveAdapter((string)$source));
				$zipFileSystem->addPlugin(new HashPlugin);

				$isEpub = transform($zipFileSystem->listContents(), function ($files) use ($zipFileSystem) {

					$zipArchiveInstance = $zipFileSystem->getAdapter()->getArchive();

					foreach ($files as $file) {

						if ($file['path'] == 'mimetype') {
							$stream = $zipArchiveInstance->getStream($file['path']);

							try {
								$contents = stream_get_contents($stream, -1, 0);

								if ($contents == 'application/epub+zip') {
									return true;
								}

							} catch (\ErrorException $exception) {
								break;
							}
						}
					}
				});

				if ($isEpub) {
					$this->stream = $fileSystem->readStream($basename);
					$this->extension = 'epub';
				} else {
					$file = transform($zipFileSystem->listContents(), function ($files) use ($zipFileSystem) {
						foreach ($files as $file) {
							$extension = $file['extension'];
							if (in_array($extension, config('litlife.book_allowed_file_extensions')))
								return $file;
							else
								return false;
						}
					});

					if (empty($file))
						throw new Exception(__('book_file.no_supported_files_found_in_the_archive'));

					$this->extension = $file['extension'];
					$this->stream = $zipFileSystem->readStream($file['path']);
					//$this->file_size = $fileSystem->getSize($file['path']);
					//$this->md5 = $fileSystem->hash($file['path'], 'md5');
					$this->name = $file['basename'];
				}

			} else {
				$this->stream = $fileSystem->readStream($basename);
				//$this->file_size = $fileSystem->getSize($basename);
				//$this->md5 = $fileSystem->hash($basename, 'md5');
			}

			if (empty($this->extension)) {
				foreach (config('litlife.allowed_mime_types') as $extension => $mimeTypes) {
					if (in_array($mimeType, (array)$mimeTypes))
						$this->extension = $extension;
				}
			}

		} elseif (is_resource($source)) {

			$this->stream = &$source;
			$this->extension = $extension;
			/*
						$contents = stream_get_contents($this->stream, -1, 0);
						$this->md5 = md5($contents);
						$this->file_size = strlen($contents);
						*/
		} else {
			throw new Exception('File or resource not found');
		}
	}

	public function getExtensionAttribute()
	{
		return mb_strtolower($this->format);
	}

	public function setExtensionAttribute($extension)
	{
		$extension = mb_strtolower($extension);

		if (!in_array($extension, config('litlife.book_allowed_file_extensions')))
			throw new Exception('Расширение ' . $extension . ' не поддерживается');

		$this->attributes['format'] = $extension;
	}

	public function isSource()
	{
		return (bool)$this->source;
	}

	public function getEncodedNameAttribute()
	{
		return rawurlencode($this->name);
	}

	public function updateFileName()
	{
		if ($this->isZipArchive())
			$name = $this->generateFileNameForArichive();
		else
			$name = $this->generateFileName();

		$this->rename($name);
	}

	public function generateFileNameForArichive()
	{
		$name = $this->book->getNameForBookFile() . '_' . Str::random(5);

		$name = Url::fromString(empty($name) ? 'file' : $name)
			->withExtension($this->format)
			->getBasename();

		return fileNameFormat(Str::finish($name, '.zip'));
	}

	public function generateFileName()
	{
		$name = $this->book->getNameForBookFile() . '_' . Str::random(5);

		$name = Url::fromString(empty($name) ? 'file' : $name)
			->withExtension($this->format)
			->getBasename();

		return fileNameFormat($name);
	}

	public function generateDirName()
	{
		return getPath($this->book->id) . '/' . $this->folder;
	}

	public function generateFileNameForFileInsideArichive()
	{
		$name = $this->book->getNameForBookFile();

		$name = Url::fromString(empty($name) ? 'file' : $name)
			->withExtension($this->format)
			->getBasename();

		return fileNameFormat($name);
	}

	public function isShouldBeArchived()
	{
		return ($this->zip and !in_array($this->format, config('litlife.not_zip_extensions')));
	}

	public function purgeDownloadLogs()
	{
		$this->download_logs()->delete();
	}

	function download_logs()
	{
		return $this->hasMany('App\BookFileDownloadLog');
	}

	public function refreshDownloadCount()
	{
		$this->download_count = $this->download_logs()->count();
	}

	public function sentParsePages()
	{
		if (!$this->canParsed())
			return false;

		if (!$this->book->parse->isReseted() and !$this->book->parse->isFailed() and !$this->book->parse->isSucceed())
			return false;

		$this->source = true;
		$this->save();

		$this->book->files()
			->where('id', '!=', $this->id)
			->update(['source' => false]);

		$parse = new BookParse();
		$parse->book()->associate($this->book);
		$parse->associateAuthUser();
		$parse->wait();
		$parse->parseOnlyPages();
		$parse->save();

		$this->book->needCreateNewBookFilesDisable();
		$this->book->save();

		return true;
	}

	public function canParsed()
	{
		return (boolean)collect(config('litlife.book_allowed_file_extensions'))
			->diff(config('litlife.no_need_convert'))
			->contains($this->extension);
	}

	public function isAutoCreated()
	{
		return (boolean)$this->auto_created;
	}

	public function scopeAutomaticCreation($query)
	{
		return $query->where('auto_created', true);
	}
}
