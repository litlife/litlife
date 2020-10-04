<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserAccountPermissionValues extends Enum
{
	const me = 1;
	const friends = 2;
	const everyone = 3;
	const friends_and_subscribers = 4;
	const friends_and_subscriptions = 5;
}
