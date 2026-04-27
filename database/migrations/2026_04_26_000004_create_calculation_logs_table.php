<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculation_logs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('payroll_simulation_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('step');
            $table->json('input');
            $table->json('output');
            $table->timestamps();

            $table->index(['payroll_simulation_id', 'step']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculation_logs');
    }
};
