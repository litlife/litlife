<?php

namespace App\Library\Old;

use Exception;

class xsBookPath
{
	static function GetRemotePath($BookId)
	{
		return $GLOBALS['XS']['S']['DATA']['DOMAIN_HTTP_WWW'] . '/' . self::ReturnPathPart($BookId);
	}

	//xsBookPath::GetLocalPath(136897)

	static function ReturnPathPart($BookId)
	{

		if (!$BookId) throw new Exception('BookId пустой');
		$FolderName1 = (floor($BookId / 1000000) * 1000000);
		$FolderName2 = (floor($BookId / 1000) * 1000);
		return 'Book/' . $FolderName1 . '/' . $FolderName2 . '/' . $BookId;
	}

	//xsBookPath::GetRemotePath(136897)

	static function GetFullRemotePath($BookId)
	{
		return $GLOBALS['XS']['S']['DATA']['DOMAIN_HTTP_WWW_FULL'] . '/' . self::ReturnPathPart($BookId);
	}

	static function GetPathToSqliteDB($BookId)
	{
		return self::GetLocalPath($BookId) . '/' . $BookId . '.sqlite';
	}

	// xsBookPath::GetPathToSqliteDB($BookId)

	static function GetLocalPath($BookId)
	{
		return old_data_path() . '/' . xsBookPath::ReturnPathPart($BookId);
	}
}

?>