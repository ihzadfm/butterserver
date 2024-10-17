<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgetterm', function (Blueprint $table) {
            $table->id();
            $table->string('kodebeban', 50); // kodebeban
            $table->decimal('q1', 15, 2)->nullable(); // Q1 = Jan + Feb + Mar
            $table->decimal('q2', 15, 2)->nullable(); // Q2 = Apr + Mei + Jun
            $table->decimal('q3', 15, 2)->nullable(); // Q3 = Jul + Ags + Sep
            $table->decimal('q4', 15, 2)->nullable(); // Q4 = Okt + Nop + Des

            // Quarterly Realization fields (N1 to N4)
            $table->decimal('realizationq1', 15, 2)->nullable(); // realizationq1 = Realization N1 + N2 + N3
            $table->decimal('realizationq2', 15, 2)->nullable(); // realizationq2 = Realization N4 + N5 + N6
            $table->decimal('realizationq3', 15, 2)->nullable(); // realizationq3 = Realization N7 + N8 + N9
            $table->decimal('realizationq4', 15, 2)->nullable(); // realizationq4 = Realization N10 + N11 + N12

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgetterm');
    }
};
