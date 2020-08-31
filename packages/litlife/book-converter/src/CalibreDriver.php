<?php

namespace Litlife\BookConverter;

class CalibreDriver extends Driver
{
	public $inputFormats = ['docx', 'cbz', 'cbr', 'cbc', 'chm', 'djvu', 'epub', 'fb2', 'html', 'htmlz',
		'lit', 'lrf', 'mobi', 'odt', 'pdf', 'prc', 'pdb', 'pml', 'rb', 'rtf', 'snb', 'tcr', 'txt', 'txtz', 'xml'];

	public $outputFormats = ['azw3', 'epub', 'fb2', 'oeb', 'lit', 'lrf', 'mobi', 'htmlz', 'pdb', 'pml',
		'rb', 'pdf', 'rtf', 'snb', 'tcr', 'txt', 'txtz'];

	public function getCommand($inputFile, $outputFile)
	{
		return 'ebook-convert ' . escapeshellarg((string)$inputFile) . ' ' . escapeshellarg((string)$outputFile) . '';
	}
}