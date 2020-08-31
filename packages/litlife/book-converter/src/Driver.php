<?php

namespace Litlife\BookConverter;

class Driver
{
	public $inputFormats = [];
	public $outputFormats = [];

	public function getCommand($inputFile, $outputFile)
	{
		return 'test ' . escapeshellarg((string)$inputFile) . ' ' . escapeshellarg((string)$outputFile) . '';
	}
}