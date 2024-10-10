<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;

use App\Models\MMUserModel;
use App\Models\MUser;
use App\Models\PublicModel;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;

class MMUserController extends Controller
{
    protected $judul_halaman_notif;
    public function _construct() 
    {
        $this->judul_halaman_notif = 'MASTER USER';
    }
        
    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new MMUserModel())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL,
            $request->limit, $request->offset);
            $todos = (new MMUserModel())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search($URL,
            $request->limit, $request->offset, $request->search);
            $todos = (new MMUserModel())->get_data_($request->search, $arr_pagination);
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
            'nama' => 'required',
            'nik' => 'required',
            'alamat' => 'required',
            'telp' => 'required',
        ]);

        try {
            $data['created_by'] = $user_id;
            MUser::create($data);

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

    public function destroy(Request $req, int $id)
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';

        try {
            $todo = MUser::findOrFail($id);

            MUser::where('id', $id)->update(['deleted_by' => $user_id]);
            $todo->delete();
            // Untuk me-restore softdelete

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
            $user = MUser::findOrFail($id);
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'data is not found',
                'error' => $e
            ], 404);
        }
    }

    public function update(Request $req, int $id)
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($req, [
            'nama' => 'required',
            'nik' => 'required',
            'telp' => 'required',
            'alamat' => 'required',
        ]);

        try {
            $todos = MUser::findOrFail($id);
            $todos->fill($data)->save();

            MUser::where('id', $id)->update([
                'updated_by' => $user_id,
            ]);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'updated successfully',
                'data' => $todos
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

    public function storeBulky(Request $req): JsonResponse
    {
        DB::beginTransaction();

        try {
        $data_csv = json_decode(json_encode($req->csv), true);
        foreach ($data_csv as $key => $value) {
            $data=array();
            $data['nama'] = $value['nama'];
            $data['nik'] = $value['nik'];
            $data['telp'] = $value['telp'];
            $data['alamat'] = $value['alamat'];

            $data['created_by'] = 'ssss';
            $data['updated_by'] = 'ssss';
            $todos = MMUserModel::create($data);
            
            }
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
                'e'=>$e,
            ], 403);
        }
    }

}