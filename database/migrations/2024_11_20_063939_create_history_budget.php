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
        Schema::create('history_budget', function (Blueprint $table) {
            $table->id();
            $table->string("kodebeban1");
            $table->string("kodebeban2");
            $table->string("bulan1");
            $table->string("bulan2");
            $table->string("amount");
            $table->string("amountbulan1");
            $table->string("amountbulan2");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_budget');
    }
};
