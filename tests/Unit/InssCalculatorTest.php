<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Payroll\InssCalculator;
use App\Services\TaxRules\TaxRuleResolver;
use Database\Seeders\TaxBracketSeeder;
use Database\Seeders\TaxRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class InssCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_salary_zero_returns_zero(): void
    {
        $this->seedTaxRules();

        self::assertSame(0.0, $this->calculator()->calculate(0, 2026));
    }

    public function test_salary_within_first_bracket(): void
    {
        $this->seedTaxRules();

        self::assertSame(75.0, $this->calculator()->calculate(1000.00, 2026));
    }

    public function test_salary_across_multiple_brackets_is_progressive(): void
    {
        $this->seedTaxRules();

        self::assertSame(501.51, $this->calculator()->calculate(5000.00, 2026));
    }

    public function test_salary_above_inss_ceiling_uses_maximum_bracket_cap(): void
    {
        $this->seedTaxRules();

        $calculator = $this->calculator();

        self::assertSame(988.09, $calculator->calculate(8475.55, 2026));
        self::assertSame(988.09, $calculator->calculate(12000.00, 2026));
    }

    private function calculator(): InssCalculator
    {
        return new InssCalculator(new TaxRuleResolver);
    }

    private function seedTaxRules(): void
    {
        $this->seed([TaxRuleSeeder::class, TaxBracketSeeder::class]);
    }
}
