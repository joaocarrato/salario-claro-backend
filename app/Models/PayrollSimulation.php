<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class PayrollSimulation extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'gross_salary',
        'dependents',
        'transport_discount',
        'meal_discount',
        'health_plan_discount',
        'other_discounts',
        'inss_amount',
        'irrf_base',
        'irrf_amount',
        'total_discounts',
        'net_salary',
        'effective_rate',
        'calculation_year',
    ];

    /**
     * @return BelongsTo<User, PayrollSimulation>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<CalculationLog>
     */
    public function calculationLogs(): HasMany
    {
        return $this->hasMany(CalculationLog::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gross_salary' => 'decimal:2',
            'dependents' => 'integer',
            'transport_discount' => 'decimal:2',
            'meal_discount' => 'decimal:2',
            'health_plan_discount' => 'decimal:2',
            'other_discounts' => 'decimal:2',
            'inss_amount' => 'decimal:2',
            'irrf_base' => 'decimal:2',
            'irrf_amount' => 'decimal:2',
            'total_discounts' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'effective_rate' => 'decimal:6',
            'calculation_year' => 'integer',
        ];
    }
}
