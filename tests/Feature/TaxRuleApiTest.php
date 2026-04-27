<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\TaxRule;
use Database\Seeders\TaxBracketSeeder;
use Database\Seeders\TaxRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TaxRuleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_tax_rules_with_brackets(): void
    {
        $this->seedTaxRules();

        $response = $this->getJson('/api/tax-rules');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.type', 'INSS_EMPLOYEE')
            ->assertJsonCount(4, 'data.0.brackets')
            ->assertJsonPath('data.0.brackets.0.sort_order', 1)
            ->assertJsonPath('data.1.type', 'IRRF_MONTHLY')
            ->assertJsonCount(5, 'data.1.brackets');
    }

    public function test_it_filters_tax_rules_by_year(): void
    {
        $this->seedTaxRules();

        TaxRule::factory()->inssEmployee(2027)->create([
            'name' => 'Future Rule',
        ]);

        $response = $this->getJson('/api/tax-rules?year=2026');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.year', 2026)
            ->assertJsonPath('data.1.year', 2026);
    }

    public function test_it_filters_tax_rules_by_type(): void
    {
        $this->seedTaxRules();

        $response = $this->getJson('/api/tax-rules?type=IRRF_MONTHLY');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'IRRF_MONTHLY')
            ->assertJsonCount(5, 'data.0.brackets');
    }

    public function test_it_shows_one_tax_rule_with_ordered_brackets(): void
    {
        $this->seedTaxRules();

        $rule = TaxRule::query()
            ->where('type', 'IRRF_MONTHLY')
            ->where('year', 2026)
            ->firstOrFail();

        $response = $this->getJson("/api/tax-rules/{$rule->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $rule->id)
            ->assertJsonPath('data.type', 'IRRF_MONTHLY')
            ->assertJsonPath('data.metadata.dependent_deduction', '189.59')
            ->assertJsonCount(5, 'data.brackets')
            ->assertJsonPath('data.brackets.0.sort_order', 1)
            ->assertJsonPath('data.brackets.4.max_amount', null);
    }

    private function seedTaxRules(): void
    {
        $this->seed([TaxRuleSeeder::class, TaxBracketSeeder::class]);
    }
}
