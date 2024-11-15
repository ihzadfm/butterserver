<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Models\accrued;
use App\Models\PublicModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

class accruedController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct()
    {
        $this->judul_halaman_notif = 'accrued';
    }

    public function deleteAll()
    {
        try {
            $rowCount = DB::table('accrued')->count();
            DB::table('accrued')->truncate();

            Log::info('All data in accrued table has been deleted.', ['row_count' => $rowCount]);

            return response()->json([
                'status' => true,
                'message' => 'All data deleted successfully',
                'deleted_rows' => $rowCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete all data from accrued table.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function paging(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;
        $query = accrued::query();

        if ($request->search) {
            $query->where('nama_pp', 'LIKE', '%' . $request->search . '%');
        }

        $count = $query->count();
        $data = $query->skip($offset)->take($limit)->get();

        return response()->json([
            'nomorBaris' => $offset,
            'count' => $count,
            'next' => $count > $offset + $limit ? URL::current() . "?offset=" . ($offset + $limit) . "&limit=" . $limit : null,
            'previous' => $offset > 0 ? URL::current() . "?offset=" . ($offset - $limit) . "&limit=" . $limit : null,
            'results' => $data // langsung menaruh data di `results` tanpa nested `data`
        ]);
    }

    public function store(Request $req): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($req, [
            'no_pp' => 'required',
            'id_detail' => 'required|integer',
            'kodebeban' => 'required',
            'nilai_pp' => 'required|numeric',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
            'jenis_realisasi' => 'required',
            'no_realisasi' => 'required',
            'tgl_realisasi' => 'required|date_format:d/m/Y',
            'nilai_realisasi' => 'required|numeric',
            'status_pp' => 'required',
            'divisi' => 'required',
            'nama_pp' => 'required',
            'jenis_accrued' => 'required',
            'status_approved' => 'required',
            'status_closed' => 'required',
            'tgl_input' => 'required',
        ]);

        try {
            // Convert tgl_realisasi to Y-m-d format
            $data['tgl_realisasi'] = Carbon::createFromFormat('d/m/Y', $data['tgl_realisasi'])->format('Y-m-d');

            // Tambahkan tgl_input dengan nilai waktu saat ini
            $data['tgl_input'] = Date::now();
            $data['created_by'] = $user_id;

            accrued::create($data);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Created successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create data',
                'error' => $e->getMessage()
            ], 403);
        }
    }

    public function destroy(Request $req, int $id)
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';

        try {
            $item = accrued::findOrFail($id);
            $item->update(['deleted_by' => $user_id]);
            $item->delete();

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Deleted successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete data',
                'error' => $e->getMessage()
            ], 409);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $item = accrued::findOrFail($id);
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $req, int $id)
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($req, [
            'no_pp' => 'required',
            'kodebeban' => 'required',
            'id_detail' => 'required|integer',
            'nilai_pp' => 'required|numeric',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
            'jenis_realisasi' => 'required',
            'no_realisasi' => 'required',
            'tgl_realisasi' => 'required|date_format:d/m/Y',
            'nilai_realisasi' => 'required|numeric',
            'status_pp' => 'required',
            'divisi' => 'required',
            'nama_pp' => 'required',
            'jenis_accrued' => 'required',
            'status_approved' => 'required',
            'status_closed' => 'required',
            'tgl_input' => 'required',
        ]);

        try {
            $item = accrued::findOrFail($id);
            $item->fill($data)->update(['updated_by' => $user_id]);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Updated successfully',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'Failed to update data',
                'error' => $e->getMessage()
            ], 409);
        }
    }

    public function storeBulky(Request $req): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data_csv = json_decode(json_encode($req->csv), true);
            foreach ($data_csv as $value) {
                // $tgl = Carbon::createFromFormat('d/m/Y', $value['tgl_realisasi'])->format('Y-m-d');
                $value['tgl_realisasi'] = Carbon::createFromFormat('d/m/Y', $value['tgl_realisasi'])->format('Y-m-d');

                // Format tgl_input to Y-m-d H:i:s if it exists in the data
                if (isset($value['tgl_input']) && !empty($value['tgl_input'])) {
                    $value['tgl_input'] = Carbon::createFromFormat('d/m/Y H:i', $value['tgl_input'])->format('Y-m-d H:i:s');
                }
                $value['created_by']   = 'user_test';
                $value['updated_by']   = 'user_test';
                $data['tgl_input'] = Date::now();

                accrued::create($value);
            }

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Created successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create data',
                'error' => $e->getMessage()
            ], 403);
        }
    }
}
