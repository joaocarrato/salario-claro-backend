<?php

declare(strict_types=1);

namespace App\Services\Payroll;

use App\Models\TaxBracket;
use App\Services\TaxRules\TaxRuleResolver;

final readonly class IrrfCalculator
{
    public function __construct(
        private TaxRuleResolver $taxRuleResolver,
    ) {}

    public function calculate(float $baseAmount, float $grossSalary, int $year): float
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

        $tax = ($baseAmount * (float) $bracket->rate) - (float) $bracket->deduction;

        return round(max(0.0, $tax), 2);
    }
}
