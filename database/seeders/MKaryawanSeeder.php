<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\MKaryawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class MKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = (new MKaryawan)->getTable();
        if (Schema::hasTable($tableName)) {
            $rowCount = MKaryawan::count();
            if ($rowCount > 0) MKaryawan::truncate();

            $sequance = $tableName . "_id_seq";
            DB::statement("ALTER SEQUENCE $sequance RESTART 1");
        }

        $schema = [
            [
                "nama" => 'Karyawan 1',
                "kode_department" => 'it',
                "nik" => '123901293183',
                "no_hp" => '0812471724',
                "umur" => 20,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
                "created_by" => 'seeder',
            ],
            [
                "nama" => 'Karyawan 1',
                "kode_department" => 'it',
                "nik" => '123901293184',
                "no_hp" => '0812471724',
                "umur" => 20,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
                "created_by" => 'seeder',
            ],
            [
                "nama" => 'Karyawan 1',
                "kode_department" => 'fnc',
                "nik" => '123901293185',
                "no_hp" => '0812471724',
                "umur" => 20,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
                "created_by" => 'seeder',
            ],
        ];

        MKaryawan::insert($schema);
    }
}
