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
        Schema::create('performance_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agreement_id')->constrained('performance_agreements')->onDelete('cascade');
            $table->text('activity_goal');
            $table->text('performance_indicator');
            $table->decimal('quality_target', 5, 2);
            $table->decimal('quantity_target', 10, 2);
            $table->string('unit', 50);
            $table->boolean('jan')->default(false);
            $table->boolean('feb')->default(false);
            $table->boolean('mar')->default(false);
            $table->boolean('apr')->default(false);
            $table->boolean('may')->default(false);
            $table->boolean('jun')->default(false);
            $table->boolean('jul')->default(false);
            $table->boolean('aug')->default(false);
            $table->boolean('sep')->default(false);
            $table->boolean('oct')->default(false);
            $table->boolean('nov')->default(false);
            $table->boolean('dec')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_targets');
    }
};
