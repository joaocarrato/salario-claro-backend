<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Requests\CalculatePayrollRequest;
use App\Http\Requests\StoreSimulationRequest;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class FormRequestValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::post('/test/calculate-payroll-request', static function (CalculatePayrollRequest $request): array {
            return $request->validated();
        });

        Route::post('/test/store-simulation-request', static function (StoreSimulationRequest $request): array {
            return $request->validated();
        });
    }

    public function test_calculate_request_accepts_missing_optional_fields(): void
    {
        $response = $this->postJson('/test/calculate-payroll-request', [
            'gross_salary' => 5000,
            'calculation_year' => 2026,
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'gross_salary' => 5000,
                'calculation_year' => 2026,
            ]);
    }

    public function test_calculate_request_rejects_missing_gross_salary(): void
    {
        $response = $this->postJson('/test/calculate-payroll-request', [
            'calculation_year' => 2026,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['gross_salary']);
    }

    public function test_calculate_request_rejects_negative_values(): void
    {
        $response = $this->postJson('/test/calculate-payroll-request', [
            'gross_salary' => -1,
            'dependents' => -1,
            'transport_discount' => -1,
            'meal_discount' => -1,
            'health_plan_discount' => -1,
            'other_discounts' => -1,
            'calculation_year' => 2026,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'gross_salary',
                'dependents',
                'transport_discount',
                'meal_discount',
                'health_plan_discount',
                'other_discounts',
            ]);
    }

    public function test_store_request_accepts_title(): void
    {
        $response = $this->postJson('/test/store-simulation-request', [
            'title' => 'April salary',
            'gross_salary' => 5000,
            'calculation_year' => 2026,
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'title' => 'April salary',
                'gross_salary' => 5000,
                'calculation_year' => 2026,
            ]);
    }

    public function test_store_request_rejects_long_title(): void
    {
        $response = $this->postJson('/test/store-simulation-request', [
            'title' => str_repeat('a', 121),
            'gross_salary' => 5000,
            'calculation_year' => 2026,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }
}
