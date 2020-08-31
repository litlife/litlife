<?php

namespace Litlife\Unitpay;


use Litlife\Unitpay\Exceptions\UnitPayApiResponseException;

class UnitPayApiResponse
{
	private $contents;
	private $json;

	public function __construct($contents)
	{
		$this->contents = $contents;
		$this->json = json_decode($this->contents);
	}

	public function isSuccess()
	{
		return (boolean)isset($this->json->result);
	}

	public function getParams()
	{
		return $this->json;
	}

	public function result()
	{
		return optional($this->json)->result;
	}

	public function throwExceptionIfHasError()
	{
		if ($this->isError())
			throw new UnitPayApiResponseException($this->getErrorMessage(), $this->getErrorCode());
	}

	public function isError()
	{
		return (boolean)isset($this->json->error);
	}

	public function getErrorMessage()
	{
		return $this->json->error->message;
	}

	public function getErrorCode()
	{
		return $this->json->error->code;
	}
}