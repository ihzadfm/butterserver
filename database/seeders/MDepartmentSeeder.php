<?php

namespace Database\Seeders;

use App\Models\MDepartment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = (new MDepartment)->getTable();
        if (Schema::hasTable($tableName)) {
            $rowCount = MDepartment::count();
            if ($rowCount > 0) MDepartment::truncate();

            $sequance = $tableName . "_id_seq";
            DB::statement("ALTER SEQUENCE $sequance RESTART 1");
        }

        $data = [
            [
                'kode_department' => 'it',
                'nama' => 'IT',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'kode_department' => 'acc',
                'nama' => 'Accounting',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'kode_department' => 'fnc',
                'nama' => 'Finance',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'kode_department' => 'mkt',
                'nama' => 'Marketing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        MDepartment::insert($data);
    }
}
