<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TaxRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaxRule>
 */
final class TaxRuleFactory extends Factory
{
    protected $model = TaxRule::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = (int) $this->faker->unique()->numberBetween(2026, 2100);

        return [
            'name' => 'Test Tax Rule '.$year,
            'type' => $this->faker->randomElement(['INSS_EMPLOYEE', 'IRRF_MONTHLY']),
            'year' => $year,
            'effective_from' => "{$year}-01-01",
            'effective_to' => "{$year}-12-31",
            'metadata' => null,
        ];
    }

    public function inssEmployee(int $year = 2026): self
    {
        return $this->state(fn (): array => [
            'name' => "INSS Employee Test Rule {$year}",
            'type' => 'INSS_EMPLOYEE',
            'year' => $year,
            'effective_from' => "{$year}-01-01",
            'effective_to' => "{$year}-12-31",
            'metadata' => null,
        ]);
    }

    public function irrfMonthly(int $year = 2026): self
    {
        return $this->state(fn (): array => [
            'name' => "IRRF Monthly Test Rule {$year}",
            'type' => 'IRRF_MONTHLY',
            'year' => $year,
            'effective_from' => "{$year}-01-01",
            'effective_to' => "{$year}-12-31",
            'metadata' => [
                'dependent_deduction' => '189.59',
                'simplified_monthly_discount' => '607.20',
            ],
        ]);
    }
}
