<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class AuthorEnum extends Enum
{
	const Writer = 0;
	const Translator = 1;
	const Editor = 2;
	const Compiler = 3;
	const Illustrator = 4;
}
