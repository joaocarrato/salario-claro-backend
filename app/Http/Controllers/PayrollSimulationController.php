<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\PayrollInputDTO;
use App\DTO\PayrollResultDTO;
use App\Exceptions\InvalidTaxRuleException;
use App\Http\Requests\CalculatePayrollRequest;
use App\Http\Requests\ComparePayrollRequest;
use App\Http\Requests\StoreSimulationRequest;
use App\Http\Resources\PayrollSimulationResource;
use App\Models\PayrollSimulation;
use App\Services\Payroll\PayrollCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class PayrollSimulationController extends Controller
{
    public function __construct(
        private readonly PayrollCalculatorService $payrollCalculatorService,
    ) {}

    public function calculate(CalculatePayrollRequest $request): JsonResponse
    {
        try {
            $result = $this->payrollCalculatorService->calculate(
                PayrollInputDTO::fromArray($request->validated())
            );
        } catch (InvalidTaxRuleException $exception) {
            return $this->domainError($exception);
        }

        return response()->json($this->calculationResponse($result));
    }

    public function compare(ComparePayrollRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $year = (int) $validated['calculation_year'];

        try {
            $first = $this->payrollCalculatorService->calculate(
                PayrollInputDTO::fromArray([...$validated['first'], 'calculation_year' => $year])
            );
            $second = $this->payrollCalculatorService->calculate(
                PayrollInputDTO::fromArray([...$validated['second'], 'calculation_year' => $year])
            );
        } catch (InvalidTaxRuleException $exception) {
            return $this->domainError($exception);
        }

        return response()->json([
            'first' => $this->calculationResponse($first),
            'second' => $this->calculationResponse($second),
            'difference' => [
                'gross_salary' => round($second->grossSalary - $first->grossSalary, 2),
                'net_salary' => round($second->netSalary - $first->netSalary, 2),
                'total_discounts' => round($second->totalDiscounts - $first->totalDiscounts, 2),
            ],
        ]);
    }

    public function index(): AnonymousResourceCollection
    {
        return PayrollSimulationResource::collection(
            PayrollSimulation::query()->latest()->paginate(15)
        );
    }

    public function store(StoreSimulationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $input = PayrollInputDTO::fromArray($validated);
            $result = $this->payrollCalculatorService->calculate($input);
        } catch (InvalidTaxRuleException $exception) {
            return $this->domainError($exception);
        }

        $simulation = PayrollSimulation::query()->create([
            'user_id' => null,
            'title' => $validated['title'] ?? null,
            'gross_salary' => $result->grossSalary,
            'dependents' => $input->dependents,
            'transport_discount' => $result->transportDiscount,
            'meal_discount' => $result->mealDiscount,
            'health_plan_discount' => $result->healthPlanDiscount,
            'other_discounts' => $result->otherDiscounts,
            'inss_amount' => $result->inssAmount,
            'irrf_base' => $result->irrfBase,
            'irrf_amount' => $result->irrfAmount,
            'total_discounts' => $result->totalDiscounts,
            'net_salary' => $result->netSalary,
            'effective_rate' => $result->effectiveRate,
            'calculation_year' => $result->calculationYear,
        ]);

        return PayrollSimulationResource::make($simulation)
            ->response()
            ->setStatusCode(201);
    }

    public function show(PayrollSimulation $simulation): PayrollSimulationResource
    {
        return PayrollSimulationResource::make($simulation);
    }

    public function destroy(PayrollSimulation $simulation): JsonResponse
    {
        $simulation->delete();

        return response()->json([
            'message' => 'Simulation deleted successfully.',
        ]);
    }

    private function domainError(InvalidTaxRuleException $exception): JsonResponse
    {
        return response()->json([
            'message' => 'Tax rule not found or invalid.',
            'error' => 'INVALID_TAX_RULE',
        ], 422);
    }

    /**
     * @return array<string, mixed>
     */
    private function calculationResponse(PayrollResultDTO $result): array
    {
        return [
            'gross_salary' => round($result->grossSalary, 2),
            'discounts' => [
                'inss' => round($result->inssAmount, 2),
                'irrf' => round($result->irrfAmount, 2),
                'transport' => round($result->transportDiscount, 2),
                'meal' => round($result->mealDiscount, 2),
                'health_plan' => round($result->healthPlanDiscount, 2),
                'other' => round($result->otherDiscounts, 2),
            ],
            'irrf_base' => round($result->irrfBase, 2),
            'total_discounts' => round($result->totalDiscounts, 2),
            'net_salary' => round($result->netSalary, 2),
            'effective_rate' => round($result->effectiveRate, 6),
            'calculation_year' => $result->calculationYear,
            'calculation_steps' => $result->steps,
        ];
    }
}
