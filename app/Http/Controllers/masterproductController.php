<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use App\Models\masterproduct;
use App\Models\PublicModel;

class masterproductController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct() 
    {
        $this->judul_halaman_notif = 'Master Product';
    }
        
    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new masterproduct())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL,
            $request->limit, $request->offset);
            $todos = (new masterproduct())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search($URL,
            $request->limit, $request->offset, $request->search);
            $todos = (new masterproduct())->get_data_($request->search, $arr_pagination);
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
            'brandname' => 'required',
            'itemcode' => 'required',
            'mtgcode' => 'required',
            'parentcode' => 'required',
            'itemname' => 'required',
        ]);

        try {
            $data['created_by'] = $user_id;
            masterproduct::create($data);

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
            $todo = masterproduct::findOrFail($id);

            masterproduct::where('id', $id)->update(['deleted_by' => $user_id]);
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
            $product = masterproduct::findOrFail($id);
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $product
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
            'itemcode' => 'required',
            'mtgcode' => 'required',
            'parentcode' => 'required',
            'itemname' => 'required',
        ]);

        try {
            $product = masterproduct::findOrFail($id);
            $product->fill($data)->save();

            masterproduct::where('id', $id)->update([
                'updated_by' => $user_id,
            ]);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Updated successfully',
                'data' => $product
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

        // try {
            $data_csv = json_decode(json_encode($req->csv), true);
            foreach ($data_csv as $key => $value) {
                $data = [];
                $data['brandcode'] = $value['brandcode'];
$data['brandname'] = $value['brandname'];
$data['itemcode'] = $value['itemcode'];
$data['mtgcode'] = $value['mtgcode'];
$data['parentcode'] = $value['parentcode'];
$data['itemname'] = $value['itemname'];

                $data['created_by'] = 'user_test';
                $data['updated_by'] = 'user_test';
                masterproduct::create($data);
            }

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Created successfully',
            ], 201);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Failed to create data',
        //         'error' => $e,
        //     ], 403);
        // }
    }
}
