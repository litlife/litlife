<?php

namespace Litlife\Fb2\Tests;

use DOMElement;
use DOMNode;
use Exception;
use Litlife\Fb2\Fb2;
use Litlife\Fb2\Fb2List;
use Litlife\Fb2\Tag;
use PHPUnit\Framework\TestCase;

class Fb2Test extends TestCase
{
	public function testCreate()
	{
		$fb2 = new Fb2();
		$this->assertInstanceOf(Fb2::class, $fb2);
	}

	public function testGetNotesBody()
	{
		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/books/test.fb2');

		$this->assertInstanceOf(DOMElement::class, $fb2->getBodiesNotes()[0]->getNode());
	}

	public function testCreateEmpty()
	{
		$fb2 = new Fb2();

		$content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<FictionBook xmlns=\"http://www.gribuser.ru/xml/fictionbook/2.0\" xmlns:l=\"http://www.w3.org/1999/xlink\">\n  <description></description>\n</FictionBook>";

		$this->assertEquals($content, $fb2->getContent());
	}

	public function testOutputEncoding()
	{
		$fb2 = new Fb2();
		$title_info = $fb2->createElement('title-info');
		$title_info->setValue('тест');
		$fb2->description()->appendChild($title_info);

		$content = $fb2->getContent('windows-1251');

		$fb2 = new Fb2();
		$fb2->loadXML($content);
		$content = $fb2->getContent('utf-8');

		$description = $fb2->xpath()->query("*[local-name()='description']/*[local-name()='title-info']")->item(0);
		$this->assertEquals('тест', $description->nodeValue);
	}

	public function testParseNamespacePrefix()
	{
		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/books/test.fb2');

		$this->assertEquals('l', $fb2->parseNamespacePrefix());
	}


	public function testSetTitle()
	{
		$title = 'тест';

		$fb2 = new Fb2();

		$book_title = $fb2->description()
			->create('title-info')
			->create('book-title', $title);

		$content = $fb2->getContent();

		$fb2 = new Fb2();
		$fb2->loadXML($content);

		$book_title = $fb2->fictionBook()
			->query('//p:description/p:title-info/p:book-title')->first();

		$this->assertEquals($title, $book_title->getNodeValue());
	}

	public function testAuthor()
	{
		$fb2 = new Fb2();

		$author = $fb2->fictionBook()
			->description()
			->create('title-info')
			->create('author');

		$author->create('first-name', 'firstName');
		$author->create('last-name', 'lastName');

		$content = $fb2->getContent();

		$fb2 = new Fb2();
		$fb2->loadXML($content);

		$author = $fb2->fictionBook()
			->description()
			->query('//p:title-info/p:author')
			->first();

		$this->assertEquals('firstName', $author->query('p:first-name')->first()->getNodeValue());
		$this->assertEquals('lastName', $author->query('p:last-name')->first()->getNodeValue());
	}

	public function testCreateBookTitle()
	{
		$fb2 = new Fb2();

		$fb2->fictionBook()
			->description()
			->create('title-info')
			->create('book-title', 'test');

		$this->assertEquals('test', $fb2->fictionBook()->description()->query('//p:title-info/p:book-title')->first()->getNodeValue());
	}

	public function testCreateAuthor()
	{
		$fb2 = new Fb2();

		$author = $fb2->description()
			->create('title-info')
			->create('author');

		$author->create('first-name', 'firstName');
		$author->create('last-name', 'lastName');

		$this->assertEquals('firstName',
			$fb2->query('//p:FictionBook/p:description/p:title-info/p:author/p:first-name')->item(0)->nodeValue);

		$this->assertEquals('lastName',
			$fb2->query('//p:FictionBook/p:description/p:title-info/p:author/p:last-name')->item(0)->nodeValue);
	}

	public function testGetNodeTitle()
	{
		$fb2 = new Fb2();

		$title = $fb2->description()
			->create('title-info')
			->create('book-title', 'test');

		$this->assertEquals('test', $title->getNodeValue());
	}

