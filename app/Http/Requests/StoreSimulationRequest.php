<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class StoreSimulationRequest extends CalculatePayrollRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:120'],
            ...parent::rules(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'title',
            ...parent::attributes(),
        ];
    }
}
