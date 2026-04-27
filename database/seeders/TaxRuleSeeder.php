<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TaxRule;
use Illuminate\Database\Seeder;

final class TaxRuleSeeder extends Seeder
{
    public function run(): void
    {
        TaxRule::query()->updateOrCreate([
            'type' => 'INSS_EMPLOYEE',
            'year' => 2026,
            'effective_from' => '2026-01-01',
        ], [
            'name' => 'INSS Employee Progressive Monthly Table 2026',
            'effective_to' => '2026-12-31',
            'metadata' => null,
        ]);

        TaxRule::query()->updateOrCreate([
            'type' => 'IRRF_MONTHLY',
            'year' => 2026,
            'effective_from' => '2026-01-01',
        ], [
            'name' => 'IRRF Monthly Table 2026',
            'effective_to' => '2026-12-31',
            'metadata' => [
                'dependent_deduction' => '189.59',
                'simplified_monthly_discount' => '607.20',
            ],
        ]);
    }
}
