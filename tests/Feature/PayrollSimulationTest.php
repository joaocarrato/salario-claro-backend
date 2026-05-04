<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PayrollSimulation;
use Carbon\CarbonImmutable;
use Database\Seeders\TaxBracketSeeder;
use Database\Seeders\TaxRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PayrollSimulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_returns_ok(): void
    {
        $response = $this->getJson('/api/health');

        $response
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }

    public function test_calculate_payroll(): void
    {
        $this->seedTaxRules();

        $response = $this->postJson('/api/payroll/calculate', [
            'gross_salary' => 5000.00,
            'calculation_year' => 2026,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('gross_salary', 5000)
            ->assertJsonPath('discounts.inss', 501.51)
            ->assertJsonPath('discounts.irrf', 0)
            ->assertJsonPath('irrf_base', 4392.8)
            ->assertJsonPath('total_discounts', 501.51)
            ->assertJsonPath('net_salary', 4498.49)
            ->assertJsonPath('effective_rate', 0.100302)
            ->assertJsonPath('calculation_year', 2026)
            ->assertJsonCount(5, 'calculation_steps');

        self::assertSame([
            'gross_salary',
            'discounts',
            'irrf_base',
            'total_discounts',
            'net_salary',
            'effective_rate',
            'calculation_year',
            'calculation_steps',
        ], array_keys($response->json()));
        self::assertSame([
            'inss',
            'irrf',
            'transport',
            'meal',
            'health_plan',
            'other',
        ], array_keys($response->json('discounts')));
    }

    public function test_store_simulation(): void
    {
        $this->seedTaxRules();

        $response = $this->postJson('/api/simulations', [
            'title' => 'April salary',
            'gross_salary' => 5000.00,
            'transport_discount' => 100.00,
            'calculation_year' => 2026,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'April salary')
            ->assertJsonPath('data.gross_salary', '5000.00')
            ->assertJsonPath('data.discounts.inss', '501.51')
            ->assertJsonPath('data.discounts.transport', '100.00')
            ->assertJsonPath('data.total_discounts', '601.51')
            ->assertJsonPath('data.net_salary', '4398.49');

        $this->assertDatabaseHas('payroll_simulations', [
            'title' => 'April salary',
            'gross_salary' => '5000.00',
            'transport_discount' => '100.00',
            'inss_amount' => '501.51',
            'irrf_amount' => '0.00',
            'total_discounts' => '601.51',
            'net_salary' => '4398.49',
            'calculation_year' => 2026,
        ]);
    }

    public function test_compare_payroll_scenarios(): void
    {
        $this->seedTaxRules();

        $response = $this->postJson('/api/payroll/compare', [
            'first' => [
                'gross_salary' => 4500,
                'dependents' => 0,
                'transport_discount' => 0,
                'meal_discount' => 0,
                'health_plan_discount' => 0,
                'other_discounts' => 0,
            ],
            'second' => [
                'gross_salary' => 5200,
                'dependents' => 0,
                'transport_discount' => 0,
                'meal_discount' => 0,
                'health_plan_discount' => 0,
                'other_discounts' => 0,
            ],
            'calculation_year' => 2026,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('first.gross_salary', 4500)
            ->assertJsonPath('first.net_salary', 4072.97)
            ->assertJsonPath('first.total_discounts', 427.03)
            ->assertJsonPath('second.gross_salary', 5200)
            ->assertJsonPath('second.net_salary', 4597.87)
            ->assertJsonPath('second.total_discounts', 602.13)
            ->assertJsonPath('difference.gross_salary', 700)
            ->assertJsonPath('difference.net_salary', 524.90)
            ->assertJsonPath('difference.total_discounts', 175.10);
    }

    public function test_compare_payroll_validation_error_for_invalid_payload(): void
    {
        $response = $this->postJson('/api/payroll/compare', [
            'first' => [
                'gross_salary' => -1,
            ],
            'calculation_year' => 2026,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'first.gross_salary',
                'second',
            ]);
    }

    public function test_list_simulations_newest_first(): void
    {
        $older = $this->createSimulation('Older', CarbonImmutable::parse('2026-04-01 10:00:00'));
        $newer = $this->createSimulation('Newer', CarbonImmutable::parse('2026-04-02 10:00:00'));

        $response = $this->getJson('/api/simulations');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $newer->id)
            ->assertJsonPath('data.1.id', $older->id)
            ->assertJsonPath('meta.per_page', 15);
    }

    public function test_show_simulation(): void
    {
        $simulation = $this->createSimulation('Show me');

        $response = $this->getJson("/api/simulations/{$simulation->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $simulation->id)
            ->assertJsonPath('data.title', 'Show me')
            ->assertJsonPath('data.discounts.irrf', '0.00');
    }

    public function test_delete_simulation(): void
    {
        $simulation = $this->createSimulation('Delete me');

        $response = $this->deleteJson("/api/simulations/{$simulation->id}");

        $response
            ->assertOk()
            ->assertExactJson([
                'message' => 'Simulation deleted successfully.',
            ]);

        $this->assertDatabaseMissing('payroll_simulations', [
            'id' => $simulation->id,
        ]);
    }

    public function test_validation_error_for_invalid_payload(): void
    {
        $response = $this->postJson('/api/payroll/calculate', [
            'gross_salary' => -1,
            'calculation_year' => 2026,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['gross_salary'])
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'gross_salary',
                ],
            ])
            ->assertJsonMissingPath('exception')
            ->assertJsonMissingPath('trace');
    }

    public function test_invalid_tax_rule_returns_consistent_api_error(): void
    {
        $response = $this->postJson('/api/payroll/calculate', [
            'gross_salary' => 5000.00,
            'calculation_year' => 2026,
        ]);

        $response
            ->assertStatus(422)
            ->assertExactJson([
                'message' => 'Tax rule not found or invalid.',
                'error' => 'INVALID_TAX_RULE',
            ]);
    }

    private function seedTaxRules(): void
    {
        $this->seed([TaxRuleSeeder::class, TaxBracketSeeder::class]);
    }

    private function createSimulation(string $title, ?CarbonImmutable $createdAt = null): PayrollSimulation
    {
        $timestamp = $createdAt ?? CarbonImmutable::parse('2026-04-01 10:00:00');

        $simulation = PayrollSimulation::factory()->create([
            'title' => $title,
        ]);

        PayrollSimulation::query()
            ->whereKey($simulation->id)
            ->update([
                'created_at' => $timestamp->format('Y-m-d H:i:s'),
                'updated_at' => $timestamp->format('Y-m-d H:i:s'),
            ]);

        return $simulation->refresh();
    }
}
