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
        Schema::create('monthly_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agreement_id')->constrained('performance_agreements')->onDelete('cascade');
            $table->tinyInteger('month');
            $table->year('year');
            $table->enum('status', ['draft', 'submitted', 'approved']);
            $table->decimal('total_score', 5, 2);
            $table->string('grade', 20);
            $table->foreignId('assessor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('supervisor_id')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_assessments');
    }
};
