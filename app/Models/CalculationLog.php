<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CalculationLog extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'payroll_simulation_id',
        'step',
        'input',
        'output',
    ];

    /**
     * @return BelongsTo<PayrollSimulation, CalculationLog>
     */
    public function payrollSimulation(): BelongsTo
    {
        return $this->belongsTo(PayrollSimulation::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'input' => 'array',
            'output' => 'array',
        ];
    }
}
