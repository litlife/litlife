<?php

namespace Litlife\BookConverter;

class AbiwordDriver extends Driver
{
	public $inputFormats = ['docx', 'doc', 'xml', 'html', 'rtf'];

	public $outputFormats = ['docx', 'doc', 'xml', 'html', 'rtf'];

	public function getCommand($inputFile, $outputFile)
	{
		return 'abiword --to=' . escapeshellarg((string)$outputFile) . ' ' . escapeshellarg((string)$inputFile) . ' ';
	}
}