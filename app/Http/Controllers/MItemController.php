<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use App\Models\MItem;
use App\Models\PublicModel;

class MItemController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct() 
    {
        $this->judul_halaman_notif = 'INVENTORY ITEM';
    }
        
    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new MItem())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL,
            $request->limit, $request->offset);
            $todos = (new MItem())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search($URL,
            $request->limit, $request->offset, $request->search);
            $todos = (new MItem())->get_data_($request->search, $arr_pagination);
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
        $user_id = 'USER TEST'; // Sesuaikan dengan ID pengguna yang sebenarnya
        $data = $this->validate($req, [
            'item_code' => 'required|string|unique:m_item',
            'item_name' => 'required|string',
            'code_bars' => 'required|string',
            'mnft_code' => 'required|string',
            'sales_item' => 'required|boolean',
            'purch_item' => 'required|boolean',
            'return_item' => 'required|boolean',
            'uom1' => 'required|numeric',
            'uom2' => 'required|numeric',
            'uom3' => 'required|numeric',
            'uom4' => 'required|numeric',
            'obj_type' => 'required|integer',
        ]);

        try {
            $data['created_by'] = $user_id;
            MItem::create($data);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'created successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'failed to create data',
            ], 403);
        }
    }

    public function destroy(Request $req, int $id): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST'; // Sesuaikan dengan ID pengguna yang sebenarnya

        try {
            $item = MItem::findOrFail($id);

            MItem::where('id', $id)->update(['deleted_by' => $user_id]);
            $item->delete(); // Soft delete

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'deleted successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'failed to delete',
            ], 409);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $item = MItem::findOrFail($id);
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'data is not found',
                'error' => $e
            ], 404);
        }
    }

    public function update(Request $req, int $id): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST'; // Sesuaikan dengan ID pengguna yang sebenarnya
        $data = $this->validate($req, [
            'item_code' => 'required|string|unique:m_item,item_code,' . $id,
            'item_name' => 'required|string',
            'code_bars' => 'nullable|string',
            'mnft_code' => 'nullable|string',
            'sales_item' => 'required|boolean',
            'purch_item' => 'required|boolean',
            'return_item' => 'required|boolean',
            'uom1' => 'nullable|numeric',
            'uom2' => 'nullable|numeric',
            'uom3' => 'nullable|numeric',
            'uom4' => 'nullable|numeric',
            'obj_type' => 'nullable|integer',
        ]);

        try {
            $item = MItem::findOrFail($id);
            $item->fill($data)->save();

            MItem::where('id', $id)->update([
                'updated_by' => $user_id,
            ]);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'updated successfully',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'failed to update data',
            ], 409);
        }
    }
}
