<?php

namespace App;

use App\Enums\StatusEnum;
use App\Model as Model;
use App\Traits\CheckedItems;
use App\Traits\LatestOldestWithIDTrait;
use App\Traits\UserCreate;
use DOMDocument;
use DOMXPath;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Kalnoy\Nestedset\NodeTrait;
use Kalnoy\Nestedset\QueryBuilder;
use Litlife\Url\Url;
use Stevebauman\Purify\Facades\Purify;
use tidy;

// use IgnorableObservers\IgnorableObservers;

/**
 * App\Section
 *
 * @property int $id
 * @property int $inner_id
 * @property string $type
 * @property int $book_id
 * @property string $title
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $_lft
 * @property int $_rgt
 * @property int|null $parent_id
 * @property int|null $character_count
 * @property Carbon|null $user_edited_at Время когда пользователь отредактировал
 * @property array|null $parameters
 * @property int $pages_count
 * @property int $status Статус главы. Пока будут варианты опубликована и в личном доступе или черновик
 * @property string|null $status_changed_at Дата изменения статуса
 * @property int|null $status_changed_user_id Пользователь последний изменивший статус
 * @property-read \App\Book $book
 * @property-read \Kalnoy\Nestedset\Collection|Section[] $children
 * @property-read \App\User $create_user
 * @property mixed $characters_count
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Page[] $pages
 * @property-read Section|null $parent
 * @property-write mixed $content
 * @property-write mixed $element_id
 * @property-read \App\User|null $status_changed_user
 * @method static \Illuminate\Database\Eloquent\Builder|Section accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Section acceptedAndSentForReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Section acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Section acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Section acceptedOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Section acceptedOrBelongsToUser($user)
 * @method static \Kalnoy\Nestedset\Collection|static[] all($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Section anchorSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section any()
 * @method static \Illuminate\Database\Eloquent\Builder|Section chapter()
 * @method static \Illuminate\Database\Eloquent\Builder|Section chaptersOrNotes()
 * @method static \Illuminate\Database\Eloquent\Builder|Section checked()
 * @method static \Illuminate\Database\Eloquent\Builder|Section checkedAndOnCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Section checkedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Section d()
 * @method static \Illuminate\Database\Eloquent\Builder|Section findInnerIdOrFail($innerId)
 * @method static \Illuminate\Database\Eloquent\Builder|Section fulltextSearch($searchText)
 * @method static \Kalnoy\Nestedset\Collection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Section latestWithId($column = 'created_at')
 * @method static QueryBuilder|Section newModelQuery()
 * @method static QueryBuilder|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section notes()
 * @method static \Illuminate\Database\Eloquent\Builder|Section oldestWithId($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|Section onCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Section onlyChecked()
 * @method static Builder|Section onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Section orderStatusChangedAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|Section orderStatusChangedDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|Section parametersIn($var, $array)
 * @method static \Illuminate\Database\Eloquent\Builder|Section private()
 * @method static QueryBuilder|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder|Section sentOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Section unaccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Section unchecked()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCharacterCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereInnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereLft($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section wherePagesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereRgt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereStatusChangedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereStatusIn($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereStatusNot($status)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereUserEditedAt($value)
 * @method static Builder|Section withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Section withUnchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|Section withoutCheckedScope()
 * @method static Builder|Section withoutTrashed()
 * @mixin Eloquent
 */
class Section extends Model
{
    use SoftDeletes;
    use NodeTrait;
    use UserCreate;
    use LatestOldestWithIDTrait;
    use CheckedItems;

    public $prefix = "u-";
    public $pagesAfterSplitter;
    public $dom;
    public $xpath;
    public $contentChanged = false;
    protected $attributes = array(
        'type' => 'section',
        'status' => StatusEnum::Accepted
    );
    protected $casts = [
        'html_tags_ids' => 'array',
        'parameters' => 'array'
    ];
    protected $dates = [
        'user_edited_at'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        '_lft',
        '_rgt'
    ];

    public function scopeAny($query)
    {
        return $query->withTrashed();
    }

    public function book()
    {
        return $this->belongsTo('App\Book')->any();
    }

    public function pages()
    {
        return $this->hasMany('App\Page')
            ->orderBy('page', 'asc');
    }

    /*
        public function links_to()
        {
            return $this->hasMany('App\Anchor', 'link_to_section', 'inner_id')
                ->where('anchors.book_id', '=', $this->book_id);
        }

            public function anchors()
            {
                return $this->hasMany('App\Anchor', 'section_id', 'inner_id')
                    ->where('anchors.book_id', '=', $this->book_id);
            }
            */

