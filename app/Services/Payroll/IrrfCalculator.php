<?php

declare(strict_types=1);

namespace App\Services\Payroll;

use App\Models\TaxBracket;
use App\Services\TaxRules\TaxRuleResolver;

final readonly class IrrfCalculator
{
    // Law-specific 2026 monthly IRRF reduction constants.
    private const float IRRF_2026_FULL_REDUCTION_LIMIT = 5000.00;
    private const float IRRF_2026_PARTIAL_REDUCTION_LIMIT = 7350.00;
    private const float IRRF_2026_MAX_MONTHLY_REDUCTION = 312.89;
    private const float IRRF_2026_PARTIAL_REDUCTION_FIXED = 978.62;
    private const float IRRF_2026_PARTIAL_REDUCTION_FACTOR = 0.133145;

    public function __construct(
        private TaxRuleResolver $taxRuleResolver,
    ) {}

    public function calculate(float $baseAmount, float $monthlyTaxableIncome, int $year): float
    {
        if ($baseAmount <= 0) {
            return 0.0;
        }

        $rule = $this->taxRuleResolver->resolve('IRRF_MONTHLY', $year);

        /** @var TaxBracket|null $bracket */
        $bracket = $rule->brackets->first(static function (TaxBracket $bracket) use ($baseAmount): bool {
            $minAmount = (float) $bracket->min_amount;
            $maxAmount = $bracket->max_amount === null ? null : (float) $bracket->max_amount;

            return $baseAmount >= $minAmount
                && ($maxAmount === null || $baseAmount <= $maxAmount);
        });

        if (! $bracket instanceof TaxBracket) {
            return 0.0;
        }

        $regularIrrf = max(0.0, ($baseAmount * (float) $bracket->rate) - (float) $bracket->deduction);
        $taxReduction = $this->monthlyTaxReduction($regularIrrf, $monthlyTaxableIncome, $year);

        return round(max(0.0, $regularIrrf - $taxReduction), 2);
    }

    private function monthlyTaxReduction(float $regularIrrf, float $monthlyTaxableIncome, int $year): float
    {
        if ($year !== 2026 || $regularIrrf <= 0) {
            return 0.0;
        }

        if ($monthlyTaxableIncome <= self::IRRF_2026_FULL_REDUCTION_LIMIT) {
            return min($regularIrrf, self::IRRF_2026_MAX_MONTHLY_REDUCTION);
        }

        if ($monthlyTaxableIncome <= self::IRRF_2026_PARTIAL_REDUCTION_LIMIT) {
            $reduction = self::IRRF_2026_PARTIAL_REDUCTION_FIXED
                - (self::IRRF_2026_PARTIAL_REDUCTION_FACTOR * $monthlyTaxableIncome);

            return min($regularIrrf, max(0.0, $reduction));
        }

        return 0.0;
    }
}
