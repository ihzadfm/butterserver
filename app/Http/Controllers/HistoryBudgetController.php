<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Models\HistoryBudget;

class HistoryBudgetController extends Controller
{
    protected $judul_halaman_notif;
    
    public function __construct()
    {
        $this->judul_halaman_notif = 'History Budget';
    }

    public function deleteAll(): JsonResponse
    {
        try {
            $rowCount = DB::table('history_budget')->count();
            DB::table('history_budget')->truncate();

            Log::info('All data in history_budget table has been deleted.', ['row_count' => $rowCount]);

            return response()->json([
                'status' => true,
                'message' => 'All data deleted successfully',
                'deleted_rows' => $rowCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete all data from history_budget table.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function paging(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);

        $data = (new HistoryBudget())->getData($search, ['limit' => $limit, 'offset' => $offset]);
        $count = HistoryBudget::count();

        return response()->json([
            'data' => $data,
            'total' => $count,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $this->validate($request, [
                'kodehistory' => 'required|string',
                'kodebeban1' => 'required|string',
                'kodebeban2' => 'required|string',
                'bulan1' => 'required|string',
                'bulan2' => 'required|string',
                'amount' => 'required|string',
                'amountbulan1' => 'required|string',
                'amountbulan2' => 'required|string',
            ]);

            HistoryBudget::create($data);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data created successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $record = HistoryBudget::findOrFail($id);
            $record->delete();

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $record = HistoryBudget::findOrFail($id);
            return response()->json([
                'status' => true,
                'data' => $record,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $this->validate($request, [
                'kodehistory' => 'required|string',
                'kodebeban1' => 'required|string',
                'kodebeban2' => 'required|string',
                'bulan1' => 'required|string',
                'bulan2' => 'required|string',
                'amount' => 'required|string',
                'amountbulan1' => 'required|string',
                'amountbulan2' => 'required|string',
            ]);

            $record = HistoryBudget::findOrFail($id);
            $record->update($data);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data updated successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeBulky(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = $request->validate([
                'csv' => 'required|array',
            ]);

            foreach ($data['csv'] as $record) {
                HistoryBudget::create($record);
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Bulk data created successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create bulk data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