	public function testGetChilds()
	{
		$fb2 = new Fb2();

		$title = $fb2->fictionBook()
			->description()
			->create('title-info')
			->create('book-title', 'test');

		$this->assertEquals('FictionBook', $fb2->fictionBook()->getNodeName());

		$description = $fb2->fictionBook()->childs()->first();

		$this->assertEquals('description', $description->getNodeName());

		$this->assertEquals(1, $description->childs()->count());

		$this->assertEquals('title-info', $description->childs()->first()->getNodeName());
	}

	public function testQuery()
	{
		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/books/test.fb2');

		$title_info = $fb2->fictionBook()
			->query('//p:description/p:title-info')->first();

		$this->assertEquals('title-info', $title_info->getNodeName());

		$nodes = $title_info->childs();

		$this->assertEquals(12, $nodes->count());
	}

	public function testFictionBookTag()
	{
		$fb2 = new Fb2();

		$fictionBook = $fb2->fictionBook();

		$this->assertEquals('FictionBook', $fictionBook->getNodeName());
		$this->assertInstanceOf(DOMNode::class, $fictionBook->getNode());
		$this->assertEquals('', $fictionBook->getNodeValue());

		$this->assertEquals(0, $fb2->fictionBook()->childs()->count());
		$this->assertNotNull($fictionBook->getParent());
	}

	public function testTitle()
	{
		$fb2 = new Fb2();

		$title = 'текст';

		$fb2->fictionBook()
			->description()
			->create('title-info')
			->create('book-title')
			->setValue($title);

		$xml = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<FictionBook xmlns="http://www.gribuser.ru/xml/fictionbook/2.0" xmlns:l="http://www.w3.org/1999/xlink">
  <description>
    <title-info>
      <book-title>текст</book-title>
    </title-info>
  </description>
</FictionBook>
EOF;

		$this->assertEquals(str_replace("\r\n", "\n", $xml),
			$fb2->getContent());

		$this->assertEquals('description', $fb2->fictionBook()->description()->getNodeName());

		$this->assertInstanceOf(Tag::class, $fb2->fictionBook()->description()->getFirstChild('title-info')->getFirstChild('book-title'));
		$this->assertInstanceOf(DOMNode::class, $fb2->fictionBook()->description()->getFirstChild('title-info')->getFirstChild('book-title')->getNode());

		$this->assertEquals($title, $fb2->fictionBook()->description()->getFirstChild('title-info')->getFirstChild('book-title')->getNodeValue());

		$title = uniqid();

		$fb2->fictionBook()
			->description()
			->getFirstChild('title-info')
			->getFirstChild('book-title')
			->setValue($title);

		$this->assertEquals($title, $fb2->fictionBook()
			->description()
			->getFirstChild('title-info')
			->getFirstChild('book-title')
			->getNodeValue());
	}

	public function testChilds()
	{
		$fb2 = new Fb2();

		$title = 'текст';

		$fictionBook = $fb2->fictionBook();
		$description = $fictionBook->description();
		$title_info = $description->create('title-info');
		$book_title = $title_info->create('book-title');
		$book_title->setValue('текст');

		/*
				$this->assertEquals("<?xml version=\"1.0\"?>\n<FictionBook xmlns=\"http://www.gribuser.ru/xml/fictionbook/2.0\" xmlns:l=\"http://www.w3.org/1999/xlink\"><description><title-info><book-title>" . $title . "</book-title></title-info></description></FictionBook>\n",
					$fb2->getXml());
		*/
		$this->assertEquals($fictionBook, $fb2->fictionBook());
		$this->assertEquals(1, $fb2->fictionBook()->childs()->count());

		$this->assertInstanceOf(Fb2List::class, $fb2->fictionBook()->childs());

		$this->assertEquals($description, $fb2->fictionBook()->childs()->item(0));
		//$this->assertEquals($description, $fb2->fictionBook()->first());
		$this->assertEquals($description, $fb2->fictionBook()->getFirstChild('description'));
		$this->assertEquals($description, $fb2->fictionBook()->description());

		$this->assertEquals(1, $fb2->fictionBook()->description()->childs()->count());
		$this->assertEquals('title-info', $fb2->fictionBook()->description()->childs()->first()->getNodeName());
		$this->assertEquals($title_info, $fb2->fictionBook()->description()->getFirstChild('title-info'));

		$this->assertEquals(1, $fb2->fictionBook()->description()->getFirstChild('title-info')->childs()->count());
	}

