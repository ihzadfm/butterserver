<?php

namespace App\Http\Controllers;

use App\Models\MKaryawan;

class MKaryawanController extends Controller
{
    public function getKaryawans() {
        try {
            $datas = MKaryawan::select(['nama', 'kode_department'])->with(['department'])->get();

            return response()->json([
                'status' => 'success',
                'message' => 'get data karyawan success!',
                'data' => $datas
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'get data karyawan failed!',
                'error' => $e->getMessage()
            ], 400);
        }
        
    }
}
