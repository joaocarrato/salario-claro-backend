<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PayrollSimulationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'gross_salary' => $this->gross_salary,
            'dependents' => $this->dependents,
            'discounts' => [
                'inss' => $this->inss_amount,
                'irrf' => $this->irrf_amount,
                'transport' => $this->transport_discount,
                'meal' => $this->meal_discount,
                'health_plan' => $this->health_plan_discount,
                'other' => $this->other_discounts,
            ],
            'irrf_base' => $this->irrf_base,
            'total_discounts' => $this->total_discounts,
            'net_salary' => $this->net_salary,
            'effective_rate' => $this->effective_rate,
            'calculation_year' => $this->calculation_year,
            'created_at' => $this->created_at?->toJSON(),
            'updated_at' => $this->updated_at?->toJSON(),
        ];
    }
}
