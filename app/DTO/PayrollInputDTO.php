<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class PayrollInputDTO
{
    public function __construct(
        public float $grossSalary,
        public int $dependents,
        public float $transportDiscount,
        public float $mealDiscount,
        public float $healthPlanDiscount,
        public float $otherDiscounts,
        public int $calculationYear,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            grossSalary: (float) $data['gross_salary'],
            dependents: (int) ($data['dependents'] ?? 0),
            transportDiscount: (float) ($data['transport_discount'] ?? 0),
            mealDiscount: (float) ($data['meal_discount'] ?? 0),
            healthPlanDiscount: (float) ($data['health_plan_discount'] ?? 0),
            otherDiscounts: (float) ($data['other_discounts'] ?? 0),
            calculationYear: (int) $data['calculation_year'],
        );
    }
}
