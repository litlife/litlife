<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserNameShowType extends Enum
{
	const FullLastNameFirstName = 0;
	const FullFirstNameLastName = 1;
	const Nick = 2;
	const LastNameFirstName = 3;
	const FirstNameLastName = 4;
	const FirstnameNicknameLastname = 5;
	const LastnameNicknameFirstname = 6;
	const NicknameFirstname = 7;
	const FirstnameNickname = 8;
}
