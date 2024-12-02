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
        Schema::create('accrued', function (Blueprint $table) {
            $table->id();
            $table->string('no_pp', 50); // no_pp
            $table->string('id_detail'); // id_detail
            $table->string('kodebeban', 50); // kode_beban
            $table->decimal('nilai_pp', 15, 2); // nilai_pp
            $table->integer('bulan'); // bulan
            $table->year('tahun'); // tahun
            $table->string('jenis_realisasi', 50); // jenis_realisasi
            $table->string('no_realisasi', 50); // no_realisasi
            $table->date('tgl_realisasi'); // tgl_realisasi
            $table->decimal('nilai_realisasi', 15, 2); // nilai_realisasi
            $table->string('status_pp', 10); // status_pp
            $table->string('divisi', 10); // divisi
            $table->string('nama_pp', 255); // nama_pp
            $table->string('jenis_accrued', 10); // jenis_accrued
            $table->string('status_approved', 10); // status_approved
            $table->string('status_closed', 10); // status_closed
            $table->timestamp('tgl_input')->nullable(); // tgl_input

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
        Schema::dropIfExists('accrued');
    }
};
