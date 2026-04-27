<?php

declare(strict_types=1);

namespace App\Services\Payroll;

use App\DTO\PayrollInputDTO;
use App\DTO\PayrollResultDTO;

final readonly class PayrollCalculatorService
{
    // TODO: Load this value from IRRF tax rule metadata instead of keeping it here.
    private const float DEPENDENT_DEDUCTION = 189.59;

    public function __construct(
        private InssCalculator $inssCalculator,
        private IrrfCalculator $irrfCalculator,
    ) {}

    public function calculate(PayrollInputDTO $input): PayrollResultDTO
    {
        $grossSalary = round($input->grossSalary, 2);
        $inss = $this->inssCalculator->calculate($grossSalary, $input->calculationYear);
        $dependentDiscount = round($input->dependents * self::DEPENDENT_DEDUCTION, 2);
        $irrfBase = round(max(0.0, $grossSalary - $inss - $dependentDiscount), 2);
        $irrf = $this->irrfCalculator->calculate($irrfBase, $grossSalary, $input->calculationYear);
        $optionalDiscounts = round(
            $input->transportDiscount
            + $input->mealDiscount
            + $input->healthPlanDiscount
            + $input->otherDiscounts,
            2,
        );
        $totalDiscounts = round($inss + $irrf + $optionalDiscounts, 2);
        $netSalary = round($grossSalary - $totalDiscounts, 2);
        $effectiveRate = $grossSalary > 0
            ? round($totalDiscounts / $grossSalary, 6)
            : 0.0;

        return new PayrollResultDTO(
            grossSalary: $grossSalary,
            inssAmount: $inss,
            irrfBase: $irrfBase,
            irrfAmount: $irrf,
            transportDiscount: round($input->transportDiscount, 2),
            mealDiscount: round($input->mealDiscount, 2),
            healthPlanDiscount: round($input->healthPlanDiscount, 2),
            otherDiscounts: round($input->otherDiscounts, 2),
            totalDiscounts: $totalDiscounts,
            netSalary: $netSalary,
            effectiveRate: $effectiveRate,
            calculationYear: $input->calculationYear,
            steps: [
                [
                    'step' => 'INSS',
                    'output' => [
                        'amount' => $inss,
                    ],
                ],
                [
                    'step' => 'IRRF base',
                    'output' => [
                        'gross_salary' => $grossSalary,
                        'inss_amount' => $inss,
                        'dependents' => $input->dependents,
                        'dependent_deduction' => self::DEPENDENT_DEDUCTION,
                        'base_amount' => $irrfBase,
                    ],
                ],
                [
                    'step' => 'IRRF',
                    'output' => [
                        'amount' => $irrf,
                    ],
                ],
                [
                    'step' => 'Optional discounts',
                    'output' => [
                        'transport_discount' => round($input->transportDiscount, 2),
                        'meal_discount' => round($input->mealDiscount, 2),
                        'health_plan_discount' => round($input->healthPlanDiscount, 2),
                        'other_discounts' => round($input->otherDiscounts, 2),
                        'amount' => $optionalDiscounts,
                    ],
                ],
                [
                    'step' => 'Net salary',
                    'output' => [
                        'total_discounts' => $totalDiscounts,
                        'net_salary' => $netSalary,
                        'effective_rate' => $effectiveRate,
                    ],
                ],
            ],
        );
    }
}
