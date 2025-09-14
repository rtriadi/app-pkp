<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('monthly_assessments')->onDelete('cascade');
            $table->foreignId('target_id')->constrained('performance_targets')->onDelete('cascade');
            $table->decimal('ak_target', 5, 2);
            $table->decimal('quantity_realization', 10, 2);
            $table->decimal('quality_realization', 5, 2);
            $table->decimal('score', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_details');
    }
};
