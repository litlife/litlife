<?php

namespace App\Library\Old;

class xsBookDB
{
	public $DBPathFull;
	public $BookDBLink;

	/*
	$xsBookDB = new xsBookDB($DBPathFull);
	*/

	function __construct($DBPathFull)
	{
		$this->DBPathFull = $DBPathFull;

		$this->OpenDB();
		//$this->BookDBLink->BeginTransaction();
		//$this->CreateTableIfNotExists();
	}

	function OpenDB()
	{
		if (!file_exists($this->DBPathFull)) {
			throw new Exception('База данных книги отсутствует');
		}

		$this->BookDBLink = new xs_Sqlite2($this->DBPathFull);
	}

	function __destruct()
	{
		if ($this->BookDBLink) {
			//$this->BookDBLink->Commit();
			$this->BookDBLink->close();
		}
	}

	function SqliteLink()
	{
		return $this->BookDBLink;
	}

	function CreateTableIfNotExists()
	{
		$this->BookDBLink->q('CREATE TABLE IF NOT EXISTS "pages" (id INTEGER, text BLOB, PRIMARY KEY(id))');
		$this->BookDBLink->q('CREATE TABLE IF NOT EXISTS "binary" (br_id INTEGER PRIMARY KEY AUTOINCREMENT, br_code VARCHAR(100), br_is_image TINYINT(1), br_name VARCHAR(100) UNIQUE,' .
			'br_mime_type VARCHAR(100), br_edit_time INT(12), br_param TINYTEXT,' .
			'br_content BLOB, br_file_size BIGINT, br_md5 VARCHAR(32))');
		$this->BookDBLink->q('CREATE INDEX IF NOT EXISTS "br_code" ON "binary" ("br_code")');
		$this->BookDBLink->q('CREATE TABLE IF NOT EXISTS "other" (name VARCHAR(50) UNIQUE, content BLOB)');
	}

	function GetBinaryAr($BinaryName)
	{
		$Stmt = $this->BookDBLink->p('SELECT * FROM "binary" WHERE "br_name"=:br_name LIMIT 1');
		$this->BookDBLink->bindValue($Stmt, ':br_name', $BinaryName, SQLITE3_TEXT);
		return pos($this->BookDBLink->e($Stmt));
	}

	function RemoveBinary($BinaryName)
	{
		$Stmt = $this->BookDBLink->p('DELETE FROM "binary" WHERE "br_name" = :br_name');
		$this->BookDBLink->bindValue($Stmt, ':br_name', $BinaryName, SQLITE3_TEXT);
		$this->BookDBLink->e($Stmt);

		//$this->BookDBLink->Save();
	}

	function DeleteAllCovers()
	{
		$this->BookDBLink->q('DELETE FROM "binary" WHERE "br_code" IN (\'CoverOriginal\', \'CoverNormal\', \'CoverSmall\')');
		$this->BookDBLink->Save();
	}

	function GetPageContent($Page)
	{
		$a = $this->BookDBLink->q('SELECT "text" FROM "pages" WHERE "id"="' . $Page . '" LIMIT 1');
		return gzuncompress($a[0]['text']);
	}

	function SetPagesCount($PageCount)
	{
		if ($PageCount === FALSE)
			throw new Exception('Количество страниц не определено');

		$this->BookDBLink->q('INSERT OR REPLACE INTO "other" ("name", "content") VALUES ("pages_count", "' . $PageCount . '");');
	}

	function GetPagesCount()
	{
		$a = $this->BookDBLink->q('SELECT MAX(id) FROM "pages"');
		return (Int)pos($a[0]);
	}

	function SetTitlesCount($TitlesCount)
	{
		if ($TitlesCount === FALSE)
			throw new Exception('Количество страниц не определено');

		$this->BookDBLink->q('INSERT OR REPLACE INTO "other" ("name", "content") VALUES ("section_titles_count", "' . $TitlesCount . '");');
	}

	function GetTitlesCount()
	{
		$a = $this->BookDBLink->q('SELECT * FROM other WHERE name="section_titles_count"');
		$a = pos($a);
		return $a['content'];
	}

	function SetSectionTitlesAr($SectionTitlesAr)
	{
		if ($SectionTitlesAr === FALSE)
			throw new Exception('Массив названий глав не определен');

		$Stmt = $this->BookDBLink->p('INSERT OR REPLACE INTO "other" ("name", "content") VALUES ("titles", :content);');
		$this->BookDBLink->bindValue($Stmt, ':content', gzcompress(serialize($SectionTitlesAr), 9), SQLITE3_BLOB);
		$this->BookDBLink->e($Stmt);
	}

	function SetOtherContent($Name, $Value)
	{
		if (!isset($Name)) {
			throw new Exception('Имя пустое');
		}

		$Stmt = $this->BookDBLink->p('INSERT OR REPLACE INTO "other" ("name", "content") VALUES ("' . $Name . '", :content);');
		$this->BookDBLink->bindValue($Stmt, ':content', gzcompress($Value, 9), SQLITE3_BLOB);
		$this->BookDBLink->e($Stmt);
	}

	function GetOtherContent($Name)
	{
		$a = $this->BookDBLink->q('SELECT "content" FROM "other" WHERE "name"="' . $Name . '" LIMIT 1');
		return gzuncompress($a[0]['content']);
	}

	function GetSectionTitlesAr()
	{
		$ar = $this->BookDBLink->q("SELECT * FROM other WHERE name='titles'");
		return unserialize(gzuncompress($ar[0]['content']));
	}
}


?>