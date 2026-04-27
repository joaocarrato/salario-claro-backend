<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ComparePayrollRequest extends FormRequest
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
            'first' => ['required', 'array'],
            'second' => ['required', 'array'],
            'first.gross_salary' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'second.gross_salary' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'first.dependents' => ['nullable', 'integer', 'min:0', 'max:20'],
            'second.dependents' => ['nullable', 'integer', 'min:0', 'max:20'],
            'first.transport_discount' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'second.transport_discount' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'first.meal_discount' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'second.meal_discount' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'first.health_plan_discount' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'second.health_plan_discount' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'first.other_discounts' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'second.other_discounts' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'calculation_year' => ['required', 'integer', 'min:2026', 'max:2100'],
        ];
    }
}
