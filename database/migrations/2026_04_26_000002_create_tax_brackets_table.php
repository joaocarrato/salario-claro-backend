<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_brackets', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('tax_rule_id')->constrained()->cascadeOnDelete();
            $table->decimal('min_amount', 12, 2);
            $table->decimal('max_amount', 12, 2)->nullable();
            $table->decimal('rate', 8, 6);
            $table->decimal('deduction', 12, 2)->default(0);
            $table->integer('sort_order');
            $table->timestamps();

            $table->index(['tax_rule_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_brackets');
    }
};
