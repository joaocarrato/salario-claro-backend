<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Resources\PayrollSimulationResource;
use App\Http\Resources\TaxRuleResource;
use App\Models\PayrollSimulation;
use App\Models\TaxBracket;
use App\Models\TaxRule;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Tests\TestCase;

final class ApiResourceTest extends TestCase
{
    public function test_payroll_simulation_resource_returns_clean_shape(): void
    {
        $createdAt = CarbonImmutable::parse('2026-04-27 10:00:00', 'UTC');
        $updatedAt = CarbonImmutable::parse('2026-04-27 11:00:00', 'UTC');
        $simulation = PayrollSimulation::factory()->make([
            'title' => 'April salary',
            'gross_salary' => '5000.00',
            'dependents' => 2,
            'transport_discount' => '100.00',
            'meal_discount' => '250.00',
            'health_plan_discount' => '300.00',
            'other_discounts' => '50.00',
            'inss_amount' => '501.51',
            'irrf_base' => '4119.31',
            'irrf_amount' => '251.35',
            'total_discounts' => '1452.86',
            'net_salary' => '3547.14',
            'effective_rate' => '0.290572',
            'calculation_year' => 2026,
        ]);
        $simulation->id = 'simulation-id';
        $simulation->created_at = $createdAt;
        $simulation->updated_at = $updatedAt;

        $resource = (new PayrollSimulationResource($simulation))->toArray(Request::create('/'));

        self::assertSame([
            'id' => 'simulation-id',
            'title' => 'April salary',
            'gross_salary' => '5000.00',
            'dependents' => 2,
            'discounts' => [
                'inss' => '501.51',
                'irrf' => '251.35',
                'transport' => '100.00',
                'meal' => '250.00',
                'health_plan' => '300.00',
                'other' => '50.00',
            ],
            'irrf_base' => '4119.31',
            'total_discounts' => '1452.86',
            'net_salary' => '3547.14',
            'effective_rate' => '0.290572',
            'calculation_year' => 2026,
            'created_at' => '2026-04-27T13:00:00.000000Z',
            'updated_at' => '2026-04-27T14:00:00.000000Z',
        ], $resource);
    }

    public function test_tax_rule_resource_returns_loaded_brackets(): void
    {
        $rule = TaxRule::factory()->irrfMonthly(2026)->make([
            'name' => 'IRRF Monthly Table 2026',
            'metadata' => [
                'dependent_deduction' => '189.59',
            ],
        ]);
        $rule->id = 'rule-id';

        $bracket = TaxBracket::factory()->make([
            'tax_rule_id' => 'rule-id',
            'min_amount' => '4664.69',
            'max_amount' => null,
            'rate' => '0.275000',
            'deduction' => '908.73',
            'sort_order' => 5,
        ]);
        $bracket->id = 'bracket-id';
        $rule->setRelation('brackets', collect([$bracket]));

        $resource = (new TaxRuleResource($rule))->toArray(Request::create('/'));

        self::assertSame('rule-id', $resource['id']);
        self::assertSame('IRRF_MONTHLY', $resource['type']);
        self::assertSame(2026, $resource['year']);
        self::assertSame([
            [
                'id' => 'bracket-id',
                'min_amount' => '4664.69',
                'max_amount' => null,
                'rate' => '0.275000',
                'deduction' => '908.73',
                'sort_order' => 5,
            ],
        ], $resource['brackets']->all());
    }
}
