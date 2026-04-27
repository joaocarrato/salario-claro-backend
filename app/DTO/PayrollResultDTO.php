<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class PayrollResultDTO
{
    /**
     * @param  array<int, array<string, mixed>>  $steps
     */
    public function __construct(
        public float $grossSalary,
        public float $inssAmount,
        public float $irrfBase,
        public float $irrfAmount,
        public float $transportDiscount,
        public float $mealDiscount,
        public float $healthPlanDiscount,
        public float $otherDiscounts,
        public float $totalDiscounts,
        public float $netSalary,
        public float $effectiveRate,
        public int $calculationYear,
        public array $steps = [],
    ) {}

    /**
     * @return array{
     *     gross_salary: float,
     *     inss_amount: float,
     *     irrf_base: float,
     *     irrf_amount: float,
     *     transport_discount: float,
     *     meal_discount: float,
     *     health_plan_discount: float,
     *     other_discounts: float,
     *     total_discounts: float,
     *     net_salary: float,
     *     effective_rate: float,
     *     calculation_year: int,
     *     steps: array<int, array<string, mixed>>
     * }
     */
    public function toArray(): array
    {
        return [
            'gross_salary' => round($this->grossSalary, 2),
            'inss_amount' => round($this->inssAmount, 2),
            'irrf_base' => round($this->irrfBase, 2),
            'irrf_amount' => round($this->irrfAmount, 2),
            'transport_discount' => round($this->transportDiscount, 2),
            'meal_discount' => round($this->mealDiscount, 2),
            'health_plan_discount' => round($this->healthPlanDiscount, 2),
            'other_discounts' => round($this->otherDiscounts, 2),
            'total_discounts' => round($this->totalDiscounts, 2),
            'net_salary' => round($this->netSalary, 2),
            'effective_rate' => round($this->effectiveRate, 6),
            'calculation_year' => $this->calculationYear,
            'steps' => $this->steps,
        ];
    }
}
