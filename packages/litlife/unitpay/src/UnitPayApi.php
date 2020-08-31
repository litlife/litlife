<?php

namespace Litlife\Unitpay;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use function request;

class UnitPayApi
{
	private $client;
	private $method;
	private $params = [];
	private $testRequest = false;

	public function testRequestEnable()
	{
		$this->testRequest = true;

		return $this;
	}

	public function isTestRequestEnabled()
	{
		return (boolean)$this->testRequest;
	}

	public function setAllowedUnitpayIps($allowedUnitpayIps)
	{
		$this->allowedUnitpayIps = $allowedUnitpayIps;
	}

	public function checkIp($ip)
	{
		if (!in_array($ip, (array)config('unitpay.allowed_ips')))
			throw new Exception('Ip is wrong');

		return $this;
	}

	public function checkSignature($method, $params)
	{
		$signature = $this->getSignature($method, $params);

		if ($signature != $params['signature'])
			throw new Exception('Signature is wrong');

		return $this;
	}

	public function getSignature($method = null, array $params): string
	{
		ksort($params);
		if (isset($params['sign'])) unset($params['sign']);
		if (isset($params['signature'])) unset($params['signature']);
		array_push($params, config('unitpay.secret_key'));
		if ($method) {
			array_unshift($params, $method);
		}
		return hash('sha256', join('{up}', $params));
	}

	public function getFormUrl($paymentType, $params): string
	{
		$this->params = array_merge($this->params, $params);

		$this->params['account'] = (string)$this->params['account'];

		$this->validate([
			'account' => 'required|string',
			'sum' => 'required|numeric|min:1',
			'desc' => 'required|string',
			'currency' => 'required|string|in:' . implode(',', config('unitpay.allowed_currencies')) . '',
			'backUrl' => 'nullable|url'
		]);

		$this->params['signature'] = $this->getFormSignature();

		$url = config('unitpay.url') . '/pay/' . config('unitpay.public_key') . '/' . $paymentType . '?' . http_build_query($this->params);

		return $url;
	}

	private function validate($array)
	{
		$validator = Validator::make($this->params, $array);

		if ($validator->fails())
			throw new Exception(pos(pos($validator->errors()->toArray())));
	}

	public function getFormSignature(): string
	{
		$hashStr = $this->params['account'] . '{up}' . $this->params['currency'] . '{up}' . $this->params['desc'] . '{up}' . $this->params['sum'] . '{up}' . config('unitpay.secret_key');
		return hash('sha256', $hashStr);
	}

	public function getCommissions()
	{
		$this->params['projectId'] = config('unitpay.project_id');
		$this->params['login'] = config('unitpay.login');
		$this->params['secretKey'] = config('unitpay.api_secret_key');
		$this->method = 'getCommissions';

		$this->validate([
			'projectId' => 'required|integer',
			'login' => 'required|email',
			'secretKey' => 'required|string',
		]);

		return $this;
	}

	public function initPayment($params)
	{
		$this->params = array_merge($this->params, $params);
		$this->params['projectId'] = config('unitpay.project_id');
		$this->params['ip'] = request()->ip();
		$this->params['secretKey'] = config('unitpay.api_secret_key');
		$this->method = 'initPayment';

		$this->validate([
			'paymentType' => 'required|string|in:' . implode(',', config('unitpay.allowed_payment_types')) . '',
			'account' => 'required|string',
			'sum' => 'required|numeric|min:1',
			'projectId' => 'required|integer',
			'resultUrl' => 'required|string|url',
			'desc' => 'required|string',
			'ip' => 'required|string|ip',
			'secretKey' => 'required|string',
			'customerEmail' => 'nullable|email',
			'currency' => 'nullable|string|in:' . implode(',', config('unitpay.allowed_currencies')) . '',
			'locale' => 'nullable|string|in:' . implode(',', config('unitpay.allowed_locales')) . '',
			'backUrl' => 'nullable|url',
			'subscription' => 'nullable|boolean',
			'subscriptionId' => 'nullable|numeric',
		]);

		return $this;
	}

	public function getPayment($params)
	{
		$this->params = array_merge($this->params, $params);
		$this->params['secretKey'] = config('unitpay.secret_key');
		$this->method = 'getPayment';

		$this->validate([
			'paymentId' => 'required|integer',
			'secretKey' => 'required|string',
		]);

		return $this;
	}

	public function massPayment($params)
	{
		$this->params = array_merge($this->params, $params);
		$this->params['secretKey'] = config('unitpay.api_secret_key');
		$this->params['projectId'] = config('unitpay.project_id');
		$this->params['login'] = config('unitpay.login');
		$this->method = 'massPayment';

		$this->validate([
			'login' => 'required|email',
			'secretKey' => 'required|string',
			'purse' => 'required|string',
			'transactionId' => 'required|string',
			'sum' => 'required|numeric|min:1',
			'paymentType' => 'required|in:' . implode(',', config('unitpay.allowed_outgoing_payment_types')) . '',
			'projectId' => 'nullable|integer',
		]);

		return $this;
	}

	public function massPaymentStatus($params)
	{
		$this->params = array_merge($this->params, $params);
		$this->params['secretKey'] = config('unitpay.api_secret_key');
		$this->params['login'] = config('unitpay.login');
		$this->method = 'massPaymentStatus';

		$this->validate([
			'login' => 'required|email',
			'secretKey' => 'required|string',
			'transactionId' => 'required|string'
		]);

		return $this;
	}

	public function getPartner()
	{
		$this->params['secretKey'] = config('unitpay.api_secret_key');
		$this->params['login'] = config('unitpay.login');
		$this->method = 'getPartner';

		$this->validate([
			'login' => 'required|email',
			'secretKey' => 'required|string'
		]);

		return $this;
	}

	public function getBinInfo($params)
	{
		$this->params = array_merge($this->params, $params);
		$this->params['secretKey'] = config('unitpay.api_secret_key');
		$this->params['login'] = config('unitpay.login');
		$this->method = 'getBinInfo';

		$this->validate([
			'login' => 'required|email',
			'secretKey' => 'required|string',
			'bin' => 'required|integer|digits:6'
		]);

		return $this;
	}

	/**
	 *
	 *
	 * @param void
	 * @return UnitPayApiResponse
	 * @throws
	 * */
	public function request(): UnitPayApiResponse
	{
		$this->client = new Client();

		$query = [
			'method' => $this->getMethod(),
			'params' => $this->getParams()
		];

		$res = $this->client->request('GET', config('unitpay.api_url'), [
			'query' => $query,
			'timeout' => 60
		]);

		$contents = $res->getBody()->getContents();

		$this->log($query, $contents);

		$response = new UnitPayApiResponse($contents);
		$response->throwExceptionIfHasError();
		return $response;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	public function getParams(): array
	{
		$params = $this->params;

		if ($this->testRequest)
			$params['test'] = '1';

		return $params;
	}

	public function log(array $query, $contents)
	{
		if (config('unitpay.log_enable')) {
			$query['params']['secretKey'] = '*******';

			Log::channel(config('unitpay.log_chanel'))
				->info('Request: ' . json_encode($query) . "\n" . 'Response: ' . $contents);
		}
	}
}