<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Payroll\IrrfCalculator;
use App\Services\TaxRules\TaxRuleResolver;
use Database\Seeders\TaxBracketSeeder;
use Database\Seeders\TaxRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class IrrfCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_negative_or_zero_base_returns_zero(): void
    {
        $this->seedTaxRules();

        $calculator = $this->calculator();

        self::assertSame(0.0, $calculator->calculate(-1.00, 5000.00, 2026));
        self::assertSame(0.0, $calculator->calculate(0.00, 5000.00, 2026));
    }

    public function test_exempt_range_returns_zero(): void
    {
        $this->seedTaxRules();

        self::assertSame(0.0, $this->calculator()->calculate(2428.80, 3000.00, 2026));
    }

    public function test_first_taxable_range_uses_rate_and_deduction(): void
    {
        $this->seedTaxRules();

        self::assertSame(17.34, $this->calculator()->calculate(2660.00, 3000.00, 2026));
    }

    public function test_middle_range_uses_rate_and_deduction(): void
    {
        $this->seedTaxRules();

        self::assertSame(130.84, $this->calculator()->calculate(3500.00, 5000.00, 2026));
    }

    public function test_highest_range_uses_open_ended_bracket(): void
    {
        $this->seedTaxRules();

        self::assertSame(741.27, $this->calculator()->calculate(6000.00, 8000.00, 2026));
    }

    private function calculator(): IrrfCalculator
    {
        return new IrrfCalculator(new TaxRuleResolver);
    }

    private function seedTaxRules(): void
    {
        $this->seed([TaxRuleSeeder::class, TaxBracketSeeder::class]);
    }
}
