<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use App\Models\sales;
use App\Models\PublicModel;

class salesController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct() 
    {
        $this->judul_halaman_notif = 'Sales';
    }
        
    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new sales())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL,
            $request->limit, $request->offset);
            $todos = (new sales())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search($URL,
            $request->limit, $request->offset, $request->search);
            $todos = (new sales())->get_data_($request->search, $arr_pagination);
            $count = $todos->count();
        }

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    public function store(Request $req): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($req, [
            'brandcode' => 'required',
            'itemname' => 'required',
            'itemcode' => 'required',
            'sales' => 'required',
            'yop' => 'required',
            'mop' => 'required',
            'distcode' => 'required',
        ]);

        try {
            $data['created_by'] = $user_id;
            sales::create($data);

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
                'message' => 'Failed to create data',$e,
            ], 403);
        }
    }

    public function destroy(Request $req, int $id)
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';

        try {
            $todo = sales::findOrFail($id);

            sales::where('id', $id)->update(['deleted_by' => $user_id]);
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
            $sales = sales::findOrFail($id);
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $sales
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
            'itemname' => 'required',
            'itemcode' => 'required',
            'sales' => 'required',
            'yop' => 'required',
            'mop' => 'required',
            'distcode' => 'required',
        ]);

        try {
            $sales = sales::findOrFail($id);
            $sales->fill($data)->save();

            sales::where('id', $id)->update([
                'updated_by' => $user_id,
            ]);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Updated successfully',
                'data' => $sales
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
                $data['itemname'] = $value['itemname'];
                $data['itemcode'] = $value['itemcode'];
                $data['sales'] = $value['sales'];
                $data['yop'] = $value['yop'];
                $data['mop'] = $value['mop'];                
                $data['distcode'] = $value['distcode'];                

                $data['created_by'] = 'user_test';
                $data['updated_by'] = 'user_test';
                sales::create($data);
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