    public function scopeAnchorSearch($query, $value)
    {
        $s = "content ~* '\<a\ " . '(.*)href(.*)=(.*)\"\#?\"(.*)\>' . "(.*)" . "\<\/a\>'";

        return $query->whereRaw($s, [preg_quote($value)]);
    }

    /*
        public function scopeInLinksIdSections($query, $array)
        {
            $array = (array)$array;

            return $query->whereRaw('jsonb_exists_any("links_id_sections", array[?, ?])', [$array]);
        }


        public function scopeIdSearch($query, $value)
        {
            $s = "content ~* '\<([A-z]+)\ " . '(.*)id(.*)=(.*)(\")' . preg_quote($value) . '\"' . "(.*)\>'";

            return $query->whereRaw($s)->orWhere('element_id', $value);
        }
    */
    public function scopeFulltextSearch($query, $searchText)
    {
        /*
        $Ar = preg_split("/[\s,[:punct:]]+/", $searchText, 0, PREG_SPLIT_NO_EMPTY);

        $s = '';

        if ($Ar) {
            $s = "to_tsvector('english', \"last_name\" || ' ' || \"first_name\" || ' ' || \"middle_name\" || ' ' || \"nickname\" )  ";
            $s .= " @@ to_tsquery('english', quote_literal(quote_literal(?)))";

            return $query->whereRaw($s, [implode('&', $Ar)]);
        }
        */
    }

    public function scopefindInnerIdOrFail($query, $innerId)
    {
        $result = $query->where('inner_id', $innerId)->first();

        if (!is_null($result)) {
            return $result;
        }

        throw (new ModelNotFoundException())->setModel(
            get_class($this), $innerId
        );
    }

    public function scopeParametersIn($query, $var, $array)
    {
        $array = (array)$array;

        return $query->where(function ($query) use ($var, $array) {
            foreach ($array as $value) {
                $query->orWhereRaw('"parameters"::jsonb @> ?', [json_encode([$var => $value])]);
            }
        });
    }

    public function setTypeAttribute($value)
    {
        $value = trim($value);

        if (in_array($value, ['section', 'note', 'annotation'])) {
            $this->attributes['type'] = $value;
        } else {
            throw new Exception('Wrong section type');
        }
    }

    public function setTitleAttribute($value)
    {
        $value = strip_tags($value);
        $value = replaceAsc194toAsc32($value);
        $value = preg_replace('/([[:space:]]+)/iu', ' ', $value);
        $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
        $value = trim($value);

        if (mb_strlen($value) > 100) {
            $value = mb_substr($value, 0, 100);
        }

        $this->attributes['title'] = trim($value);
    }

    public function setElementIdAttribute($value)
    {
        $this->addParameter('section_id', appendPrefix($this->prefix, $value));
    }

    public function addParameter($key, $value)
    {
        if (!empty($key)) {
            $arr = $this->parameters ?? [];
            $arr[$key] = $value;
            $this->parameters = $arr;
        }
    }

    public function getSectionId()
    {
        return $this->getParameter('section_id');
    }

