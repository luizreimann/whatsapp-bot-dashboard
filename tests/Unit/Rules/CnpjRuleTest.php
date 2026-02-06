<?php

namespace Tests\Unit\Rules;

use App\Rules\CnpjRule;
use PHPUnit\Framework\TestCase;

class CnpjRuleTest extends TestCase
{
    private CnpjRule $rule;
    private ?string $failMessage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new CnpjRule();
        $this->failMessage = null;
    }

    private function validate(string $value): bool
    {
        $this->failMessage = null;
        $this->rule->validate('cnpj', $value, function ($message) {
            $this->failMessage = $message;
        });
        return $this->failMessage === null;
    }

    public function test_valid_cnpj_without_mask(): void
    {
        $this->assertTrue($this->validate('11222333000181'));
    }

    public function test_valid_cnpj_with_mask(): void
    {
        $this->assertTrue($this->validate('11.222.333/0001-81'));
    }

    public function test_another_valid_cnpj(): void
    {
        $this->assertTrue($this->validate('11444777000161'));
    }

    public function test_invalid_cnpj_wrong_digits(): void
    {
        $this->assertFalse($this->validate('12345678000199'));
    }

    public function test_invalid_cnpj_too_short(): void
    {
        $this->assertFalse($this->validate('1234567890'));
    }

    public function test_invalid_cnpj_too_long(): void
    {
        $this->assertFalse($this->validate('123456789012345'));
    }

    public function test_invalid_cnpj_repeated_sequence(): void
    {
        $this->assertFalse($this->validate('11111111111111'));
    }

    public function test_invalid_cnpj_all_zeros(): void
    {
        $this->assertFalse($this->validate('00000000000000'));
    }

    public function test_invalid_cnpj_with_mask(): void
    {
        $this->assertFalse($this->validate('12.345.678/0001-99'));
    }

    public function test_fail_message(): void
    {
        $this->validate('00000000000000');
        $this->assertEquals('O CNPJ informado não é válido.', $this->failMessage);
    }
}
