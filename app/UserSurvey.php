<?php

namespace App;

use App\Traits\UserCreate;
use Illuminate\Database\Eloquent\Model;

/**
 * App\UserSurvey
 *
 * @property int $id
 * @property int $create_user_id
 * @property string|null $do_you_read_books_or_download_them Вы читаете книги или скачиваете?
 * @property array|null $what_file_formats_do_you_download Если вы скачиваете книги, то какие форматы вы предпочитаете? (можно выбрать несколько вариантов)
 * @property string|null $how_improve_download_book_files Напишите, как можно улучшить файлы книг
 * @property int|null $how_do_you_rate_the_convenience_of_reading_books_online Если вы читаете книги онлайн, то как вы оцениваете удобство чтения книг онлайн?
 * @property string|null $how_to_improve_the_convenience_of_reading_books_online Напишите, как можно улучшить удобство чтения книг онлайн
 * @property int|null $how_do_you_rate_the_convenience_and_functionality_of_search Как вы оцениваете удобство и функциональность поиска книг, авторов?
 * @property string|null $how_to_improve_the_convenience_of_searching_for_books Напишите, как можно улучшить удобство поиска книг и авторов
 * @property int|null $how_do_you_rate_the_site_design Как вы оцениваете дизайн сайта?
 * @property string|null $how_to_improve_the_site_design Напишите, как можно улучшить дизайн сайта
 * @property int|null $how_do_you_assess_the_work_of_the_site_administration Как вы оцениваете работу администрации сайта?
 * @property string|null $how_improve_the_site_administration Напишите, как можно улучшить работу администрации сайта
 * @property string|null $what_do_you_like_on_the_site Напишите, что вам нравится на нашем сайте
 * @property string|null $what_you_dont_like_about_the_site Напишите, что вам не нравится на нашем сайте
 * @property string|null $what_you_need_on_our_site Напишите, чего вам не хватает на нашем сайте
 * @property array|null $what_site_features_are_interesting_to_you Какие функции сайта вам интересны? (можно выбрать несколько вариантов)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $create_user
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereDoYouReadBooksOrDownloadThem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowDoYouAssessTheWorkOfTheSiteAdministration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowDoYouRateTheConvenienceAndFunctionalityOfSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowDoYouRateTheConvenienceOfReadingBooksOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowDoYouRateTheSiteDesign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowImproveDownloadBookFiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowImproveTheSiteAdministration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowToImproveTheConvenienceOfReadingBooksOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowToImproveTheConvenienceOfSearchingForBooks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowToImproveTheSiteDesign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereWhatDoYouLikeOnTheSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereWhatFileFormatsDoYouDownload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereWhatSiteFeaturesAreInterestingToYou($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereWhatYouDontLikeAboutTheSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereWhatYouNeedOnOurSite($value)
 * @mixin \Eloquent
 */
class UserSurvey extends Model
{
	use UserCreate;

	protected $casts = [
		'answers' => 'json',
		'what_file_formats_do_you_download' => 'json',
		'what_site_features_are_interesting_to_you' => 'json'
	];

	private $timestamp = [
		'created_at',
		'updated_at'
	];

	public $fillable = [
		'do_you_read_books_or_download_them',
		'what_file_formats_do_you_download',
		'how_improve_download_book_files',
		'how_do_you_rate_the_convenience_of_reading_books_online',
		'how_to_improve_the_convenience_of_reading_books_online',
		'how_do_you_rate_the_convenience_and_functionality_of_search',
		'how_to_improve_the_convenience_of_searching_for_books',
		'how_do_you_rate_the_site_design',
		'how_to_improve_the_site_design',
		'how_do_you_assess_the_work_of_the_site_administration',
		'how_improve_the_site_administration',
		'what_do_you_like_on_the_site',
		'what_you_dont_like_about_the_site',
		'what_you_need_on_our_site',
		'what_site_features_are_interesting_to_you'
	];
}
