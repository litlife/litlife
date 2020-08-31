<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PaymentSystemType extends Enum
{
	const card = 0;
	const qiwi = 1;
	const webmoney = 2;
	const yandex = 3;
	const mobile = 4;
	const tele2 = 5;
	const mf = 6;
	const beeline = 7;
	const mts = 8;
	const paypal = 9;
	const alfaClick = 10;
	const euroset = 11;
	const svyaznoy = 12;
	const applepay = 13;
}
