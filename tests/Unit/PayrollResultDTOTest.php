<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\PayrollResultDTO;
use PHPUnit\Framework\TestCase;

final class PayrollResultDTOTest extends TestCase
{
    public function test_it_converts_to_api_ready_array_with_rounded_values(): void
    {
        $dto = new PayrollResultDTO(
            grossSalary: 5000.555,
            inssAmount: 512.345,
            irrfBase: 4488.211,
            irrfAmount: 120.456,
            transportDiscount: 100.125,
            mealDiscount: 200.555,
            healthPlanDiscount: 300.444,
            otherDiscounts: 50.999,
            totalDiscounts: 1283.987,
            netSalary: 3716.568,
            effectiveRate: 0.2567894,
            calculationYear: 2026,
            steps: [
                ['step' => 'example'],
            ],
        );

        self::assertSame([
            'gross_salary' => 5000.56,
            'inss_amount' => 512.35,
            'irrf_base' => 4488.21,
            'irrf_amount' => 120.46,
            'transport_discount' => 100.13,
            'meal_discount' => 200.56,
            'health_plan_discount' => 300.44,
            'other_discounts' => 51.0,
            'total_discounts' => 1283.99,
            'net_salary' => 3716.57,
            'effective_rate' => 0.256789,
            'calculation_year' => 2026,
            'steps' => [
                ['step' => 'example'],
            ],
        ], $dto->toArray());
    }
}
