<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Models\BudgetMonitoring;
use App\Models\PublicModel;


class BudgetMonitoringController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct() 
    {
        $this->judul_halaman_notif = 'Budget Monitoring';
    }
        
    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new BudgetMonitoring())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL,
            $request->limit, $request->offset);
            $todos = (new BudgetMonitoring())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search($URL,
            $request->limit, $request->offset, $request->search);
            $todos = (new BudgetMonitoring())->get_data_($request->search, $arr_pagination);
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
            'kodebeban' => 'required',
            'kodedivisi' => 'required',
            'expense' => 'required',
            'expensegroup' => 'required',
            'groupbeban' => 'required',
            'groupcostcenter' => 'required',
            'costcenter' => 'required',
            'totalfinal' => 'required',
            'total' => 'required',
            // Monthly fields
            'jan' => 'required',
            'feb' => 'required',
            'mar' => 'required',
            'apr' => 'required',
            'mei' => 'required',
            'jun' => 'required',
            'jul' => 'required',
            'ags' => 'required',
            'sep' => 'required',
            'okt' => 'required',
            'nop' => 'required',
            'des' => 'required',
            // Realization fields
            'realizationn1' => 'required',
            'realizationn2' => 'required',
            'realizationn3' => 'required',
            'realizationn4' => 'required',
            'realizationn5' => 'required',
            'realizationn6' => 'required',
            'realizationn7' => 'required',
            'realizationn8' => 'required',
            'realizationn9' => 'required',
            'realizationn10' => 'required',
            'realizationn11' => 'required',
            'realizationn12' => 'required',
            'totalrealization' => 'required',
            'year' => 'required',
        ]);

        try {
            $data['created_by'] = $user_id;
            BudgetMonitoring::create($data);

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
    public function deleteAll()
    {
        try {
            $rowCount = DB::table('budget_monitorings')->count();
            DB::table('budget_monitorings')->truncate();

            Log::info('All data in budget_monitorings table has been deleted.', ['row_count' => $rowCount]);

            return response()->json([
                'status' => true,
                'message' => 'All data deleted successfully',
                'deleted_rows' => $rowCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete all data from budget_monitorings table.', [
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

    public function destroy(Request $req, int $id)
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';

        try {
            $todo = BudgetMonitoring::findOrFail($id);

            BudgetMonitoring::where('id', $id)->update(['deleted_by' => $user_id]);
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
            $budget = BudgetMonitoring::findOrFail($id);
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $budget
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
           'kodebeban' => 'required',
            'kodedivisi' => 'required',
            'expense' => 'required',
            'expensegroup' => 'required',
            'groupbeban' => 'required',
            'groupcostcenter' => 'required',
            'totalfinal' => 'required',
            'total' => 'required',
            // Monthly fields
            'jan' => 'required',
            'feb' => 'required',
            'mar' => 'required',
            'apr' => 'required',
            'mei' => 'required',
            'jun' => 'required',
            'jul' => 'required',
            'ags' => 'required',
            'sep' => 'required',
            'okt' => 'required',
            'nop' => 'required',
            'des' => 'required',
            // Realization fields
            'realizationn1' => 'required',
            'realizationn2' => 'required',
            'realizationn3' => 'required',
            'realizationn4' => 'required',
            'realizationn5' => 'required',
            'realizationn6' => 'required',
            'realizationn7' => 'required',
            'realizationn8' => 'required',
            'realizationn9' => 'required',
            'realizationn10' => 'required',
            'realizationn11' => 'required',
            'realizationn12' => 'required',
            'totalrealization' => 'required',
            'year' => 'required',
        ]);

        try {
            $budget = BudgetMonitoring::findOrFail($id);
            $budget->fill($data)->save();

            BudgetMonitoring::where('id', $id)->update([
                'updated_by' => $user_id,
            ]);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Updated successfully',
                'data' => $budget
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
                $data['kodebeban'] = $value['kodebeban'];
                $data['kodedivisi'] = $value['kodedivisi'];
                $data['expense'] = $value['expense'];
                $data['expensegroup'] = $value['expensegroup'];
                $data['groupbeban'] = $value['groupbeban'];
                $data['groupcostcenter'] = $value['groupcostcenter'];
                $data['costcenter'] = $value['costcenter'];
                $data['totalfinal'] = $value['totalfinal'];
                $data['total'] = $value['total'];
                // Monthly fields
                $data['jan'] = $value['jan'];
                $data['feb'] = $value['feb'];
                $data['mar'] = $value['mar'];
                $data['apr'] = $value['apr'];
                $data['mei'] = $value['mei'];
                $data['jun'] = $value['jun'];
                $data['jul'] = $value['jul'];
                $data['ags'] = $value['ags'];
                $data['sep'] = $value['sep'];
                $data['okt'] = $value['okt'];
                $data['nop'] = $value['nop'];
                $data['des'] = $value['des'];

                // Realization fields
                $data['realizationn1'] = $value['realizationn1'];
                $data['realizationn2'] = $value['realizationn2'];
                $data['realizationn3'] = $value['realizationn3'];
                $data['realizationn4'] = $value['realizationn4'];
                $data['realizationn5'] = $value['realizationn5'];
                $data['realizationn6'] = $value['realizationn6'];
                $data['realizationn7'] = $value['realizationn7'];
                $data['realizationn8'] = $value['realizationn8'];
                $data['realizationn9'] = $value['realizationn9'];
                $data['realizationn10'] = $value['realizationn10'];
                $data['realizationn11'] = $value['realizationn11'];
                $data['realizationn12'] = $value['realizationn12'];
                $data['totalrealization'] = $value['totalrealization'];

                // Year field
                $data['year'] = $value['year'];

                $data['created_by'] = 'user test';
                $data['updated_by'] = 'user test';
                BudgetMonitoring::create($data);
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