	public function testAuthorTag()
	{
		$fb2 = new Fb2();

		$author = $fb2->createElement('author');
		$author->create('last-name', 'Фамилия');
		$author->create('first-name', 'Имя');

		$this->assertInstanceOf(Tag::class, $author);
		$this->assertInstanceOf(DOMNode::class, $author->getNode());
		$this->assertEquals('author', $author->getNodeName());
		$this->assertEquals(2, $author->childs()->count());

		$fb2->description()
			->create('title-info')
			->appendChild($author);

		$xml = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<FictionBook xmlns="http://www.gribuser.ru/xml/fictionbook/2.0" xmlns:l="http://www.w3.org/1999/xlink">
  <description>
    <title-info>
      <author>
        <last-name>Фамилия</last-name>
        <first-name>Имя</first-name>
      </author>
    </title-info>
  </description>
</FictionBook>
EOF;

		$this->assertEquals(str_replace("\r\n", "\n", $xml),
			$fb2->getContent());
	}

	public function testDescriptionTag()
	{
		$fb2 = new Fb2();

		$description = $fb2->description();

		$title_info = $description->create('title-info');

		$title_info->appendChild($fb2->createElement('genre', 'sf_epic'));
		$title_info->appendChild($fb2->createElement('genre', 'det_hard'));

		$author = $fb2->createElement('author');
		$author->create('last-name')->setValue('Фамилия');
		$author->create('first-name')->setValue('Имя');

		$title_info->appendChild($author);

		$author = $fb2->createElement('author');
		$author->create('last-name')->setValue('Фамилия2');
		$author->create('first-name')->setValue('Имя2');

		$title_info->appendChild($author);

		$title_info->appendChild($fb2->createElement('book-title', 'Название книги'));

		$annotation = $fb2->createElement('annotation');
		$annotation->appendChild($fb2->createElement('p', 'Аннотация'));

		$title_info->appendChild($annotation);

		$xml = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<FictionBook xmlns="http://www.gribuser.ru/xml/fictionbook/2.0" xmlns:l="http://www.w3.org/1999/xlink">
  <description>
    <title-info>
      <genre>sf_epic</genre>
      <genre>det_hard</genre>
      <author>
        <last-name>Фамилия</last-name>
        <first-name>Имя</first-name>
      </author>
      <author>
        <last-name>Фамилия2</last-name>
        <first-name>Имя2</first-name>
      </author>
      <book-title>Название книги</book-title>
      <annotation>
        <p>Аннотация</p>
      </annotation>
    </title-info>
  </description>
</FictionBook>
EOF;

		$this->assertEquals(str_replace("\r\n", "\n", $xml),
			$fb2->getContent());
	}

	public function testDescription()
	{
		$fb2 = new Fb2();

		$description = $fb2->description();

		$this->assertNotNull($description);
	}

	public function testDelete()
	{
		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/books/test.fb2');

		$title = $fb2->fictionBook()
			->description()
			->query('//p:title-info/p:book-title')
			->first();

		$this->assertNotNull($title);

		$title->delete();

		$this->assertEquals(0, $fb2->fictionBook()
			->description()
			->query('//p:title-info/p:book-title')
			->count());

		$title = $fb2->fictionBook()
			->description()
			->query('//p:title-info/p:book-title')
			->item(0);

		$this->assertNull($title);
	}


	public function testIsValid()
	{
		$fb2 = new Fb2();
		$fb2->loadFile(__DIR__ . '/books/test.fb2');

		$this->assertTrue($fb2->isValid());

		$fb2->fictionBook()->description()->create('test');

		$this->assertFalse($fb2->isValid());
	}

