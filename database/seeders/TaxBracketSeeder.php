<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TaxBracket;
use App\Models\TaxRule;
use Illuminate\Database\Seeder;
use RuntimeException;

final class TaxBracketSeeder extends Seeder
{
    public function run(): void
    {
        $inssRule = $this->rule('INSS_EMPLOYEE');
        $irrfRule = $this->rule('IRRF_MONTHLY');

        $this->seedBrackets($inssRule, [
            ['min_amount' => '0.00', 'max_amount' => '1621.00', 'rate' => '0.075000', 'deduction' => '0.00'],
            ['min_amount' => '1621.01', 'max_amount' => '2902.84', 'rate' => '0.090000', 'deduction' => '0.00'],
            ['min_amount' => '2902.85', 'max_amount' => '4354.27', 'rate' => '0.120000', 'deduction' => '0.00'],
            ['min_amount' => '4354.28', 'max_amount' => '8475.55', 'rate' => '0.140000', 'deduction' => '0.00'],
        ]);

        $this->seedBrackets($irrfRule, [
            ['min_amount' => '0.00', 'max_amount' => '2428.80', 'rate' => '0.000000', 'deduction' => '0.00'],
            ['min_amount' => '2428.81', 'max_amount' => '2826.65', 'rate' => '0.075000', 'deduction' => '182.16'],
            ['min_amount' => '2826.66', 'max_amount' => '3751.05', 'rate' => '0.150000', 'deduction' => '394.16'],
            ['min_amount' => '3751.06', 'max_amount' => '4664.68', 'rate' => '0.225000', 'deduction' => '675.49'],
            ['min_amount' => '4664.69', 'max_amount' => null, 'rate' => '0.275000', 'deduction' => '908.73'],
        ]);
    }

    private function rule(string $type): TaxRule
    {
        $rule = TaxRule::query()
            ->where('type', $type)
            ->where('year', 2026)
            ->first();

        if (! $rule instanceof TaxRule) {
            throw new RuntimeException("Tax rule [{$type}] for 2026 was not found.");
        }

        return $rule;
    }

    /**
     * @param  list<array{min_amount: string, max_amount: string|null, rate: string, deduction: string}>  $brackets
     */
    private function seedBrackets(TaxRule $rule, array $brackets): void
    {
        foreach ($brackets as $index => $bracket) {
            TaxBracket::query()->updateOrCreate([
                'tax_rule_id' => $rule->id,
                'sort_order' => $index + 1,
            ], $bracket);
        }
    }
}
