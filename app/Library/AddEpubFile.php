<?php

namespace App\Library;

use App\Attachment;
use App\Author;
use App\Book;
use App\Genre;
use App\Language;
use App\Section;
use App\Sequence;
use DOMNode;
use Illuminate\Support\Facades\DB;
use Litlife\Epub\EpubDescription;
use Litlife\Url\Url;

class AddEpubFile
{
    private $book;
    private $binarySignatureArray;

    public function __construct(Book &$book = null)
    {
        $this->epub = new EpubDescription();
        $this->epub->ignoreMissingFiles = true;

        if (empty($book)) {
            $this->book = new Book;
        } else {
            $this->book = &$book;
        }
    }

    public function setFile(string $source)
    {
        $this->open($source);
    }

    public function open($source)
    {
        $this->epub->setFile($source);
        $this->handlers();
    }

    public function handlers()
    {
        $this->epub->unifyTagIds()->unify();

        $this->epub->unifyImagesNames()->unify();

        $this->epub->addExtensionIfNotExist()->addExtension();

        $this->epub->addSectionsIds()->init();

        $this->getAttachmentsSignatureArray();
    }

    public function getAttachmentsSignatureArray()
    {
        foreach ($this->epub->getImages() as $image) {

            $this->binarySignatureArray[$image->getPath()][] = $image->getImagick()->getImageSignature();

            $attachment = new Attachment();
            $attachment->openImage($image->getImagick());

            $this->binarySignatureArray[$image->getPath()][] = $attachment->getImagick()->getImageSignature();
        }
    }

    public function openStream($source)
    {
        $this->open($source);
    }

    public function setBookId($bookId)
    {
        $this->bookId = $bookId;
    }

    public function setBook(&$book)
    {
        $this->book = &$book;
    }

    public function initWithTransaction()
    {
        DB::transaction(function () {
            $this->init();
        });
        return true;
    }

    public function init()
    {
        $this->addImages();
        $this->description();
        $this->addSections();
        $this->addNotes();

        return true;
    }

    public function addImages()
    {
        foreach ($this->epub->getImages() as $inner_id => $image) {

            if ($image->isValid()) {
                $attachment = new Attachment;
                $attachment->storage = config('filesystems.default');
                $attachment->content_type = $image->getContentType();
                $attachment->size = $image->getSize();
                $attachment->name = $image->getBaseName();
                $attachment->type = 'image';
                $attachment->openImageNotThroughImagick($image->getContent(), 'blob');

                if (!$this->book->attachments()->whereSha256Hash($attachment->getSha256Hash())->first()) {
                    $attachment->addParameter('w', $image->getWidth());
                    $attachment->addParameter('h', $image->getHeight());
                    $attachment->addParameter('epub_path', $image->getPath());
                    $this->book->attachments()->save($attachment);
                }
            }
        }

        $this->book->load('attachments');
    }

    public function description()
    {
        $this->book->title = $this->epub->getTitle();

        if ($lang = Language::where('code', 'ilike', ilikeSpecialChars($this->epub->getLanguage()))->first()) {
            $this->book->ti_lb = $lang->code;
        }

        $this->book->pi_pub = $this->epub->getPublisher() ?? '';
        $this->book->pi_city = $this->epub->getPublishCity() ?? '';
        $this->book->pi_year = $this->epub->getPublishYear() ?? null;
        $this->book->pi_isbn = $this->epub->getISBN() ?? '';
        $this->book->rightholder = $this->epub->getRightsholder() ?? '';
        $this->book->year_writing = $this->epub->getCreatedDate() ?? null;

        $this->addAuthors();
        $this->addTranslators();
        $this->addGenres();
        $this->addSequences();
        $this->addAnnotation();
        $this->addCover();
    }

    private function addAuthors()
    {
        $this->book->writers()->detach();

        foreach ($this->epub->getAuthors() as $order => $name) {

            $author = Author::acceptedOrBelongsToUser($this->book->create_user)
                ->notMerged()
                ->fulltextSearch($name)
                ->first();

            if (empty($author)) {
                $author = new Author;
                $author->name = $name;
                $author->create_user()->associate($this->book->create_user);
                $author->save();
            }

            $this->book->writers()
                ->syncWithoutDetaching([
                    $author->id => ['order' => $order]
                ]);
        }
    }

    private function addTranslators()
    {
        $this->book->translators()->detach();

        foreach ($this->epub->getTranslators() as $order => $name) {

            $translator = Author::acceptedOrBelongsToUser($this->book->create_user)
                ->notMerged()
                ->fulltextSearch($name)
                ->first();

            if (empty($translator)) {
                $translator = new Author;
                $translator->name = $name;
                $translator->create_user()->associate($this->book->create_user);
                $translator->save();
            }

            $this->book->translators()
                ->syncWithoutDetaching([
                    $translator->id => ['order' => $order]
                ]);
        }
    }

