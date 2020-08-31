<?php

namespace App\Library\Old;


use SQLite3;

class xs_Sqlite2 extends SQLite3
{
	var $Db;

	function p($QueryStr)
	{
		$Stmt = $this->prepare($QueryStr);
		if ($Stmt === FALSE) {
			throw new Exception('Ошибка подготовки запроса SQLite3 ' . $this->lastErrorMsg() . ' запрос ' . $QueryStr . '');
		} else {
			return $Stmt;
		}
	}

	function e(&$Stmt)
	{
		$Ar = [];

		$Result = $Stmt->execute();
		if ($Result === FALSE) {
			throw new Exception('Ошибка запроса SQLite3 ' . $this->lastErrorMsg() . ' ');
		} else {
			while ($row = $Result->fetchArray(SQLITE3_ASSOC)) {
				$Ar[] = ($row);
			}
			return $Ar;
		}
	}

	function bindValue(&$Stmt, $sql_param, $value, $type)
	{
		if ($Stmt->bindValue($sql_param, $value, $type) === FALSE) {
			throw new Exception('Ошибка SQLite3->bindValue значение не совпадает по типу или не найдено в исходном запросе ');
		}
	}

	function CreateTableIfNotExists($TableName, $Q)
	{
		if (!$this->IsTableExists($TableName)) $this->q('CREATE TABLE ' . $TableName . ' ' . $Q . '');
	}

	function IsTableExists($table_name)
	{
		if ($this->q("SELECT name FROM sqlite_master WHERE type='table' AND name='" . $table_name . "';")) return TRUE; else return FALSE;
	}

	function q($QueryStr)
	{
		$ar = [];

		$QueryStr = trim($QueryStr);
		if (!$QueryStr) throw new Exception('Ошибка пустой запрос');
		$Result = $this->query($QueryStr);
		if ($Result === FALSE) {
			throw new Exception('Ошибка SQLite3 ' . $this->lastErrorMsg() . ' запрос ' . $QueryStr . ' ');
		} else {
			while ($row = $Result->fetchArray(SQLITE3_ASSOC)) {
				$ar[] = ($row);
			}
		}
		return $ar;
	}

	function ShowTablesInFile()
	{
		return ($this->q("SELECT * FROM sqlite_master WHERE type='table'"));
	}

	function LastInsertId()
	{
		$result = $this->q("SELECT last_insert_rowid() as last_insert_rowid");
		return $result['last_insert_rowid'];
	}

	function Rollback()
	{
		$this->q("ROLLBACK;");
	}

	function Save()
	{
		$this->Commit();
		$this->BeginTransaction();
	}

	function Commit()
	{
		$this->q("COMMIT;");
	}

	function BeginTransaction()
	{
		$this->q("BEGIN TRANSACTION;");
	}

	function CloseAllOpenedConnection()
	{

	}
}


?>