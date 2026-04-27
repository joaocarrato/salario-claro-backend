<?php

declare(strict_types=1);

namespace App\Services\TaxRules;

use App\Exceptions\InvalidTaxRuleException;
use App\Models\TaxRule;

final class TaxRuleResolver
{
    public function resolve(string $type, int $year): TaxRule
    {
        $rule = TaxRule::query()
            ->where('type', $type)
            ->where('year', $year)
            ->with(['brackets' => static function ($query): void {
                $query->orderBy('sort_order');
            }])
            ->first();

        if (! $rule instanceof TaxRule) {
            throw InvalidTaxRuleException::missing($type, $year);
        }

        if ($rule->brackets->isEmpty()) {
            throw InvalidTaxRuleException::invalid($type, $year, 'no tax brackets configured.');
        }

        return $rule;
    }
}
