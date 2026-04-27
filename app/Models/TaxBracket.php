<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TaxBracket extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'tax_rule_id',
        'min_amount',
        'max_amount',
        'rate',
        'deduction',
        'sort_order',
    ];

    /**
     * @return BelongsTo<TaxRule, TaxBracket>
     */
    public function taxRule(): BelongsTo
    {
        return $this->belongsTo(TaxRule::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_amount' => 'decimal:2',
            'max_amount' => 'decimal:2',
            'rate' => 'decimal:6',
            'deduction' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }
}
