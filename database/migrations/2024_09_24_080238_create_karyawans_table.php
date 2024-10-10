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
        Schema::create('m_karyawans', function (Blueprint $table) {
            $table->id();
            
            $table->string('kode_department')->nullable();
            $table->string('nama')->nullable();
            $table->string('nik')->unique();
            $table->string('no_hp')->nullable();
            $table->string('umur')->unsigned()->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

            $table->foreign('kode_department')->references('kode_department')->on('m_departments')->onDelete('SET NULL')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
