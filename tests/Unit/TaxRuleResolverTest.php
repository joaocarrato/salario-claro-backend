<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\InvalidTaxRuleException;
use App\Models\TaxRule;
use App\Services\TaxRules\TaxRuleResolver;
use Database\Seeders\TaxBracketSeeder;
use Database\Seeders\TaxRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TaxRuleResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_resolves_inss_employee_rule_for_2026_with_ordered_brackets(): void
    {
        $this->seed([TaxRuleSeeder::class, TaxBracketSeeder::class]);

        $rule = (new TaxRuleResolver)->resolve('INSS_EMPLOYEE', 2026);

        self::assertSame('INSS_EMPLOYEE', $rule->type);
        self::assertSame(2026, $rule->year);
        self::assertTrue($rule->relationLoaded('brackets'));
        self::assertCount(4, $rule->brackets);
        self::assertSame([1, 2, 3, 4], $rule->brackets->pluck('sort_order')->all());
    }

    public function test_it_resolves_irrf_monthly_rule_for_2026_with_ordered_brackets(): void
    {
        $this->seed([TaxRuleSeeder::class, TaxBracketSeeder::class]);

        $rule = (new TaxRuleResolver)->resolve('IRRF_MONTHLY', 2026);

        self::assertSame('IRRF_MONTHLY', $rule->type);
        self::assertSame(2026, $rule->year);
        self::assertTrue($rule->relationLoaded('brackets'));
        self::assertCount(5, $rule->brackets);
        self::assertSame([1, 2, 3, 4, 5], $rule->brackets->pluck('sort_order')->all());
    }

    public function test_it_throws_when_rule_does_not_exist(): void
    {
        $this->expectException(InvalidTaxRuleException::class);
        $this->expectExceptionMessage('Tax rule [INSS_EMPLOYEE] for year [2025] was not found.');

        (new TaxRuleResolver)->resolve('INSS_EMPLOYEE', 2025);
    }

    public function test_it_throws_when_rule_has_no_brackets(): void
    {
        TaxRule::factory()->inssEmployee(2026)->create([
            'name' => 'Empty Rule',
        ]);

        $this->expectException(InvalidTaxRuleException::class);
        $this->expectExceptionMessage('Tax rule [INSS_EMPLOYEE] for year [2026] is invalid: no tax brackets configured.');

        (new TaxRuleResolver)->resolve('INSS_EMPLOYEE', 2026);
    }
}
