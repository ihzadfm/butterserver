<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use App\Models\BudgetTerm;
use App\Models\PublicModel;

class AccruedTermController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct()
    {
        $this->judul_halaman_notif = 'BUDGET TERM';
    }

    /**
     * Get all budget term data.
     */
    public function getBudgetTermData()
    {
        $data = BudgetTerm::all(); // Ambil semua data dari tabel budgetterm
        return response()->json($data);
    }

    /**
     * Paginate data for budget terms with accrued values.
     */
    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        $limit = $request->limit ?? 10;  // Default limit jika tidak ada input
        $offset = $request->offset ?? 0; // Default offset jika tidak ada input
        $search = $request->search ?? ''; // Default pencarian kosong

        // Tentukan pagination
        $arr_pagination = (new PublicModel())->pagination_without_search($URL, $limit, $offset);

        // Ambil data dengan accrued menggunakan fungsi get_data_accrued di model BudgetTerm
        $todos = (new BudgetTerm())->get_data_accrued($search, $arr_pagination);

        // Hitung total data
        $count = count($todos);

        // Response dalam format JSON
        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }
}
