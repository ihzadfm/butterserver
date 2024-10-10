<?php

namespace App\Http\Controllers;

use App\Models\MUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MUserController extends Controller
{
    public function getData(): JsonResponse
    {
        try {
            $todos = (new MUser())->getData();
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $todos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'failed to get data',
                'error' => $e
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

    public function update(Request $req, int $id)
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($req, [
            'nama' => 'required',
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
}
