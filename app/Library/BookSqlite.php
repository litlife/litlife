<?php

namespace App\Library;

use PDO;

class BookSqlite
{
	private $connection;

	public function connect($db_path)
	{
		$this->connection = new PDO('sqlite:' . $db_path);
	}

	public function setPdoConnection($pdo)
	{
		$this->connection = $pdo;
	}

	public function getConnection()
	{
		return $this->connection;
	}

	public function sectionsCount()
	{
		$statement = $this->connection->prepare('SELECT "content" FROM other WHERE name = "section_titles_count" LIMIT 1');
		$statement->execute();

		return pos($statement->fetchAll(PDO::FETCH_COLUMN));
	}

	public function sections()
	{
		$statement = $this->connection->prepare('SELECT "content" FROM other WHERE name = "titles" LIMIT 1');

		$statement->execute();

		$sections = pos($statement->fetchAll(PDO::FETCH_NAMED));

		if (empty($sections['content']))
			return false;

		$serialized = @gzuncompress($sections['content']);

		return @unserialize($serialized);
	}

	public function binaryContentByName($name)
	{
		$statement = $this->connection->prepare('SELECT * FROM binary WHERE br_name = :name LIMIT 1');
		$statement->bindValue(':name', $name);
		$statement->execute();

		return pos($statement->fetchAll(PDO::FETCH_NAMED));
	}

	public function binaryContentById($id)
	{
		$statement = $this->connection->prepare('SELECT * FROM binary where br_id = :id LIMIT 1');
		$statement->bindValue(':id', $id);
		$statement->execute();

		return pos($statement->fetchAll(PDO::FETCH_NAMED));
	}

	public function binaryList()
	{
		$statement = $this->connection->prepare('SELECT * FROM binary');
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_NAMED);
	}

	public function getCharactersCount()
	{
		$count = $this->pagesCount();

		$strlen = 0;

		for ($page = 1; $page <= $count; $page++) {
			$text = $this->pageContent($page);

			$text = strip_tags($text);

			$text = preg_replace("/[[:space:]]+/iu", "", $text);

			$strlen = ($strlen + mb_strlen($text));
		}

		return intval($strlen);
	}

	public function pagesCount()
	{
		$statement = $this->connection->prepare('SELECT "content" FROM other WHERE name = "pages_count" LIMIT 1');
		$statement->execute();

		$count = pos($statement->fetchAll(PDO::FETCH_COLUMN));

		if (empty($count)) {
			$statement = $this->connection->prepare('SELECT count(*) FROM pages LIMIT 1');
			$statement->execute();

			$count = intval(pos($statement->fetchAll(PDO::FETCH_COLUMN)));

			$statement = $this->connection->prepare('INSERT INTO other (name, content) VALUES (:name, :content)');

			$statement->execute([
				'name' => 'pages_count',
				'content' => $count
			]);
		}
		/*
				$statement = $this->connection->prepare('SELECT count(*) FROM pages LIMIT 1');
				$statement->execute();

				return intval(pos($statement->fetchAll(\PDO::FETCH_COLUMN)));
				*/
		return $count;
	}

	public function pageContent($page)
	{
		$statement = $this->connection->prepare('SELECT * FROM pages WHERE id = :page LIMIT 1');
		$statement->bindValue(':page', $page);
		$statement->execute();

		return @gzuncompress($statement->fetchColumn(1));
	}
}