    private function addGenres()
    {
        $this->book->genres()->detach();

        foreach ($this->epub->getGenres() as $number => $genreName) {
            if ($genre = Genre::where('fb_code', 'ilike', ilikeSpecialChars($genreName))->orWhere('name', 'ilike', ilikeSpecialChars($genreName))->first()) {

                $this->book->genres()->syncWithoutDetaching([$genre->id => ['order' => $number]]);
            }
        }
    }

    private function addSequences()
    {
        foreach ($this->epub->getSequences() as $order => $sequenceAr) {

            $sequence = Sequence::acceptedOrBelongsToUser($this->book->create_user)
                ->where('name', 'ILIKE', ilikeSpecialChars($sequenceAr['name']))
                ->notMerged()
                ->first();

            if (empty($sequence)) {
                $sequence = new Sequence;
                $sequence->name = $sequenceAr['name'];
                $sequence->create_user()->associate($this->book->create_user);
                $sequence->save();
            }

            $this->book->sequences()
                ->syncWithoutDetaching([
                    $sequence->id => [
                        'number' => empty($sequenceAr['number']) ? null : (int) $sequenceAr['number'],
                        'order' => $order
                    ]
                ]);
        }
    }

    public function addAnnotation()
    {
        $this->book->annotation()->forceDelete();

        if ($annotationText = $this->epub->getAnnotation()) {

            $annotationText = str_replace("\n", '<br />', $annotationText);

            $annotation = new Section;
            $annotation->inner_id = 0;
            $annotation->title = '';
            $annotation->content = $annotationText;
            $annotation->type = 'annotation';
            $this->book->annotation()->save($annotation);
        }
    }

    public function addCover()
    {
        if ($cover = $this->epub->getCover()) {

            $path = $cover->getPath();

            if (array_key_exists((string) $path, $this->binarySignatureArray)) {
                $signature = $this->binarySignatureArray[(string) $path];
                $attachment = $this->book->attachments()->whereSha256Hash($signature)->first();

                if (!empty($attachment)) {
                    $this->book->cover()->associate($attachment);
                }
            }
        }
    }

    public function addSections()
    {
        $inner_id = 0;

        collect($this->epub->getSectionsList())->map(function ($section) use (&$inner_id) {

            if ($section->getLinear() != 'no') {
                $inner_id++;

                $this->addSection($section, $inner_id);
            }
        });

        $this->book->load('sections');
    }

    public function addSection(\Litlife\Epub\Section $section, $inner_id)
    {
        $text = trim(strip_tags($section->getBodyContent(), '<img>'));

        if (!empty($text)) {

            $nodeList = $section->xpath()->query("//*[local-name()='img'][@src]", $section->body());

            if ($nodeList->length) {

                foreach ($nodeList as $node) {

                    $this->imageNode($section, $node);
                }
            }

            $title = $section->getTitle();

            $s = new Section;
            $s->book_id = $this->book->id; // не убирать и не менять расположение
            $s->scoped(['book_id' => $this->book->id, 'type' => 'section']);
            $s->title = empty($title) ? __('section.untitled') : $title;
            $s->content = $section->getBodyContent();
            $s->inner_id = $inner_id;
            $s->addParameter('epub_path', $section->getPath());
            $s->addParameter('section_id', appendPrefix(config('litlife.class_prefix'), $section->body()->getAttribute('id')));
            $this->book->sections()->save($s);

            $s->refresh();
        }
    }

    public function imageNode(\Litlife\Epub\Section $section, DOMNode $node)
    {
        $imagesPath = Url::fromString(urldecode($node->getAttribute("src")))
            ->getPathRelativelyToAnotherUrl($section->getPath());

        if (empty($imagesPath->getHost())) {

            if (array_key_exists((string) $imagesPath, $this->binarySignatureArray)) {

                $signature = $this->binarySignatureArray[(string) $imagesPath];

                $attachment = $this->book->attachments()
                    ->whereSha256Hash($signature)
                    ->first();

                if (!empty($attachment)) {
                    $node->setAttribute("src", $attachment->url);
                    if ($attachment->getWidth()) {
                        $node->setAttribute("width", $attachment->getWidth());
                    }

                    if ($attachment->getHeight()) {
                        $node->setAttribute("height", $attachment->getHeight());
                    }

                    $prefix = appendPrefix(config('litlife.class_prefix'), 'attachment-');

                    if ($node->hasAttribute("class")) {
                        $class = $node->getAttribute("class");

                        $class = preg_replace("/[[:space:]]+/iu", " ", $class);

                        $class = preg_replace("/".preg_quote($prefix)."([0-9]+)/iu", "", $class);

                        $class_ar = explode(' ', $class);
                        $class_ar[] = $prefix.$attachment->id;
                        $class = implode(' ', $class_ar);

                        $node->setAttribute("class", $class);
                    } else {
                        $node->setAttribute("class", $prefix.$attachment->id);
                    }
                }
            }
        }
    }

    public function addNotes()
    {

    }
}
