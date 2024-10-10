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
        Schema::create('m_item_inventory', function (Blueprint $table) {
            $table->id(); // id (pk, bigint)
            
            $table->string('item_code')->nullable(); // item_code (string, nullable)
            $table->string('whs_code')->nullable(); // whs_code (string, nullable)
            $table->double('on_hand')->nullable(); // on_hand (double, nullable)
            $table->double('on_order')->nullable(); // on_order (double, nullable)
            $table->double('min_stock')->nullable(); // min_stock (double, nullable)
            $table->double('max_stock')->nullable(); // max_stock (double, nullable)
            $table->double('min_order')->nullable(); // min_order (double, nullable)
            $table->double('reorder_qty')->nullable(); // reorder_qty (double, nullable)
            $table->boolean('on_priority')->nullable(); // on_priority (boolean, nullable)
            $table->boolean('flag_active')->default(true)->nullable(); // flag_active (boolean, default: true, nullable)
            
            $table->string('created_by')->nullable(); // created_by (string, nullable)
            $table->string('updated_by')->nullable(); // updated_by (string, nullable)
            $table->string('deleted_by')->nullable(); // deleted_by (string, nullable)
            $table->timestamps(); // created_at, updated_at (auto timestamps, nullable)
            $table->softDeletes(); // deleted_at (soft delete, nullable)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_item_inventories');
    }
};
