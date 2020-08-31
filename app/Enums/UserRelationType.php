<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserRelationType extends Enum
{
	const Null = 0;
	const Subscriber = 1;
	const Friend = 2;
	const Blacklist = 3;
}
