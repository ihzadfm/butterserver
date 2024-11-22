<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use App\Models\reclass;
use App\Models\BudgetMonitoring;
use App\Models\HistoryBudget;
use App\Models\PublicModel;
use Psy\Command\HistoryCommand;

class reclassController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct()
    {
        $this->judul_halaman_notif = 'Master Reclass';
    }

    
    public function searchhistorybudget(Request $request): JsonResponse
{
    try {
        // Ambil parameter search jika ada
        $search = $request->input('search', '');

        // Query dengan pencarian jika search tidak kosong
        $query = HistoryBudget::query();
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('kodebeban1', 'LIKE', "%$search%")
                  ->orWhere('kodebeban2', 'LIKE', "%$search%")
                  ->orWhere('bulan1', 'LIKE', "%$search%")
                  ->orWhere('bulan2', 'LIKE', "%$search%")
                  ->orWhere('amount', 'LIKE', "%$search%");
            });
        }

        // Paginate data sesuai permintaan frontend
        $data = $query->paginate($request->input('perPage', 25));

        return response()->json([
            'code' => 201,
            'status' => true,
            'data' => $data,
            'message' => "Data berhasil di fetch.",
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'code' => 500,
            'status' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}


    public function pagingkodebeban(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new reclass())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new reclass())->get_data_kodebeban($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new reclass())->get_data_kodebeban($request->search, $arr_pagination);
            $count = count($todos);
        }

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    public function getbudgetmonitoring(Request $request): JsonResponse
    {
        try {
            $data = BudgetMonitoring::all();

            return response()->json([
                'code' => 201,
                'status' => true,
                'data' => $data,
                'message' => "Data berhasil di fetch.",
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function gethistorybudget(Request $request): JsonResponse
    {
        try {
            $data = HistoryBudget::paginate($request->perPage);

            return response()->json([
                'code' => 201,
                'status' => true,
                'data' => $data,
                'message' => "Data berhasil di fetch.",
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getdatakodebebanbybulan(Request $request)
    {
        try {
            DB::beginTransaction();
            $bulan_mapping = [
                1 => 'jan',
                2 => 'feb',
                3 => 'mar',
                4 => 'apr',
                5 => 'mei',
                6 => 'jun',
                7 => 'jul',
                8 => 'ags',
                9 => 'sep',
                10 => 'okt',
                11 => 'nop',
                12 => 'des',
            ];

            // Mapping bulan pertama dan kedua
            $namabulan = $bulan_mapping[$request->month1] ?? null;
            $namabulan2 = $bulan_mapping[$request->month2] ?? null;

            // Validasi jika salah satu bulan tidak valid
            if (!$namabulan || !$namabulan2) {
                return response()->json(['error' => 'Invalid month input'], 400);
            }

            // Ambil data bulan pertama dari tabel reclass
            $databulan = BudgetMonitoring::where("kodebeban", $request->kodebeban1)->first();
            if (!$databulan) {
                return response()->json(['error' => 'Data not found in reclass'], 404);
            }
            $budgetbulan1 = $databulan->$namabulan;

            $databulan->update([
                $namabulan => $databulan->$namabulan - $request->amount,
            ]);

            // Ambil data target yang akan diupdate dari tabel BudgetMonitoring
            $updatebudget = BudgetMonitoring::where("kodebeban", $request->kodebeban2)->first();
            if (!$updatebudget) {
                return response()->json(['error' => 'Data not found in BudgetMonitoring'], 404);
            }
            $budgetbulan2 = $updatebudget->$namabulan2;
            $updatebudget->update([
                $namabulan2 => $updatebudget->$namabulan2 + $request->amount,
            ]);

            $historybudget = HistoryBudget::create([
                "kodebeban1" => $request->kodebeban1,
                "kodebeban2" => $request->kodebeban2,
                "bulan1" => $namabulan,
                "bulan2" => $namabulan2,
                "amountbulan1" => $budgetbulan1,
                "amountbulan2" => $budgetbulan2,
                "amount" => $request->amount,
            ]);
            DB::commit();
            return response()->json(['message' => 'Data updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function pagingrealokasi(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new reclass())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new reclass())->get_data_brand($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new reclass())->get_data_brand($request->search, $arr_pagination);
            $count = count($todos);
        }

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    public function pagingbudget(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new reclass())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new reclass())->get_data_budget($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new reclass())->get_data_budget($request->search, $arr_pagination);
            $count = count($todos);
        }

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    public function pagingbulan(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new reclass())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new reclass())->get_data_bulan($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new reclass())->get_data_bulan($request->search, $arr_pagination);
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
            reclass::create($data);

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
            $todo = reclass::findOrFail($id);

            reclass::where('id', $id)->update(['deleted_by' => $user_id]);
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
            $distcode = reclass::findOrFail($id);
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
            $distcode = reclass::findOrFail($id);
            $distcode->fill($data)->save();

            reclass::where('id', $id)->update([
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
                reclass::create($data);
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
