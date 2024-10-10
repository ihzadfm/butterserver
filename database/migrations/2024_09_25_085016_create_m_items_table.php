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
        Schema::create('m_item', function (Blueprint $table) {
            $table->id(); // id (pk, bigint)

            $table->string('item_code')->unique(); // item_code (string, unique)
            $table->string('item_name')->nullable(); // item_name (string, nullable)
            $table->string('code_bars')->nullable(); // code_bars (string, nullable)
            $table->string('mnft_code')->nullable(); // mnft_code (string, nullable)
            $table->boolean('sales_item')->nullable(); // sales_item (boolean, nullable)
            $table->boolean('purch_item')->nullable(); // purch_item (boolean, nullable)
            $table->boolean('return_item')->nullable(); // return_item (boolean, nullable)
            $table->double('uom1')->nullable(); // uom1 (double, nullable)
            $table->double('uom2')->nullable(); // uom2 (double, nullable)
            $table->double('uom3')->nullable(); // uom3 (double, nullable)
            $table->double('uom4')->nullable(); // uom4 (double, nullable)
            $table->integer('obj_type')->nullable(); // obj_type (int, nullable)
            $table->boolean('flag_active')->default(true)->nullable(); // flag_active (boolean, default: true, nullable)

            $table->string('created_by')->nullable(); // created_by (string, nullable)
            $table->string('updated_by')->nullable(); // updated_by (string, nullable)
            $table->string('deleted_by')->nullable(); // deleted_by (string, nullable)
            
            // Timestamps (created_at, updated_at)
            $table->timestamps(); 
            // Soft delete (deleted_at)
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_items');
    }
};
