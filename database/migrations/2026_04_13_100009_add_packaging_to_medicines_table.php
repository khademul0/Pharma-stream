<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->unsignedInteger('units_per_strip')->default(10)->after('conversion_rate');
            $table->unsignedInteger('strips_per_box')->default(1)->after('units_per_strip');
        });
    }

    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['units_per_strip', 'strips_per_box']);
        });
    }
};