	public function testValidationErrors()
	{
		$fb2 = new Fb2();
		$fb2->loadFile(__DIR__ . '/books/test.fb2');
		$fb2->fictionBook()->description()->create('test');

		$body = $fb2->getBodies()[0];
		$body->create('test2');

		$errors = $fb2->getValidationErrors();

		$this->assertEquals("Element 'test': This element is not expected. Expected is one of ( custom-info, output ).\n",
			$errors[0]->message);

		$this->assertEquals("Element 'test2': This element is not expected. Expected is ( section ).\n",
			$errors[1]->message);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testInit()
	{
		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/books/test.fb2');

		$description = $fb2->description();

		$titleInfo = $description->getFirstChild('title-info');
		$publishInfo = $description->getFirstChild('publish-info');

		$this->assertEquals('Title', $titleInfo->getFirstChildValue('book-title'));

		$this->assertEquals('ru', $titleInfo->getFirstChildValue('lang'));
		$this->assertEquals('en', $titleInfo->getFirstChildValue('src-lang'));

		$this->assertEquals('Publisher', $publishInfo->getFirstChildValue('publisher'));
		$this->assertEquals('City', $publishInfo->getFirstChildValue('city'));
		$this->assertEquals('2000', $publishInfo->getFirstChildValue('year'));
		$this->assertEquals('1-11-111111', $publishInfo->getFirstChildValue('isbn'));

		$authors = $titleInfo->childs('author');

		$this->assertEquals(2, $authors->count());

		$this->assertEquals('FirstName', $authors->item(0)->getFirstChildValue('first-name'));
		$this->assertEquals('LastName', $authors->item(0)->getFirstChildValue('last-name'));
		$this->assertEquals('MiddleName', $authors->item(0)->getFirstChildValue('middle-name'));
		$this->assertEquals('NickName', $authors->item(0)->getFirstChildValue('nickname'));
		$this->assertEquals('https://example.com', $authors->item(0)->getFirstChildValue('home-page'));
		$this->assertEquals('test@example.com', $authors->item(0)->getFirstChildValue('email'));
		/*
				$this->assertEquals('image.png', $titleInfo->coverFileName());

				foreach ($titleInfo->series() as $sequence) {

					$this->assertEquals('Title', $sequence['name']);
					$this->assertEquals('1', $sequence['number']);
				}

				$this->assertEquals('<p>Annotation <a class="u-note" href="#note5">note5</a></p>',
					$titleInfo->annotation()->getHtml());

				foreach ($fb2->getBodies() as $body) {

					$sections = $body->getSections();

					$section = $sections[0];

					$this->assertEquals('Section 1', $section->getTitle());
					$this->assertEquals('<p>section 1 text</p><div class="u-empty-line"></div><img src="#image.png"><div class="u-empty-line"></div>', $section->getHtmlExceptTitleAndSection());
					$this->assertEquals(0, $section->getSectionsCount());
					$this->assertEquals('section1', $section->getFb2Id());
					$this->assertTrue($section->isHaveImages());
					$this->assertFalse($section->isHaveInnerSections());

					$section = $sections[1];

					$this->assertEquals('Section 2', $section->getTitle());
					$this->assertEquals('<p>текст второй секции</p><div class="u-empty-line"></div>', $section->getHtmlExceptTitleAndSection());
					$this->assertEquals(0, $section->getSectionsCount());
					$this->assertNull($section->getFb2Id());
					$this->assertFalse($section->isHaveImages());
					$this->assertFalse($section->isHaveInnerSections());

					$section = $sections[2];

					$this->assertEquals('Section 3', $section->getTitle());
					$this->assertEquals('<p><a class="u-note" href="#note3">note1text</a> </p>', $section->getHtmlExceptTitleAndSection());
					$this->assertEquals(0, $section->getSectionsCount());
					$this->assertNull($section->getFb2Id());
					$this->assertFalse($section->isHaveImages());
					$this->assertFalse($section->isHaveInnerSections());

					$section = $sections[3];

					$this->assertEquals('Section 4', $section->getTitle());
					$this->assertEquals('<p><a class="u-note" href="#note4">note2text</a> </p>', $section->getHtmlExceptTitleAndSection());
					$this->assertEquals(0, $section->getSectionsCount());
					$this->assertNull($section->getFb2Id());
					$this->assertFalse($section->isHaveImages());
					$this->assertFalse($section->isHaveInnerSections());

					$section = $sections[4];

					$this->assertTrue($section->isHaveInnerSections());
					$this->assertEquals('Section 5', $section->getTitle());
					$this->assertEquals(1, $section->getSectionsCount());

					foreach ($section->getSections() as $subsection) {
						$this->assertEquals('Subsection 51', $subsection->getTitle());
						$this->assertTrue($subsection->isHaveInnerSections());
						$this->assertEquals(1, $subsection->getSectionsCount());

						foreach ($subsection->getSections() as $subsubsection) {
							$this->assertEquals('Subsubsection 511', $subsubsection->getTitle());
							$this->assertFalse($subsubsection->isHaveInnerSections());
							$this->assertEquals(0, $subsubsection->getSectionsCount());
							$this->assertEquals('<p>text</p>', $subsubsection->getHtmlExceptTitleAndSection());
						}
					}
				}

				foreach ($fb2->getBodiesNotes() as $body) {

					$sections = $body->getSections();

					$section = $sections[0];

					$this->assertEquals('1', $section->getTitle());
					$this->assertEquals('<p>note 1 text</p>', $section->getHtmlExceptTitleAndSection());
					$this->assertEquals(0, $section->getSectionsCount());
					$this->assertEquals('note3', $section->getFb2Id());
					$this->assertFalse($section->isHaveImages());
					$this->assertFalse($section->isHaveInnerSections());

					$section = $sections[1];

					$this->assertEquals('2', $section->getTitle());
					$this->assertEquals('<p>note 2 text</p>', $section->getHtmlExceptTitleAndSection());
					$this->assertEquals(0, $section->getSectionsCount());
					$this->assertEquals('note4', $section->getFb2Id());
					$this->assertFalse($section->isHaveImages());
					$this->assertFalse($section->isHaveInnerSections());
				}

				foreach ($fb2->getBinaryList() as $binary) {

					$this->assertNotNull($binary->getContent());
					$this->assertEquals('1772', $binary->getSize());
					$this->assertTrue($binary->isExists());
					$this->assertEquals('image.png', $binary->getName());
					$this->assertEquals('image/png', $binary->getContentType());
					$this->assertEquals('275', $binary->getWidth());
					$this->assertEquals('382', $binary->getHeight());
				}
				*/
	}

	public function testOpenStream()
	{
		$file = fopen(__DIR__ . '/books/test.fb2', 'r');

		$fb2 = new Fb2();
		$fb2->loadFile($file);

		$this->assertEquals('Title', $fb2->description()
			->getFirstChild('title-info')
			->getFirstChildValue('book-title'));
	}

	public function testLoadXml()
	{
		$file = file_get_contents(__DIR__ . '/books/test.fb2');

		$fb2 = new Fb2();
		$fb2->loadXML($file);

		$this->assertEquals('Title', $fb2->description()
			->getFirstChild('title-info')
			->getFirstChildValue('book-title'));
	}

	public function testExceptionFileOrResourceNotFound()
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('File or resource invalid');

		$fb2 = new Fb2();
		$fb2->loadFile(uniqid());
	}

	public function testFileWithHtmlEntities()
	{
		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/books/test_with_html_entities.fb2');

		$this->assertEquals('Название книги', $fb2->description()
			->getFirstChild('title-info')
			->getFirstChildValue('book-title'));
	}

	public function testEntities()
	{
		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/books/test_entities.fb2');

		$this->assertEquals('Название & книги', $fb2->description()
			->getFirstChild('title-info')
			->getFirstChildValue('book-title'));
	}

	public function testParseNamespace()
	{
		$fb2 = new Fb2();

		$this->assertEquals('l', $fb2->parseNamespacePrefix(file_get_contents(__DIR__ . '/books/test_entities.fb2')));
	}

	public function testIsFileParsedIfNoNamespaceExists()
	{
		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/books/without_namespace.fb2');

		$this->assertEquals('Название книги', $fb2->description()
			->getFirstChild('title-info')
			->getFirstChildValue('book-title'));
	}
}
