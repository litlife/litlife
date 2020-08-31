<?php

namespace Litlife\BookConverter;

use Litlife\Url\Url;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BookConverter
{
	public $tmpDir;

	protected $driver;

	public $timeout = 600;

	private $inputFile;

	private $outputFile;

	public function __construct($driver = null)
	{
		$this->createTempDir();

		if (!empty($driver))
			return $this->with($driver);
	}

	public function createTempDir(): bool
	{
		$this->tmpDir = sys_get_temp_dir() . '/converter';

		if (!file_exists($this->tmpDir)) {
			$old_umask = umask(0);
			mkdir($this->tmpDir, intval("1777", 8), true);
			umask($old_umask);
		}

		return true;
	}

	public function getTempDir(): string
	{
		return $this->tmpDir;
	}

	public function with($driver)
	{
		if (is_string($driver)) {
			if ($driver == 'abiword')
				$this->driver = new AbiwordDriver();
			elseif ($driver == 'calibre')
				$this->driver = new CalibreDriver();
			else
				throw new \InvalidArgumentException('The specified driver "' . $driver . '" is not supported');
		}

		if (is_object($driver))
			$this->driver = $driver;

		if (empty($this->driver))
			throw new \InvalidArgumentException('Driver is not specified');

		return $this;
	}

	public function getDriver(): Driver
	{
		return $this->driver;
	}

	public function open($source, $extension = null)
	{
		if (is_string($source) and is_file($source)) {

			$this->inputFile = new File($source);

			if (empty($this->inputFile->getExtension()))
				throw new \InvalidArgumentException ('You must specify input file extension');

		} elseif (is_resource($source)) {

			if (empty($extension))
				throw new \InvalidArgumentException ('You must specify input resource extension');

			$this->inputFile = new File(Url::fromString($this->tmpDir . '/' . uniqid() . '.' . $extension));
			$this->inputFile->putContentsFromResource($source);

			if ($this->inputFile->getSize() < 1)
				throw new \InvalidArgumentException('Input resource is empty');

		} else {
			throw new \InvalidArgumentException('File or resource not found');
		}

		if (!$this->isAllowedInputExtension($this->inputFile->getExtension()))
			$this->inputFormatIsNotAllowedException($this->inputFile->getExtension());

		return $this;
	}

	public function getInputFile(): File
	{
		return $this->inputFile;
	}

	public function isAllowedInputExtension($extension)
	{
		return (bool)in_array($extension, $this->getInputFormats());
	}

	public function getInputFormats()
	{
		return $this->driver->inputFormats;
	}

	public function inputFormatIsNotAllowedException($extension)
	{
		throw new \InvalidArgumentException('The input file "' . $extension . '" format is not allowed');
	}

	public function convertToFormat($extension, $process = null): File
	{
		$this->setOutputExtension($extension);

		$fullCommand = $this->createFullCommand();

		if (empty($process))
			$process = Process::fromShellCommandline($fullCommand);

		$process->setTimeout($this->timeout);
		$process->run();

		// executes after the command finishes
		if (!$process->isSuccessful()) {
			throw new ProcessFailedException($process);
		}
		/*
				if (!file_exists($this->outputFilePath))
					throw new \RuntimeException('Output file not found ' . $this->outputFilePath . '');

				if (!is_readable($this->outputFilePath))
					throw new \RuntimeException('Ouput file is not readable');

				if (empty(filesize($this->outputFilePath)))
					throw new \RuntimeException('Ouput file is empty');
		*/
		return $this->outputFile;
	}

	public function getOutputFormats()
	{
		return $this->driver->outputFormats;
	}

	public function createFullCommand()
	{
		if (empty($this->driver))
			throw new \LogicException('Driver is not specified');

		return $this->driver->getCommand($this->getInputFile()->getPath(), $this->getOutputFile()->getPath());
	}

	public function getOutputFile(): File
	{
		return $this->outputFile;
	}

	public function isAllowedOutputExtension($extension)
	{
		return (bool)in_array($extension, $this->getOutputFormats());
	}

	public function setOutputExtension(string $extension)
	{
		if (empty($this->driver))
			throw new \LogicException('Driver is not specified');

		if (empty($extension))
			throw new \InvalidArgumentException('Output extension must be specified');

		if (!$this->isAllowedOutputExtension($extension))
			throw new \InvalidArgumentException('The output extension ' . $extension . ' is not allowed');

		$this->outputFile = new File(Url::fromString($this->tmpDir . '/' . uniqid() . '.' . $extension));

		return $this;
	}
}