<?php

namespace App;

use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Carbon;

/**
 * App\BookTextProcessing
 *
 * @property int $id
 * @property int $book_id ID обрабатываемой книги
 * @property int $create_user_id ID пользователя создавшего обработку
 * @property bool $remove_bold Удалить "жирное" выделение во всем тексте. Будут удалены теги b, strong
 * @property bool $remove_extra_spaces Убрать лишние пробелы перед текстом внутри параграфов
 * @property bool $split_into_chapters Попробовать разбить тексты на главы. Главы разбваются если в тексте будут найдены параграфы с текстом "Глава (номер главы)", "Эпилог", "Предисловие"
 * @property bool $convert_new_lines_to_paragraphs Попробовать разбить тексты на главы. Главы разбваются если в тексте будут найдены параграфы с текстом "Глава (номер главы)", "Эпилог", "Предисловие"
 * @property bool $add_a_space_after_the_first_hyphen_in_the_paragraph Попробовать разбить тексты на главы. Главы разбваются если в тексте будут найдены параграфы с текстом "Глава (номер главы)", "Эпилог", "Предисловие"
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $started_at Время начала обработки
 * @property Carbon|null $completed_at Время окончания обработки
 * @property bool $remove_italics Удалить "жирное" выделение во всем тексте. Будут удалены теги b, strong
 * @property bool $remove_spaces_before_punctuations_marks Убрать лишние пробелы перед текстом внутри параграфов
 * @property bool $add_spaces_after_punctuations_marks Убрать лишние пробелы перед текстом внутри параграфов
 * @property bool $merge_paragraphs_if_there_is_no_dot_at_the_end Слить параграфы, если в конце текста параграфа нет точки. Например: "<p>Текст текст</p><p> текст текст.</p><p>Текст текст.</p>" станет таким: "<p>Текст текст текст текст.</p><p>Текст текст.</p>"
 * @property bool $tidy_chapter_names Сделать аккуратными названия глав. Например: "ГЛАВА   1" будет приведено в "Глава 1"
 * @property bool $remove_empty_paragraphs Удалить "жирное" выделение во всем тексте. Будут удалены теги b, strong
 * @property-read \App\Book $book
 * @property-read \App\User $create_user
 * @method static Builder|BookTextProcessing newModelQuery()
 * @method static Builder|BookTextProcessing newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookTextProcessing query()
 * @method static Builder|Model void()
 * @method static Builder|BookTextProcessing waited()
 * @method static Builder|BookTextProcessing whereAddASpaceAfterTheFirstHyphenInTheParagraph($value)
 * @method static Builder|BookTextProcessing whereAddSpacesAfterPunctuationsMarks($value)
 * @method static Builder|BookTextProcessing whereBookId($value)
 * @method static Builder|BookTextProcessing whereCompletedAt($value)
 * @method static Builder|BookTextProcessing whereConvertNewLinesToParagraphs($value)
 * @method static Builder|BookTextProcessing whereCreateUserId($value)
 * @method static Builder|BookTextProcessing whereCreatedAt($value)
 * @method static Builder|BookTextProcessing whereCreator(\App\User $user)
 * @method static Builder|BookTextProcessing whereId($value)
 * @method static Builder|BookTextProcessing whereMergeParagraphsIfThereIsNoDotAtTheEnd($value)
 * @method static Builder|BookTextProcessing whereRemoveBold($value)
 * @method static Builder|BookTextProcessing whereRemoveEmptyParagraphs($value)
 * @method static Builder|BookTextProcessing whereRemoveExtraSpaces($value)
 * @method static Builder|BookTextProcessing whereRemoveItalics($value)
 * @method static Builder|BookTextProcessing whereRemoveSpacesBeforePunctuationsMarks($value)
 * @method static Builder|BookTextProcessing whereSplitIntoChapters($value)
 * @method static Builder|BookTextProcessing whereStartedAt($value)
 * @method static Builder|BookTextProcessing whereTidyChapterNames($value)
 * @method static Builder|BookTextProcessing whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BookTextProcessing extends Model
{
    use UserCreate;

    protected $casts = [
        'options' => 'array'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'started_at',
        'completed_at'
    ];

    protected $fillable = [
        'remove_bold',
        'remove_extra_spaces',
        'split_into_chapters',
        'convert_new_lines_to_paragraphs',
        'add_a_space_after_the_first_hyphen_in_the_paragraph',
        'remove_italics',
        'remove_spaces_before_punctuations_marks',
        'add_spaces_after_punctuations_marks',
        'tidy_chapter_names',
        'remove_empty_paragraphs',
        'merge_paragraphs_if_there_is_no_dot_at_the_end'
    ];

    public function book()
    {
        return $this->belongsTo('App\Book');
    }

    public function setOption($key, $value)
    {
        if (!empty($key)) {
            $arr = (array)$this->options ?? [];
            $arr[$key] = $value;
            $this->options = $arr;
        }
    }

    public function getOption($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        } else {
            return null;
        }
    }

    public function scopeWaited($query)
    {
        return $query->whereNull('started_at')
            ->whereNull('completed_at');
    }

    public function isWait()
    {
        if (empty($this->started_at) and empty($this->completed_at)) {
            return true;
        } else {
            return false;
        }
    }

    public function isStarted()
    {
        if (!empty($this->started_at) and empty($this->completed_at)) {
            return true;
        } else {
            return false;
        }
    }

    public function isCompleted()
    {
        return (boolean)$this->completed_at;
    }

    public function start()
    {
        $this->started_at = now();
    }

    public function complete()
    {
        $this->completed_at = now();
    }

    public function wait()
    {
        $this->started_at = null;
        $this->completed_at = null;
    }
}