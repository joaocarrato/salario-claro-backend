<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TaxBracket;
use App\Models\TaxRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaxBracket>
 */
final class TaxBracketFactory extends Factory
{
    protected $model = TaxBracket::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tax_rule_id' => TaxRule::factory(),
            'min_amount' => '0.00',
            'max_amount' => '1000.00',
            'rate' => '0.075000',
            'deduction' => '0.00',
            'sort_order' => 1,
        ];
    }

    public function forRange(
        string $minAmount,
        ?string $maxAmount,
        string $rate,
        string $deduction,
        int $sortOrder,
    ): self {
        return $this->state(fn (): array => [
            'min_amount' => $minAmount,
            'max_amount' => $maxAmount,
            'rate' => $rate,
            'deduction' => $deduction,
            'sort_order' => $sortOrder,
        ]);
    }
}
