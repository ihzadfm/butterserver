<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Models\targetpenjualan;
use App\Models\PublicModel;
use App\Models\BudgetMonitoring;
use App\Models\Penampung;
use Exception;

class targetpenjualanController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct()
    {
        $this->judul_halaman_notif = 'Target Penjualan';
    }

    public function adjusmentaccrued(Request $request): JsonResponse
    {
        $kodebeban = $request->kodebeban;
        $ach = $request->ach;
        try {
            $data = BudgetMonitoring::adjusmentaccrued($kodebeban, $ach);

            return response()->json([
                'code' => 201,
                'status' => true,
                'data' => $data,
                'ach' => $request,
                'message' => $this->judul_halaman_notif . " berhasil di fetch.",
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function adjusmentrealisasi(Request $request): JsonResponse
    {
        $kodebeban = $request->kodebeban;
        $ach = $request->ach;
        try {
            $data = BudgetMonitoring::adjusmentrealisasi($kodebeban, $ach);

            return response()->json([
                'code' => 201,
                'status' => true,
                'data' => $data,
                'ach' => $request,
                'message' => $this->judul_halaman_notif . " berhasil di fetch.",
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
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
            $count = $todos->count();
        }

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    public function showdatabyparameter(String $distcode, String $brandcode, String $yop, String $mop, Request $request): JsonResponse
    {
        $URL =  URL::current();

        // return $request;
        $count = (new targetpenjualan())->count();
        $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
        $todos = (new targetpenjualan())->get_data_param($distcode, $brandcode, $yop, $mop, $arr_pagination);
        // print_r($todos); 

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }
    public function showsuggestionpenampung(String $distcode, String $brandcode, String $term, Request $request): JsonResponse
    {
        $URL =  URL::current();

        // return $request;
        $count = (new targetpenjualan())->count();
        $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
        $todos = (new targetpenjualan())->get_data_penampung($distcode, $brandcode, $term, $arr_pagination);
        // print_r($todos); 

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }
    public function showsuggestionbyparameter(String $distcode, String $brandcode, String $term, Request $request): JsonResponse
    {
        $URL =  URL::current();

        // return $request;
        $count = (new targetpenjualan())->count();
        $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
        $todos = (new targetpenjualan())->get_data_z($distcode, $brandcode, $term, $arr_pagination);
        // print_r($todos); 

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    

    public function updatebudget(String $kodebeban, $term, Request $request): JsonResponse
    {
        $URL =  URL::current();

        $count = (new targetpenjualan())->count();
        $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
        $todos = (new targetpenjualan())->get_data_updatebudget($kodebeban, $term, $arr_pagination);
        
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

    public function update(Request $req, String $kodebeban, String $term)
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($req, []);

        try {
            $URL =  URL::current();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $req->limit, $req->offset);
            $target = (new targetpenjualan)->get_data_updatebudget($kodebeban, $term , $arr_pagination);
            // $target->fill($data)->save();

            $budgetafterb = $target[0]->budgetafterb;


            if ($term == 1) {
                BudgetMonitoring::where('kodebeban', $kodebeban)->update([
                    'apr' => $budgetafterb,
                    'mei' => 0,
                    'jun' => 0,
                ]);
            } else if ($term == 2) {
                BudgetMonitoring::where('kodebeban', $kodebeban)->update([
                    'jul' => $budgetafterb,
                    'ags' => 0,
                    'sep' => 0,
                ]);
            } else if ($term == 3) {
                BudgetMonitoring::where('kodebeban', $kodebeban)->update([
                    'okt' => $budgetafterb,
                    'nop' => 0,
                    'des' => 0,
                ]);
            }

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
    
    public function updatea(Request $req, String $kodebeban, String $term)
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($req, []);

        try {        
            $URL =  URL::current();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $req->limit, $req->offset);
            $target = (new targetpenjualan)->get_data_updatebudget($kodebeban, $term, $arr_pagination);

            $budgetaftera = $target[0]->budgetaftera;

            if ($term == 1) {
                BudgetMonitoring::where('kodebeban', $kodebeban)->update([
                    'apr' => $budgetaftera,
                    'mei' => 0,
                    'jun' => 0,
                ]);
            } else if ($term == 2) {
                BudgetMonitoring::where('kodebeban', $kodebeban)->update([
                    'jul' => $budgetaftera,
                    'ags' => 0,
                    'sep' => 0,
                ]);
            } else if ($term == 3) {
                BudgetMonitoring::where('kodebeban', $kodebeban)->update([
                    'okt' => $budgetaftera,
                    'nop' => 0,
                    'des' => 0,
                ]);
            }

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

    public function updatepenampungq1(Request $req, String $kodebeban, String $term)
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($req, []);

        try {        
            $URL =  URL::current();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $req->limit, $req->offset);
            $target = (new targetpenjualan)->get_data_updatepenampung($kodebeban, $term, $arr_pagination);

            $budgetafterq1 = $target[0]->budgetafterq1;

            if ($term == 1) {
                Penampung::where('kodebeban', $kodebeban)->update([
                    'apr' => $budgetafterq1,
                    'mei' => 0,
                    'jun' => 0,
                ]);
            } else if ($term == 2) {
                Penampung::where('kodebeban', $kodebeban)->update([
                    'jul' => $budgetafterq1,
                    'ags' => 0,
                    'sep' => 0,
                ]);
            } else if ($term == 3) {
                Penampung::where('kodebeban', $kodebeban)->update([
                    'okt' => $budgetafterq1,
                    'nop' => 0,
                    'des' => 0,
                ]);
            }

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
    public function updatepenampungq2(Request $req, String $kodebeban, String $term)
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($req, []);

        try {        
            $URL =  URL::current();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $req->limit, $req->offset);
            $target = (new targetpenjualan)->get_data_updatepenampung($kodebeban, $term, $arr_pagination);

            $budgetafterq2 = $target[0]->budgetafterq2;

            if ($term == 1) {
                BudgetMonitoring::where('kodebeban', $kodebeban)->update([
                    'apr' => $budgetafterq2,
                    'mei' => 0,
                    'jun' => 0,
                ]);
            } else if ($term == 2) {
                BudgetMonitoring::where('kodebeban', $kodebeban)->update([
                    'jul' => $budgetafterq2,
                    'ags' => 0,
                    'sep' => 0,
                ]);
            } else if ($term == 3) {
                BudgetMonitoring::where('kodebeban', $kodebeban)->update([
                    'okt' => $budgetafterq2,
                    'nop' => 0,
                    'des' => 0,
                ]);
            }

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
    public function updatepenampungq3(Request $req, String $kodebeban, String $term)
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($req, []);

        try {        
            $URL =  URL::current();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $req->limit, $req->offset);
            $target = (new targetpenjualan)->get_data_updatepenampung($kodebeban, $term, $arr_pagination);

            $budgetafterq3 = $target[0]->budgetafterq3;

            if ($term == 1) {
                BudgetMonitoring::where('kodebeban', $kodebeban)->update([
                    'apr' => $budgetafterq3,
                    'mei' => 0,
                    'jun' => 0,
                ]);
            } else if ($term == 2) {
                BudgetMonitoring::where('kodebeban', $kodebeban)->update([
                    'jul' => $budgetafterq3,
                    'ags' => 0,
                    'sep' => 0,
                ]);
            } else if ($term == 3) {
                BudgetMonitoring::where('kodebeban', $kodebeban)->update([
                    'okt' => $budgetafterq3,
                    'nop' => 0,
                    'des' => 0,
                ]);
            }

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
