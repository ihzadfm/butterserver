<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class reclass extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'budget_monitorings';  // Correct table name from the migration
    protected $guarded = [];  // Guarded attributes are kept empty to allow mass assignment

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function get_data_kodebeban($search, $arr_pagination) 
{
    if (!empty($search)) {
        $arr_pagination['offset'] = 0;
    }
    $kodebeban = reclass::all();

    return $kodebeban;
}

    public function get_data_search($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
      
        $data = DB::select("select a.* from (
        select 2 as visorder, id, distcode, distname FROM distcode
        union all select 1, 0,'ALL', 'ALL') as a
        order by a.visorder, distcode, distname");

        return $data;
    }
    public function get_data_brand($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        // $search = strtolower($search);
        // $data = DB::select("SELECT 'ALL', id, distcode, distname FROM distcode");
        $data = DB::select("select a.* from (
        select 2 as visorder, id, brandcode, brandname FROM masterbrand
        union all select 1, 0,'ALL', 'ALL') as a
        order by a.visorder, brandcode, brandname;");

        // $data = "SELECT id, distcode, distname FROM distcode WHERE distcode like '%" . $search . "%' OR distname like '%" . $search . "%'";
        return $data;
    }

    public function get_data_bulan($search, $arr_pagination)
{
    if (!empty($search)) {
        $arr_pagination['offset'] = 0;
    }

    // Mapping bulan ke nama kolom di tabel
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

    // Jika tidak ada pencarian, kembalikan semua bulan
    if (empty($search)) {
        $data = DB::select("
            SELECT 
                1 as month, 'Jan' as month_name
            UNION ALL
            SELECT 
                2 as month, 'Feb' as month_name
            UNION ALL
            SELECT 
                3 as month, 'Mar' as month_name
            UNION ALL
            SELECT 
                4 as month, 'Apr' as month_name
            UNION ALL
            SELECT 
                5 as month, 'Mei' as month_name
            UNION ALL
            SELECT 
                6 as month, 'Jun' as month_name
            UNION ALL
            SELECT 
                7 as month, 'Jul' as month_name
            UNION ALL
            SELECT 
                8 as month, 'Ags' as month_name
            UNION ALL
            SELECT 
                9 as month, 'Sep' as month_name
            UNION ALL
            SELECT 
                10 as month, 'Okt' as month_name
            UNION ALL
            SELECT 
                11 as month, 'Nop' as month_name
            UNION ALL
            SELECT 
                12 as month, 'Des' as month_name
            ORDER BY month;
        ");
        return $data;
    }

    // Query untuk mendapatkan data berdasarkan bulan tertentu
    $bulan_column = $bulan_mapping[$search];
    $query = "
        SELECT 
            id, 
            kodebeban, 
            kodedivisi, 
            expense, 
            $bulan_column AS budget 
        FROM 
            budget_monitorings 
        WHERE 
            $bulan_column IS NOT NULL 
        ORDER BY 
            $bulan_column DESC 
        LIMIT :limit OFFSET :offset;
    ";

    $data = DB::select($query, [
        'limit' => $arr_pagination['limit'] ?? 10,
        'offset' => $arr_pagination['offset'] ?? 0,
    ]);

    return $data;
}

    public function get_data_budget($search, $arr_pagination)
    {
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        // Query untuk mengambil data budget berdasarkan bulan dan kodebeban
        $query = "
            SELECT 
                bm.kodebeban,
                CASE 
                    WHEN :month = 1 THEN bm.jan
                    WHEN :month = 2 THEN bm.feb
                    WHEN :month = 3 THEN bm.mar
                    WHEN :month = 4 THEN bm.apr
                    WHEN :month = 5 THEN bm.mei
                    WHEN :month = 6 THEN bm.jun
                    WHEN :month = 7 THEN bm.jul
                    WHEN :month = 8 THEN bm.ags
                    WHEN :month = 9 THEN bm.sep
                    WHEN :month = 10 THEN bm.okt
                    WHEN :month = 11 THEN bm.nop
                    WHEN :month = 12 THEN bm.des
                    ELSE bm.totalfinal
                END AS budget,
                bm.totalfinal
            FROM budget_monitorings bm
            WHERE 
                (:month = 1 AND bm.jan IS NOT NULL) OR
                (:month = 2 AND bm.feb IS NOT NULL) OR
                (:month = 3 AND bm.mar IS NOT NULL) OR
                (:month = 4 AND bm.apr IS NOT NULL) OR
                (:month = 5 AND bm.mei IS NOT NULL) OR
                (:month = 6 AND bm.jun IS NOT NULL) OR
                (:month = 7 AND bm.jul IS NOT NULL) OR
                (:month = 8 AND bm.ags IS NOT NULL) OR
                (:month = 9 AND bm.sep IS NOT NULL) OR
                (:month = 10 AND bm.okt IS NOT NULL) OR
                (:month = 11 AND bm.nop IS NOT NULL) OR
                (:month = 12 AND bm.des IS NOT NULL) OR
                (:month = 0)
            ORDER BY bm.kodebeban;
        ";

        $data = DB::select($query, ['month' => $search]);

        return $data;
    }


    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);
        $data = distcode::whereRaw("LOWER(distcode) LIKE ?", ["%$search%"])
            ->orWhereRaw("LOWER(distname) LIKE ?", ["%$search%"])
            ->whereNull('deleted_by')
            ->select(
                'id',
                'distcode',
                'distname'
            )
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();
        return $data;
    }
}
