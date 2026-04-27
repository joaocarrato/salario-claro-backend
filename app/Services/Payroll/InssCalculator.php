<?php

declare(strict_types=1);

namespace App\Services\Payroll;

use App\Models\TaxBracket;
use App\Services\TaxRules\TaxRuleResolver;

final readonly class InssCalculator
{
    public function __construct(
        private TaxRuleResolver $taxRuleResolver,
    ) {}

    public function calculate(float $grossSalary, int $year): float
    {
        if ($grossSalary <= 0) {
            return 0.0;
        }

        $rule = $this->taxRuleResolver->resolve('INSS_EMPLOYEE', $year);
        $grossSalaryCents = $this->toCents($grossSalary);
        $contribution = 0.0;

        /** @var TaxBracket $bracket */
        foreach ($rule->brackets as $bracket) {
            $minCents = $this->toCents((float) $bracket->min_amount);
            $maxCents = $bracket->max_amount === null
                ? $grossSalaryCents
                : $this->toCents((float) $bracket->max_amount);

            if ($grossSalaryCents < $minCents) {
                continue;
            }

            $upperCents = min($grossSalaryCents, $maxCents);
            $inclusiveBoundaryAdjustment = $minCents > 0 ? 1 : 0;
            $taxableCents = max(0, $upperCents - $minCents + $inclusiveBoundaryAdjustment);

            if ($taxableCents === 0) {
                continue;
            }

            $contribution += ($taxableCents / 100) * (float) $bracket->rate;

            if ($grossSalaryCents <= $maxCents) {
                break;
            }
        }

        return round($contribution, 2);
    }

    private function toCents(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
