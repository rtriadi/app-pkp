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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nip', 20)->unique();
            $table->string('name');
            $table->string('rank_grade', 50);
            $table->string('position');
            $table->foreignId('work_unit_id')->constrained()->onDelete('cascade');
            $table->enum('employee_status', ['active', 'inactive', 'retired']);
            $table->date('hire_date');
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
