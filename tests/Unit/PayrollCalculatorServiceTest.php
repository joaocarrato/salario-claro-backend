<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\PayrollInputDTO;
use App\DTO\PayrollResultDTO;
use App\Services\Payroll\InssCalculator;
use App\Services\Payroll\IrrfCalculator;
use App\Services\Payroll\PayrollCalculatorService;
use App\Services\TaxRules\TaxRuleResolver;
use Database\Seeders\TaxBracketSeeder;
use Database\Seeders\TaxRuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PayrollCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_a_normal_salary(): void
    {
        $this->seedTaxRules();

        $result = $this->service()->calculate(new PayrollInputDTO(
            grossSalary: 5000.00,
            dependents: 0,
            transportDiscount: 0.0,
            mealDiscount: 0.0,
            healthPlanDiscount: 0.0,
            otherDiscounts: 0.0,
            calculationYear: 2026,
        ));

        self::assertInstanceOf(PayrollResultDTO::class, $result);
        self::assertSame(501.51, $result->inssAmount);
        self::assertSame(4498.49, $result->irrfBase);
        self::assertSame(336.67, $result->irrfAmount);
        self::assertSame(838.18, $result->totalDiscounts);
        self::assertSame(4161.82, $result->netSalary);
        self::assertSame(0.167636, $result->effectiveRate);
        self::assertSame(['INSS', 'IRRF base', 'IRRF', 'Optional discounts', 'Net salary'], array_column($result->steps, 'step'));
    }

    public function test_it_calculates_zero_salary(): void
    {
        $this->seedTaxRules();

        $result = $this->service()->calculate(new PayrollInputDTO(
            grossSalary: 0.0,
            dependents: 0,
            transportDiscount: 0.0,
            mealDiscount: 0.0,
            healthPlanDiscount: 0.0,
            otherDiscounts: 0.0,
            calculationYear: 2026,
        ));

        self::assertSame(0.0, $result->inssAmount);
        self::assertSame(0.0, $result->irrfBase);
        self::assertSame(0.0, $result->irrfAmount);
        self::assertSame(0.0, $result->totalDiscounts);
        self::assertSame(0.0, $result->netSalary);
        self::assertSame(0.0, $result->effectiveRate);
    }

    public function test_it_calculates_salary_with_optional_discounts(): void
    {
        $this->seedTaxRules();

        $result = $this->service()->calculate(new PayrollInputDTO(
            grossSalary: 5000.00,
            dependents: 0,
            transportDiscount: 100.00,
            mealDiscount: 250.00,
            healthPlanDiscount: 300.00,
            otherDiscounts: 50.00,
            calculationYear: 2026,
        ));

        self::assertSame(1538.18, $result->totalDiscounts);
        self::assertSame(3461.82, $result->netSalary);
        self::assertSame(0.307636, $result->effectiveRate);
        self::assertSame(100.0, $result->transportDiscount);
        self::assertSame(250.0, $result->mealDiscount);
        self::assertSame(300.0, $result->healthPlanDiscount);
        self::assertSame(50.0, $result->otherDiscounts);
    }

    public function test_it_calculates_salary_with_dependents(): void
    {
        $this->seedTaxRules();

        $result = $this->service()->calculate(new PayrollInputDTO(
            grossSalary: 5000.00,
            dependents: 2,
            transportDiscount: 0.0,
            mealDiscount: 0.0,
            healthPlanDiscount: 0.0,
            otherDiscounts: 0.0,
            calculationYear: 2026,
        ));

        self::assertSame(4119.31, $result->irrfBase);
        self::assertSame(251.35, $result->irrfAmount);
        self::assertSame(752.86, $result->totalDiscounts);
        self::assertSame(4247.14, $result->netSalary);
        self::assertSame(0.150572, $result->effectiveRate);
    }

    private function service(): PayrollCalculatorService
    {
        $resolver = new TaxRuleResolver;

        return new PayrollCalculatorService(
            new InssCalculator($resolver),
            new IrrfCalculator($resolver),
        );
    }

    private function seedTaxRules(): void
    {
        $this->seed([TaxRuleSeeder::class, TaxBracketSeeder::class]);
    }
}
