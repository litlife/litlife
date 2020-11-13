<?php

namespace Tests\Feature\ValidationRules;

use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AlphaSingleQuoteValidationRuleTest extends TestCase
{
    public function testIfLettersPasses()
    {
        $validator = $this->validate('тест');

        $this->assertTrue($validator->passes());
    }

    private function validate(string $text): \Illuminate\Validation\Validator
    {
        return Validator::make([
            'text' => $text
        ], [
            'text' => 'required|alpha_single_quote'
        ], [], ['text' => 'Текст']);
    }

    public function testIfNumbersFails()
    {
        $validator = $this->validate('тест45');

        $this->assertTrue($validator->fails());
    }

    public function testIfSingleQuotePasses()
    {
        $validator = $this->validate('тест\'fg');

        $this->assertTrue($validator->passes());
    }

    public function testIfDoubleQuoteFails()
    {
        $validator = $this->validate('тест"fg');

        $this->assertTrue($validator->fails());

        $this->assertEquals(__('validation.alpha_single_quote', ['attribute' => 'Текст']),
            pos($validator->errors()->get('text')));
    }
}