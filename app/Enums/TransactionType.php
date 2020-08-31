<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TransactionType extends Enum
{
	const deposit = 1;      // операция пополнения
	const withdrawal = 2;   // операция вывода
	const buy = 3;          // операция покупки
	const sell = 4;         // операция продажи
	const comission = 5;    // операция комиссии
	const receipt = 6;      // операция получения денег на счет
	const transfer = 7;     // операция отправки денег на другой счет
	const comission_referer_buyer = 8;    // операция комиссии за привлечение покупателя
	const comission_referer_seller = 9;    // операция комиссии за привлечение продавца
}
