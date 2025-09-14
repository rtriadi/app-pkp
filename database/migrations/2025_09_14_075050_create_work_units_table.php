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
        Schema::create('work_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20);
            $table->foreignId('parent_id')->nullable()->constrained('work_units')->onDelete('set null');
            $table->foreignId('head_employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_units');
    }
};
