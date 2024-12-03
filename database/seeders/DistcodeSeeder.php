<?php

namespace Database\Seeders;

use App\Models\distcode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistcodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        distcode::create([
            'distcode' => 'TPS',
            'distname' => 'TARA PARA SEMESTA',
            'created_by' => 1
        ]);
        
        distcode::create([
            'distcode' => 'TRS',
            'distname' => 'TIGA RAKSA SATRIA',
            'created_by' => 1
        ]);
        
        distcode::create([
            'distcode' => 'SGS',
            'distname' => 'SINERGI GLOBAL SERVIS',
            'created_by' => 1
        ]);
        
        distcode::create([
            'distcode' => 'MBO',
            'distname' => 'MARTINA BERTO',
            'created_by' => 1
        ]);
        
        distcode::create([
            'distcode' => 'PPG',
            'distname' => 'PARIT PADANG GLOBAL',
            'created_by' => 1
        ]);
        
        distcode::create([
            'distcode' => 'IDM',
            'distname' => 'INDOMARET',
            'created_by' => 1
        ]);
        
        distcode::create([
            'distcode' => 'ALF',
            'distname' => 'ALFAMART',
            'created_by' => 1
        ]);
        
        distcode::create([
            'distcode' => 'PV',
            'distname' => 'PENTA VALENT',
            'created_by' => 1
        ]);        
    }
}
