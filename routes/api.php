<?php

declare(strict_types=1);

use App\Http\Controllers\PayrollSimulationController;
use App\Http\Controllers\TaxRuleController;
use Illuminate\Support\Facades\Route;

Route::get('/health', static fn (): array => ['status' => 'ok']);

Route::post('/payroll/calculate', [PayrollSimulationController::class, 'calculate']);
Route::post('/payroll/compare', [PayrollSimulationController::class, 'compare']);
Route::get('/simulations', [PayrollSimulationController::class, 'index']);
Route::post('/simulations', [PayrollSimulationController::class, 'store']);
Route::get('/simulations/{simulation}', [PayrollSimulationController::class, 'show']);
Route::delete('/simulations/{simulation}', [PayrollSimulationController::class, 'destroy']);

Route::get('/tax-rules', [TaxRuleController::class, 'index']);
Route::get('/tax-rules/{taxRule}', [TaxRuleController::class, 'show']);
