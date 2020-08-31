<?php

namespace App;

use App\Traits\Cachable;
use Eloquent;
use GeneaLabs\LaravelModelCaching\CachedBuilder;
use Illuminate\Database\Eloquent\Builder;


/**
 * App\UserReadStyle
 *
 * @property int $user_id
 * @property int|null $font
 * @property int|null $align
 * @property int|null $size
 * @property string|null $background_color
 * @property string|null $font_color
 * @property string|null $card_color
 * @property-read \App\User $user
 * @method static Builder|UserReadStyle disableCache()
 * @method static CachedBuilder|UserReadStyle newModelQuery()
 * @method static CachedBuilder|UserReadStyle newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static CachedBuilder|UserReadStyle query()
 * @method static Builder|Model void()
 * @method static Builder|UserReadStyle whereAlign($value)
 * @method static Builder|UserReadStyle whereBackgroundColor($value)
 * @method static Builder|UserReadStyle whereCardColor($value)
 * @method static Builder|UserReadStyle whereFont($value)
 * @method static Builder|UserReadStyle whereFontColor($value)
 * @method static Builder|UserReadStyle whereSize($value)
 * @method static Builder|UserReadStyle whereUserId($value)
 * @method static Builder|UserReadStyle withCacheCooldownSeconds($seconds = null)
 * @mixin Eloquent
 */
class UserReadStyle extends Model
{
	use Cachable;

	public $timestamps = false;
	public $primaryKey = 'user_id';
	public $incrementing = false;

	protected $attributes = [
		'font' => 1,
		'align' => 4,
		'size' => 18,
		'background_color' => 'eeeeee',
		'card_color' => 'ffffff',
		'font_color' => '000000'
	];

	protected $fillable = [
		'font',
		'align',
		'size',
		'background_color',
		'card_color',
		'font_color',
		'user_id'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'user_id', 'id');
	}

	public function setFontAttribute($value)
	{
		foreach (config('litlife.read_allowed_fonts') as $index => $font) {
			if ($value == $font) {
				$this->attributes['font'] = $index;
			}
		}
	}

	public function getFontAttribute($value)
	{
		foreach (config('litlife.read_allowed_fonts') as $index => $font) {
			if ($value == $index) {
				return $font;
			}
		}
	}

	public function setAlignAttribute($value)
	{
		foreach (config('litlife.read_text_align') as $index => $align) {
			if ($value == $align) {
				$this->attributes['align'] = $index;
			}
		}
	}

	public function getAlignAttribute($value)
	{
		foreach (config('litlife.read_text_align') as $index => $align) {
			if ($value == $index) {
				return $align;
			}
		}
	}

	public function getSizeAttribute($value)
	{
		if (!empty($value))
			return $value;
	}

	public function setBackgroundColorAttribute($value)
	{
		$this->attributes['background_color'] = ltrim($value, '#');
	}

	public function setFontColorAttribute($value)
	{
		$this->attributes['font_color'] = ltrim($value, '#');
	}

	public function getBackgroundColorAttribute($value)
	{
		$value = $this->sanitizeHexColor($value);

		if (!empty($value))
			return mb_strtolower('#' . $value);
		else
			return '#eeeeee';
	}

	private function sanitizeHexColor($color)
	{
		if ('' === $color) {
			return '';
		}

		// 3 or 6 hex digits, or the empty string.
		if (preg_match('/^([A-Fa-f0-9]{3}){1,2}$/iu', $color)) {
			return $color;
		}

		return '';
	}

	public function getFontColorAttribute($value)
	{
		$value = $this->sanitizeHexColor($value);

		if (!empty($value))
			return mb_strtolower('#' . $value);
		else
			return '#000000';
	}

	public function setCardColorAttribute($value)
	{
		$this->attributes['card_color'] = ltrim($value, '#');
	}

	public function getCardColorAttribute($value)
	{
		$value = $this->sanitizeHexColor($value);

		if (!empty($value))
			return mb_strtolower('#' . $value);
		else
			return '#ffffff';
	}
}
