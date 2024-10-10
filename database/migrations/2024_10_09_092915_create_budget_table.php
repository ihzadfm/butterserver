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
        Schema::create('budget_monitorings', function (Blueprint $table) {
            $table->id();
            $table->string('kodebeban', 50); // kodebeban
            $table->string('kodedivisi'); // kodedivisi
            $table->string('expense', 15, 2); // expense
            $table->string('expensegroup'); // expensegroup
            $table->string('groupbeban', 50); // groupbeban
            $table->string('groupcostcenter'); // groupcostcenter
            $table->string('costcenter', 50); // costcenter
            $table->string('totalfinal', 15, 2); // totalfinal
            $table->string('total', 15, 2); // total

            // Monthly fields
            $table->string('jan', 15, 2)->nullable(); // jan
            $table->string('feb', 15, 2)->nullable(); // feb
            $table->string('mar', 15, 2)->nullable(); // mar
            $table->string('apr', 15, 2)->nullable(); // apr
            $table->string('mei', 15, 2)->nullable(); // mei
            $table->string('jun', 15, 2)->nullable(); // jun
            $table->string('jul', 15, 2)->nullable(); // jul
            $table->string('ags', 15, 2)->nullable(); // ags
            $table->string('sep', 15, 2)->nullable(); // sep
            $table->string('okt', 15, 2)->nullable(); // okt
            $table->string('nop', 15, 2)->nullable(); // nop
            $table->string('des', 15, 2)->nullable(); // des

            // Realization fields (N1 to N12)
            $table->string('realizationn1', 15, 2)->nullable(); // realizationn1
            $table->string('realizationn2', 15, 2)->nullable(); // realizationn2
            $table->string('realizationn3', 15, 2)->nullable(); // realizationn3
            $table->string('realizationn4', 15, 2)->nullable(); // realizationn4
            $table->string('realizationn5', 15, 2)->nullable(); // realizationn5
            $table->string('realizationn6', 15, 2)->nullable(); // realizationn6
            $table->string('realizationn7', 15, 2)->nullable(); // realizationn7
            $table->string('realizationn8', 15, 2)->nullable(); // realizationn8
            $table->string('realizationn9', 15, 2)->nullable(); // realizationn9
            $table->string('realizationn10', 15, 2)->nullable(); // realizationn10
            $table->string('realizationn11', 15, 2)->nullable(); // realizationn11
            $table->string('realizationn12', 15, 2)->nullable(); // realizationn12

            // Other fields
            $table->string('totalrealization', 15, 2); // totalrealization
            $table->year('year'); // year
            $table->string('userid')->nullable(); // userid

            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_monitorings');
    }
};
