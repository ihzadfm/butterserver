<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use App\Models\distcode;
use App\Models\PublicModel;

class distcodeController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct()
    {
        $this->judul_halaman_notif = 'Master Distribution Code';
    }

    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new distcode())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new distcode())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new distcode())->get_data_($request->search, $arr_pagination);
            $count = count($todos);

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
            'distcode' => 'required',
            'distname' => 'required',
        ]);

        try {
            $data['created_by'] = $user_id;
            distcode::create($data);

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
            $todo = distcode::findOrFail($id);

            distcode::where('id', $id)->update(['deleted_by' => $user_id]);
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

    public function show(String $id): JsonResponse
    {
        try {
            $distcode = distcode::findOrFail($id);
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $distcode
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
            'distcode' => 'required',
            'distname' => 'required',
        ]);

        try {
            $distcode = distcode::findOrFail($id);
            $distcode->fill($data)->save();

            distcode::where('id', $id)->update([
                'updated_by' => $user_id,
            ]);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Updated successfully',
                'data' => $distcode
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
                $data['distcode'] = $value['distcode'];
                $data['distname'] = $value['distname'];

                $data['created_by'] = 'user_test';
                $data['updated_by'] = 'user_test';
                distcode::create($data);
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
