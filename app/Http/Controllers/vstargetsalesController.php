<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use App\Models\targetpenjualan;
use App\Models\PublicModel;

class vstargetsalesController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct()
    {
        $this->judul_halaman_notif = 'Target Penjualan';
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
            $todos = (new targetpenjualan())->get_data_x($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new targetpenjualan())->get_data_x($request->search, $arr_pagination);
            $count = count($todos);
        }

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    public function excelmario(String $distcode, String $brandcode, String $yop, String $mop, Request $request): JsonResponse
    {
        $URL =  URL::current();

        // return $request;
        $count = (new targetpenjualan())->count();
        $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
        $todos = (new targetpenjualan())->get_data_excelmario($distcode, $brandcode, $yop, $mop, $arr_pagination);
        // print_r($todos); 

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }
    public function excelsuggestion(String $distcode, String $brandcode, String $term, Request $request): JsonResponse
    {
        $URL =  URL::current();

        // return $request;
        // $count = (new targetpenjualan())->count();
        $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
        $todos = (new targetpenjualan())->get_data_excelsuggestion($distcode, $brandcode, $term, $arr_pagination);
        $count = count($todos);
       
        // print_r($todos); 

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }
}