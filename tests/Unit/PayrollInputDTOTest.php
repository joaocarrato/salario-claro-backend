<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\PayrollInputDTO;
use PHPUnit\Framework\TestCase;

final class PayrollInputDTOTest extends TestCase
{
    public function test_it_can_be_created_from_array_with_defaults(): void
    {
        $dto = PayrollInputDTO::fromArray([
            'gross_salary' => '5000.50',
            'calculation_year' => '2026',
        ]);

        self::assertSame(5000.50, $dto->grossSalary);
        self::assertSame(0, $dto->dependents);
        self::assertSame(0.0, $dto->transportDiscount);
        self::assertSame(0.0, $dto->mealDiscount);
        self::assertSame(0.0, $dto->healthPlanDiscount);
        self::assertSame(0.0, $dto->otherDiscounts);
        self::assertSame(2026, $dto->calculationYear);
    }

    public function test_it_can_be_created_from_array_with_all_fields(): void
    {
        $dto = PayrollInputDTO::fromArray([
            'gross_salary' => 7500,
            'dependents' => 2,
            'transport_discount' => 100.25,
            'meal_discount' => 250.75,
            'health_plan_discount' => 300,
            'other_discounts' => 50,
            'calculation_year' => 2026,
        ]);

        self::assertSame(7500.0, $dto->grossSalary);
        self::assertSame(2, $dto->dependents);
        self::assertSame(100.25, $dto->transportDiscount);
        self::assertSame(250.75, $dto->mealDiscount);
        self::assertSame(300.0, $dto->healthPlanDiscount);
        self::assertSame(50.0, $dto->otherDiscounts);
        self::assertSame(2026, $dto->calculationYear);
    }
}
