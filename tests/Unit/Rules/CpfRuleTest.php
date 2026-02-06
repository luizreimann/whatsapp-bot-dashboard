<?php

namespace Tests\Unit\Rules;

use App\Rules\CpfRule;
use PHPUnit\Framework\TestCase;

class CpfRuleTest extends TestCase
{
    private CpfRule $rule;
    private ?string $failMessage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new CpfRule();
        $this->failMessage = null;
    }

    private function validate(string $value): bool
    {
        $this->failMessage = null;
        $this->rule->validate('document', $value, function ($message) {
            $this->failMessage = $message;
        });
        return $this->failMessage === null;
    }

    public function test_valid_cpf_without_mask(): void
    {
        $this->assertTrue($this->validate('52998224725'));
    }

    public function test_valid_cpf_with_mask(): void
    {
        $this->assertTrue($this->validate('529.982.247-25'));
    }

    public function test_another_valid_cpf(): void
    {
        $this->assertTrue($this->validate('11144477735'));
    }

    public function test_invalid_cpf_wrong_digits(): void
    {
        $this->assertFalse($this->validate('12345678901'));
    }

    public function test_invalid_cpf_too_short(): void
    {
        $this->assertFalse($this->validate('1234567'));
    }

    public function test_invalid_cpf_too_long(): void
    {
        $this->assertFalse($this->validate('123456789012'));
    }

    public function test_invalid_cpf_repeated_sequence_all_ones(): void
    {
        $this->assertFalse($this->validate('11111111111'));
    }

    public function test_invalid_cpf_repeated_sequence_all_zeros(): void
    {
        $this->assertFalse($this->validate('00000000000'));
    }

    public function test_invalid_cpf_repeated_sequence_all_nines(): void
    {
        $this->assertFalse($this->validate('99999999999'));
    }

    public function test_invalid_cpf_with_mask(): void
    {
        $this->assertFalse($this->validate('123.456.789-01'));
    }

    public function test_fail_message(): void
    {
        $this->validate('00000000000');
        $this->assertEquals('O CPF informado não é válido.', $this->failMessage);
    }
}
