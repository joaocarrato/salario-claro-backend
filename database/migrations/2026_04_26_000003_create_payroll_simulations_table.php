<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_simulations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->decimal('gross_salary', 12, 2);
            $table->integer('dependents')->default(0);
            $table->decimal('transport_discount', 12, 2)->default(0);
            $table->decimal('meal_discount', 12, 2)->default(0);
            $table->decimal('health_plan_discount', 12, 2)->default(0);
            $table->decimal('other_discounts', 12, 2)->default(0);
            $table->decimal('inss_amount', 12, 2);
            $table->decimal('irrf_base', 12, 2);
            $table->decimal('irrf_amount', 12, 2);
            $table->decimal('total_discounts', 12, 2);
            $table->decimal('net_salary', 12, 2);
            $table->decimal('effective_rate', 8, 6);
            $table->integer('calculation_year')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_simulations');
    }
};
