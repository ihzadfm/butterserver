<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Models\penampung;
use App\Models\PublicModel;
use App\Models\targetpenjualan;

class penampungController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct()
    {
        $this->judul_halaman_notif = 'Penampung';
    }

    public function insertrealizationterm(Request $req){
        return $req -> all();
        penampung::create($req->all());
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
                penampung::where('kodebeban', $kodebeban)->update([
                    'apr' => $budgetafterq1,
                    'mei' => 0,
                    'jun' => 0,
                ]);
            } else if ($term == 2) {
                penampung::where('kodebeban', $kodebeban)->update([
                    'jul' => $budgetafterq1,
                    'ags' => 0,
                    'sep' => 0,
                ]);
            } else if ($term == 3) {
                penampung::where('kodebeban', $kodebeban)->update([
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
                penampung::where('kodebeban', $kodebeban)->update([
                    'apr' => $budgetafterq2,
                    'mei' => 0,
                    'jun' => 0,
                ]);
            } else if ($term == 2) {
                penampung::where('kodebeban', $kodebeban)->update([
                    'jul' => $budgetafterq2,
                    'ags' => 0,
                    'sep' => 0,
                ]);
            } else if ($term == 3) {
                penampung::where('kodebeban', $kodebeban)->update([
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
                penampung::where('kodebeban', $kodebeban)->update([
                    'apr' => $budgetafterq3,
                    'mei' => 0,
                    'jun' => 0,
                ]);
            } else if ($term == 2) {
                penampung::where('kodebeban', $kodebeban)->update([
                    'jul' => $budgetafterq3,
                    'ags' => 0,
                    'sep' => 0,
                ]);
            } else if ($term == 3) {
                penampung::where('kodebeban', $kodebeban)->update([
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

    public function deleteAll()
    {
        try {
            $rowCount = DB::table('penampung')->count();
            DB::table('penampung')->truncate();

            Log::info('All data in penampung table has been deleted.', ['row_count' => $rowCount]);

            return response()->json([
                'status' => true,
                'message' => 'All data deleted successfully',
                'deleted_rows' => $rowCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete all data from penampung table.', [
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
            $count = (new penampung())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new penampung())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new penampung())->get_data_($request->search, $arr_pagination);
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
            'kodebeban' => 'required',
            'term' => 'required',
            'realizationterm' => 'required',
        ]);

        try {
            $data['created_by'] = $user_id;
            penampung::create($data);

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
            $todo = penampung::findOrFail($id);

            penampung::where('id', $id)->update(['deleted_by' => $user_id]);
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
            $brand = penampung::findOrFail($id);
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $brand
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
            'term' => 'required',
            'realizationterm' => 'required',
        ]);

        try {
            $brand = penampung::findOrFail($id);
            $brand->fill($data)->save();

            penampung::where('id', $id)->update([
                'updated_by' => $user_id,
            ]);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Updated successfully',
                'data' => $brand
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
                $data['term'] = $value['term'];
                $data['realizationterm'] = $value['realizationterm'];

                $data['created_by'] = 'user_test';
                $data['updated_by'] = 'user_test';
                penampung::create($data);
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
