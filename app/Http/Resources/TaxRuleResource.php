<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TaxRuleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'year' => $this->year,
            'effective_from' => $this->effective_from,
            'effective_to' => $this->effective_to,
            'metadata' => $this->metadata,
            'brackets' => $this->whenLoaded('brackets', fn () => $this->brackets->map(static fn ($bracket): array => [
                'id' => $bracket->id,
                'min_amount' => $bracket->min_amount,
                'max_amount' => $bracket->max_amount,
                'rate' => $bracket->rate,
                'deduction' => $bracket->deduction,
                'sort_order' => $bracket->sort_order,
            ])->values()),
        ];
    }
}