    public function getParameter($key)
    {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        } else {
            return null;
        }
    }

    public function getTitleId()
    {
        return $this->getParameter('title_id');
    }

    public function getContentHandeled()
    {
        $content = '';

        foreach ($this->pages->sortBy('page') as $page) {
            $content .= $page->content_handled;
        }

        return $content;
    }

    public function isDomSet()
    {
        return isset($this->dom);
    }

    public function isContentChanged()
    {
        return $this->contentChanged;
    }

    public function setContentAttribute($value)
    {
        $this->contentChanged();

        $value = trim($value);

        $value = $this->tidy($value);

        $value = $this->purify($value);

        $value = str_replace("\n", '', $value);

        libxml_use_internal_errors(true);

        $this->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8"><body>' . $value . '</body>');
        $this->xpath(true);

        $body = $this->dom()->getElementsByTagName('body')->item(0);

        $parentNode = $body;

        if (isset($parentNode)) {

            while ($this->isChildTagOnlyOne($parentNode)) {
                $parentNode = $this->getFirstTag($parentNode);
            }
        }

        $new_body = $this->dom()->createElement('body');

        $childNodes = $this->xpath()->query('child::node()', $parentNode);

        foreach ($childNodes as $childNode) {

            $new_body->appendChild($childNode);
        }

        $this->dom()->documentElement->replaceChild($new_body, $body);

        $body = $this->dom()->getElementsByTagName('body')->item(0);

        if (!$this->isAnnotation()) {
            if (empty($this->attributes['title'])) {
                $this->autoTitleIfEmpty();
            }
        }

        // добавляем префиксы к id

        $nodeList = $this->xpath()->query("//*[@id]", $body);

        if ($nodeList->length) {

            foreach ($nodeList as $node) {

                $id = $node->getAttribute("id");

                $id = appendPrefix($this->prefix, $id);

                $node->setAttribute("id", $id);

                // добавляем префикс так же к аттрибуту name
                if ($node->hasAttribute("name")) {
                    $id = $node->getAttribute("name");

                    $id = appendPrefix($this->prefix, $id);

                    $node->setAttribute("name", $id);
                }
            }
        }

        // добавляем префиксы к классам

        $nodeList = $this->xpath()->query("//*[@class]", $body);

        if ($nodeList->length) {

            foreach ($nodeList as $node) {
                $class = $node->getAttribute("class");

                $class = appendPrefix($this->prefix, $class);

                $node->setAttribute("class", $class);
            }
        }

        $nodeList = $this->xpath()->query("//*[local-name()='a'][@href]", $body);

        if ($nodeList->length) {

            foreach ($nodeList as $node) {

                $url = Url::fromString($node->getAttribute("href"));

                if (empty($url->getHost())) {

                    $fragment = $url->getFragment();

                    if (!empty($fragment)) {
                        $fragment = appendPrefix(config('litlife.class_prefix'), $fragment);
                        $node->setAttribute("href", "#" . $fragment);
                    }
                }
            }
        }

        if (!$this->isAnnotation()) {
            $this->removeTitleFromText();
        }

        // подсчитываем количество символов
        $this->attributes['character_count'] = $this->getCharacterCountInText($value);

        //$this->pages_content = $value;
    }

    public function contentChanged()
    {
        $this->contentChanged = true;
    }

    public function tidy($html)
    {
        $tidy = new tidy();

        $config = [
            'clean' => false,
            'merge-divs' => false,
            'merge-spans' => true,
            'output-xhtml' => true,
            'css-prefix' => trim($this->prefix, '-'),
            'drop-empty-paras' => false,
            'join-styles' => true,
            'join-classes' => true,
            'repeated-attributes' => 'keep-last',
            'wrap-attributes' => false,
            'wrap' => 0,
            'anchor-as-name' => true,
            'indent-attributes' => false,
            'indent' => false
        ];

        $html = $tidy->repairString($html, $config);
        $html = $tidy->parseString($html, $config);

        $html = trim($tidy->Body()->value);
        $html = ltrim($html, '<body>');
        $html = rtrim($html, '</body>');
        $html = trim($html);

        return $html;
    }

    public function purify($value)
    {
        return Purify::clean($value, $this->getPurifyConfig());
    }

    public function getPurifyConfig()
    {
        $allowedClasses = [
            'epigraph',
            'text-author',
            'stanza',
            'subtitle',
            'poem',
            'title',
            'empty-line',
            'annotation',
            'v',
            'date',
            'image-align-right',
            'image-align-left',
            'image-align-center'
        ];

        foreach ($allowedClasses as $class) {
            $allowedClasses[] = appendPrefix($this->prefix, $class);
        }

        $configuration = [
            'Attr.EnableID' => true,
            'Core.Encoding' => 'utf-8',
            'HTML.Allowed' =>
                'h1,h2,h3,h4,h5,h6,' .
                '*[class|style|id],' .
                'p[style],strong,em,s,u,sub,sup,blockquote,b,i,u,br,div[class],span[style],' .
                'img[width|height|src|alt|style|class],' .
                'table[border|cellpadding|cellspacing|style],caption,tbody,thead,tr,th[style|colspan],td[style|colspan],tfoot,col[width|valign|align|span],colgroup[width|valign|align|span],' .
                'hr,dl,dt,dd,' .
                'a[href|name|target],' .
                'ol,li,ul,' .
                'pre,code',
            'CSS.AllowedProperties' => 'text-align,height,width,color,background-color,border-spacing,border-collapse',
            'Attr.IDPrefix' => config('litlife.class_prefix'),
            'Attr.AllowedClasses' => implode(',', $allowedClasses),
            'URI.Host' => parse_url(env('APP_URL', 'https://litlife.club'), PHP_URL_HOST),
            'URI.Munge' => '/away?url=%s',
            'AutoFormat.RemoveSpansWithoutAttributes' => true,
            'AutoFormat.AutoParagraph' => true
        ];

        return array_merge(config('purify.settings'), $configuration);
    }

    public function dom()
    {
        if (isset($this->dom)) {
            return $this->dom;
        } else {
            return $this->dom = new DOMDocument();
        }
    }

    public function xpath($fresh = false)
    {
        if (isset($this->xpath) and $fresh == false) {
            return $this->xpath;
        } else {
            return $this->xpath = new DOMXPath($this->dom());
        }
    }

    /**
     * Проверяет все узлы внутри тега и возвращает true если в нем только один тег и пустые текстовые узлы до и после
     */
    public function isChildTagOnlyOne($parentNode)
    {
        $elementCount = 0;
        $s = '';

        if (!isset($parentNode)) {
            return false;
        }

        if ($parentNode->childNodes->length < 1) {
            return false;
        }

        foreach ($parentNode->childNodes as $node) {

            switch ($node->nodeType) {
                case XML_ELEMENT_NODE:

                    if ($node->hasAttribute('id')) {
                        return false;
                    }

                    if (!in_array($node->nodeName, ["div", "body"])) {
                        return false;
                    }

                    if ($node->nodeName == 'div') {
                        if ($this->xpath()->query('child::*', $node)->length < 1) {
                            return false;
                        }
                    }

                    $elementCount++;

                    break;

                case XML_TEXT_NODE:

                    $s .= $node->nodeValue;

                    break;
            }
        }

        if ($elementCount < 1) {
            return false;
        }

        if (($elementCount > 1) and (trim(replaceAsc194toAsc32($s)))) {
            return false;
        }

        return true;
    }

    private function getFirstTag($parentNode)
    {
        foreach ($parentNode->childNodes as $node) {
            switch ($node->nodeType) {
                case XML_ELEMENT_NODE:

                    return $node;

                    break;
            }
        }
    }

    public function isAnnotation()
    {
        return $this->type == 'annotation';
    }

    public function autoTitleIfEmpty()
    {
        if (empty($this->attributes['title'])) {
            $body = $this->dom()->getElementsByTagName('body')->item(0);

            $firstNode = $body->childNodes->item(0);

            if (!empty($firstNode)) {
                if (
                    preg_match('/h([1-6]+)/iu', $firstNode->nodeName) or
                    $this->xpath()->query("//b", $firstNode)->count() or
                    $this->xpath()->query("//strong", $firstNode)->count()
                ) {
                    $value = trim(preg_replace('/([[:space:]]+)/iu', '', $firstNode->nodeValue));

                    if (!empty($value)) {
                        $this->title = $firstNode->nodeValue;
                    }
                }
            }
            /*
                        if (empty($this->title))
                        {
                            foreach ($body->childNodes as $node)
                            {
                                $value = trim(preg_replace('/([[:space:]]+)/iu', '', $node->nodeValue));

                                if (!empty($value))
                                {
                                    $this->title = $node->nodeValue;
                                    break;
                                }
                            }
                        }
                        */
        }
    }

    public function removeTitleFromText()
    {
        if ($this->title != '') {
            $body = $this->dom()->getElementsByTagName('body')->item(0);

            // пытаемся извлечь заголовок из тега h1 в body
            $titleNode = $this->xpath()->query("*[local-name()='h1'][@class='u-title']", $body);

            if ($titleNode->length) {
                $titleNode = $titleNode->item(0);

                if (preg_replace('/([[:space:]]+)/iu', '', $titleNode->nodeValue) == preg_replace('/([[:space:]]+)/iu', '', $this->title)) {
                    if ($id = $titleNode->getAttribute('id')) {
                        $this->setTitleId($id);
                    }

                    $titleNode->parentNode->removeChild($titleNode);
                    return true;
                }
            }

            $firstTag = $this->xpath()->query("*", $body)->item(0);

            if (!empty($firstTag)) {

                if (preg_match('/h([1-6]{1})/iu', $firstTag->nodeName)) {
                    if (preg_replace('/([[:space:]]+)/iu', '', $firstTag->nodeValue) == preg_replace('/([[:space:]]+)/iu', '', $this->title)) {
                        if ($id = $firstTag->getAttribute('id')) {
                            $this->setTitleId($id);
                        }

                        $firstTag->parentNode->removeChild($firstTag);
                        return true;
                    }
                }

                if ($firstTag->nodeName == 'div') {

                    $title = preg_replace('/\<(.*)\>/iuU', ' ', $this->dom()->saveXML($firstTag));
                    $title = preg_replace('/([[:space:]]+)/iu', '', $title);

                    if ($title == preg_replace('/([[:space:]]+)/iu', '', $this->title)) {
                        if ($id = $firstTag->getAttribute('id')) {
                            $this->setTitleId($id);
                        } elseif ($childTagWithId = $this->xpath()->query('//*[@id]', $firstTag)->item(0)) {
                            $this->setTitleId($childTagWithId->getAttribute('id'));
                        }

                        $firstTag->parentNode->removeChild($firstTag);
                        return true;
                    }
                }

                if ($firstTag->nodeName == 'p') {

                    $title = '';

                    foreach ($firstTag->childNodes as $node) {
                        if (in_array($node->nodeName, ['b', 'strong'])) {
                            $title .= $node->nodeValue . ' ';
                        }
                    }

                    $title = trim(preg_replace('/([[:space:]]+)/iu', '', $title));

                    if (!empty($title)) {
                        if ($title == preg_replace('/([[:space:]]+)/iu', '', $this->title)) {
                            if ($id = $firstTag->getAttribute('id')) {
                                $this->setTitleId($id);
                            } elseif ($childTagWithId = $this->xpath()->query('//*[@id]', $firstTag)->item(0)) {
                                $this->setTitleId($childTagWithId->getAttribute('id'));
                            }

                            $firstTag->parentNode->removeChild($firstTag);
                            return true;
                        }
                    }

                    $title = trim(preg_replace('/([[:space:]]+)/iu', '', $firstTag->nodeValue));

                    if (!empty($title)) {
                        if ($title == preg_replace('/([[:space:]]+)/iu', '', $this->title)) {
                            if ($id = $firstTag->getAttribute('id')) {
                                $this->setTitleId($id);
                            } elseif ($childTagWithId = $this->xpath()->query('//*[@id]', $firstTag)->item(0)) {
                                $this->setTitleId($childTagWithId->getAttribute('id'));
                            }

                            $firstTag->parentNode->removeChild($firstTag);
                            return true;
                        }
                    }
                }
            }
        }
    }

    public function setTitleId($value)
    {
        $this->addParameter('title_id', appendPrefix($this->prefix, $value));
    }

    public function getCharacterCountInText($text)
    {
        return transform($text, function ($text) {

            $text = strip_tags($text);

            $text = preg_replace("/[[:space:]]+/iu", "", $text);

            $text = mb_strlen($text);

            return $text;
        });
    }

    public function saveXML()
    {
        $body = $this->dom()->getElementsByTagName('body')->item(0);

        $content = '';

        foreach ($body->childNodes as $node) {
            $content .= $this->dom()->saveXML($node);
        }

        return $content;
    }

    public function loadDom()
    {
        if ($this->dom instanceof DOMDocument) {

        } else {
            $this->dom = new DOMDocument();
            $this->dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8"><body>' . $this->getContent() . '</body>');
        }

        return $this->dom;
    }

    public function getContent()
    {
        $content = '';

        foreach ($this->pages->sortBy('page') as $page) {
            $content .= $page->content;
        }

        return $content;
    }

    public function refreshCharactersCount()
    {
        $this->character_count = $this->getCharacterCountInText($this->getContent());
        $this->save();
    }

    public function isNote()
    {
        return $this->type == 'note';
    }

    public function isSection()
    {
        return $this->type == 'section';
    }

    public function isChapter()
    {
        return $this->type == 'section';
    }

    public function isPaid(): bool
    {
        if (!$this->book->isForSale()) {
            return false;
        }

        $firstPaidSection = $this->book->getFirstPaidSection();

        if ($this->is($firstPaidSection)) {
            return true;
        }

        return $this->isLowerThan($firstPaidSection);
    }

    public function isLowerThan(Section $section): bool
    {
        return ($this->_lft > $section->_lft);
    }

    public function scopeChapter($query)
    {
        return $query->where('type', 'section');
    }

    public function scopeNotes($query)
    {
        return $query->where('type', 'note');
    }

    public function scopeChaptersOrNotes($query)
    {
        return $query->where(function ($query) {
            $query->where('type', 'section')
                ->orWhere('type', 'note');
        });
    }

    public function setCharactersCountAttribute($value)
    {
        $this->attributes['character_count'] = $value;
    }

    public function getCharactersCountAttribute()
    {
        return intval($this->attributes['character_count']);
    }

    public function isHigherThan(Section $section): bool
    {
        return ($this->_lft < $section->_lft);
    }

    protected function getScopeAttributes()
    {
        return ['book_id', 'type'];
    }
}
