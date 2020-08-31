<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PaymentStatusEnum extends Enum
{
	const Wait = 0;         // выплата ожидает обработки
	const Processing = 1;   // выплата в процессе
	const Success = 2;      // выплата оплачена
	const Error = 3;        // ошибка выплаты
	const Canceled = 4;     // выплата отменена
	const Refund = 5;       // выплата возвращена покупателю
	const Secure = 6;       // выплата на проверке у службы безопасности банка
	const Expired = 7;      // транзакция не выполнена так время ожидания истекло
}
