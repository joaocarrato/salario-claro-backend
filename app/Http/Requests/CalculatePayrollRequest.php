<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculatePayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'gross_salary' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'dependents' => ['nullable', 'integer', 'min:0', 'max:20'],
            'transport_discount' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'meal_discount' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'health_plan_discount' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'other_discounts' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'calculation_year' => ['required', 'integer', 'min:2026', 'max:2100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'gross_salary' => 'gross salary',
            'transport_discount' => 'transport discount',
            'meal_discount' => 'meal discount',
            'health_plan_discount' => 'health plan discount',
            'other_discounts' => 'other discounts',
            'calculation_year' => 'calculation year',
        ];
    }
}
