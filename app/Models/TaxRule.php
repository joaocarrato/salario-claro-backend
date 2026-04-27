<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class TaxRule extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'type',
        'year',
        'effective_from',
        'effective_to',
        'metadata',
    ];

    /**
     * @return HasMany<TaxBracket>
     */
    public function brackets(): HasMany
    {
        return $this->hasMany(TaxBracket::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'effective_from' => 'date',
            'effective_to' => 'date',
            'metadata' => 'array',
        ];
    }
}
