<?php

namespace Tests\Feature;

use App\Enums\PaymentSystemType;
use App\PaymentSystemComission;
use Illuminate\Support\Facades\Artisan;
use Litlife\Unitpay\Facades\UnitPay;
use Litlife\Unitpay\UnitPayApiResponse;
use Tests\TestCase;

class PaymentSystemComissionTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testUpdate()
	{
		$card_comission = rand(100, 500) / 100;
		$webmoney_comission = rand(100, 500) / 100;

		$json = [
			'result' => [
				"tele2" => 14,
				"mf" => 13,
				"beeline" => 15,
				"mts" => 12,
				"card" => $card_comission,
				"webmoney" => $webmoney_comission,
				"yandex" => 7,
				"qiwi" => 7,
				"paypal" => 1,
				"alfaClick" => 5,
				"euroset" => 6,
				"svyaznoy" => 6,
				"applepay" => 4
			]
		];

		UnitPay::shouldReceive('request')
			->once()
			->andReturn(new UnitPayApiResponse(json_encode($json)));
		UnitPay::makePartial();

		Artisan::call('refresh:unitpay_comissions');

		$payment_system = PaymentSystemComission::unitPay()
			->deposit()
			->paymentSystemType(PaymentSystemType::webmoney)
			->first();

		$this->assertEquals($webmoney_comission, $payment_system->comission);

		$payment_system = PaymentSystemComission::unitPay()
			->deposit()
			->paymentSystemType(PaymentSystemType::card)
			->first();

		$this->assertEquals($card_comission, $payment_system->comission);
	}

	public function testCreate()
	{
		PaymentSystemComission::truncate();

		$card_comission = rand(100, 500) / 100;
		$webmoney_comission = rand(100, 500) / 100;

		$json = [
			'result' => [
				"tele2" => 14,
				"mf" => 13,
				"beeline" => 15,
				"mts" => 12,
				"card" => $card_comission,
				"webmoney" => $webmoney_comission,
				"yandex" => 7,
				"qiwi" => 7,
				"paypal" => 1,
				"alfaClick" => 5,
				"euroset" => 6,
				"svyaznoy" => 6,
				"applepay" => 4
			]
		];

		UnitPay::shouldReceive('request')
			->once()
			->andReturn(new UnitPayApiResponse(json_encode($json)));
		UnitPay::makePartial();

		Artisan::call('refresh:unitpay_comissions');

		$payment_system = PaymentSystemComission::unitPay()
			->deposit()
			->paymentSystemType(PaymentSystemType::webmoney)
			->first();

		$this->assertEquals($webmoney_comission, $payment_system->comission);

		$payment_system = PaymentSystemComission::unitPay()
			->deposit()
			->paymentSystemType(PaymentSystemType::card)
			->first();

		$this->assertEquals($card_comission, $payment_system->comission);
	}

	public function testError()
	{
		$card_comission = rand(100, 500) / 100;
		$webmoney_comission = rand(100, 500) / 100;

		$json = [
			'error' => [
				"message" => "Неверный секретный ключ",
				"code" => -32000
			]
		];

		UnitPay::shouldReceive('request')
			->once()
			->andReturn(new UnitPayApiResponse(json_encode($json)));
		UnitPay::makePartial();

		$this->expectExceptionMessage('Неверный секретный ключ');
		$this->expectExceptionCode(-32000);

		Artisan::call('refresh:unitpay_comissions');
	}

	public function testPaymentSystemNotFound()
	{
		$card_comission = rand(100, 500) / 100;
		$webmoney_comission = rand(100, 500) / 100;
		$unknown_payment_system_comission = rand(100, 500) / 100;

		$json = [
			'result' => [
				"card" => $card_comission,
				"webmoney" => $webmoney_comission,
				"unknown_payment_system" => $unknown_payment_system_comission,
			]
		];

		UnitPay::shouldReceive('request')
			->once()
			->andReturn(new UnitPayApiResponse(json_encode($json)));
		UnitPay::makePartial();

		Artisan::call('refresh:unitpay_comissions');

		$payment_system = PaymentSystemComission::unitPay()
			->deposit()
			->paymentSystemType(PaymentSystemType::webmoney)
			->first();

		$this->assertEquals($webmoney_comission, $payment_system->comission);

		$payment_system = PaymentSystemComission::unitPay()
			->deposit()
			->paymentSystemType(PaymentSystemType::card)
			->first();

		$this->assertEquals($card_comission, $payment_system->comission);
	}
}
