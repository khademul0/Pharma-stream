<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('generic_name')->nullable()->index();
            $table->foreignId('category_id')->constrained('medicine_categories')->cascadeOnDelete();
            $table->string('barcode')->nullable()->index();
            $table->enum('unit_type', ['box', 'strip', 'tablet'])->default('tablet');
            $table->decimal('conversion_rate', 12, 4)->default(1);
            $table->unsignedInteger('low_stock_threshold')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'barcode']);
            $table->index(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
