<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PayrollSimulation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayrollSimulation>
 */
final class PayrollSimulationFactory extends Factory
{
    protected $model = PayrollSimulation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'title' => $this->faker->sentence(3),
            'gross_salary' => '5000.00',
            'dependents' => 0,
            'transport_discount' => '0.00',
            'meal_discount' => '0.00',
            'health_plan_discount' => '0.00',
            'other_discounts' => '0.00',
            'inss_amount' => '501.51',
            'irrf_base' => '4392.80',
            'irrf_amount' => '0.00',
            'total_discounts' => '501.51',
            'net_salary' => '4498.49',
            'effective_rate' => '0.100302',
            'calculation_year' => 2026,
        ];
    }
}
