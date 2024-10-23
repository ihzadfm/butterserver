<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Models\targetpenjualan;
use App\Models\PublicModel;

class targetpenjualanController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct()
    {
        $this->judul_halaman_notif = 'Target Penjualan';
    }

    // targetpenjualanController.php

public function searchData(Request $request)
{
    // Validasi input terlebih dahulu
    $this->validate($request, [
        'dist_name' => 'required|string',
        'brand_code' => 'required|string',
        'year' => 'required|numeric',
        'month' => 'required|numeric|min:1|max:12',
    ]);

    // Mendapatkan nilai dari request
    $dist_name = $request->input('dist_name');
    $brand_code = $request->input('brand_code');
    $year = $request->input('year');
    $month = $request->input('month');

    // Query untuk mendapatkan data
    $results = DB::table('distcode')
        ->join('masterbrand', 'distcode.brandcode', '=', 'masterbrand.brandcode')
        ->select('distcode.*', 'masterbrand.brandname')
        ->where('distcode.distname', 'like', '%' . $dist_name . '%')
        ->where('distcode.brandcode', 'like', '%' . $brand_code . '%')
        ->whereYear('distcode.created_at', $year)
        ->whereMonth('distcode.created_at', $month)
        ->get();

    // Check apakah data ditemukan
    if ($results->isEmpty()) {
        return response()->json(['message' => 'Data not found'], 404);
    }

    // Mengembalikan data
    return response()->json($results, 200);
}


    public function deleteAll()
    {
        try {
            $rowCount = DB::table('targetpenjualan')->count();
            DB::table('targetpenjualan')->truncate();

            Log::info('All data in targetpenjualan table has been deleted.', ['row_count' => $rowCount]);

            return response()->json([
                'status' => true,
                'message' => 'All data deleted successfully',
                'deleted_rows' => $rowCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete all data from targetpenjualan table.', [
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
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new targetpenjualan())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new targetpenjualan())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new targetpenjualan())->get_data_($request->search, $arr_pagination);
            if($request->query('distcode')){
                $todos->where('distcode','=',$request->query('distcode'));
            }
            if($request->query('brandcode')){
                $todos->where('brandcode','=',$request->query('brandcode'));
            }
            if($request->query('yop')){
                $todos->where('yop','=',$request->query('yop'));
            }
            if($request->query('mop')){
                $todos->where('mop','=',$request->query('mop'));
            }
            $count = $todos->count();
        }

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }
    public function getsearch(String $distcode, String $brandcode, String $yop, String $mop, Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->$distcode, $brandcode, $yop, $mop,)) {
            $URL =  URL::current();

            // return $request;
                $count = (new targetpenjualan())->count();
                $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
                $todos = (new targetpenjualan())->getsearch($distcode, $brandcode, $yop, $mop,$arr_pagination);
                // print_r($todos); 
    
            return response()->json(
                (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
                200
            );
        }
    }


    public function store(Request $req): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($req, [
            'brandcode' => 'required',
            'brandname' => 'required',
            'itemname' => 'required',
            'itemcode' => 'required',
            'target' => 'required',
            'distcode' => 'required',
            'distname' => 'required',
            'yop' => 'required',
            'mop' => 'required',
        ]);

        try {
            $data['created_by'] = $user_id;
            targetpenjualan::create($data);

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
                $e,
            ], 403);
        }
    }

    public function destroy(Request $req, int $id)
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';

        try {
            $todo = targetpenjualan::findOrFail($id);

            targetpenjualan::where('id', $id)->update(['deleted_by' => $user_id]);
            $todo->delete();

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
            ], 409);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $target = targetpenjualan::findOrFail($id);
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $target
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'error' => $e
            ], 404);
        }
    }

    public function update(Request $req, int $id)
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($req, [
            'brandcode' => 'required',
            'brandname' => 'required',
            'itemname' => 'required',
            'itemcode' => 'required',
            'target' => 'required',
            'yop' => 'required',
            'mop' => 'required',
        ]);

        try {
            $target = targetpenjualan::findOrFail($id);
            $target->fill($data)->save();

            targetpenjualan::where('id', $id)->update([
                'updated_by' => $user_id,
            ]);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Updated successfully',
                'data' => $target
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'Failed to update data',
            ], 409);
        }
    }

    public function storeBulky(Request $req): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data_csv = json_decode(json_encode($req->csv), true);
            foreach ($data_csv as $key => $value) {
                $data = [];
                $data['brandcode'] = $value['brandcode'];
                $data['brandname'] = $value['brandname'];
                $data['itemname'] = $value['itemname'];
                $data['itemcode'] = $value['itemcode'];
                $data['target'] = $value['target'];
                $data['distcode'] = $value['distcode'];
                $data['distname'] = $value['distname'];
                $data['yop'] = $value['yop'];
                $data['mop'] = $value['mop'];

                $data['created_by'] = 'user_test';
                $data['updated_by'] = 'user_test';
                targetpenjualan::create($data);
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
                'error' => $e,
            ], 403);
        }
    }
}