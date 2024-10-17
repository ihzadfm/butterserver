<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BudgetMonitoring extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'budget_monitorings';  // Correct table name from the migration
    protected $guarded = [];  // Guarded attributes are kept empty to allow mass assignment

    /**
     * Serialize date to a specific format.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function get_data_x($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        DB::connection('pgsql')->statement("TRUNCATE budgetterm RESTART IDENTITY CASCADE;");

        // Lanjutkan dengan perintah INSERT
        $insert = DB::connection('pgsql')->select("INSERT INTO budgetterm (
        kodebeban, 
        q1, q2, q3, q4, 
        realizationq1, realizationq2, realizationq3, realizationq4, 
        created_at, updated_at
    )
    SELECT 
        bm.kodebeban, 
        -- Agregasi bulanan menjadi kuartal dengan CAST ke numeric
        COALESCE(CAST(NULLIF(bm.jan, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.feb, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.mar, '') AS numeric), 0) AS q1,   -- Q1: Jan + Feb + Mar
COALESCE(CAST(NULLIF(bm.apr, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.mei, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.jun, '') AS numeric), 0) AS q2,   -- Q2: Apr + Mei + Jun
 COALESCE(CAST(NULLIF(bm.jul, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.ags, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.sep, '') AS numeric), 0) AS q3,   -- Q3: Jul + Ags + Sep
 COALESCE(CAST(NULLIF(bm.okt, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.nop, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.des, '') AS numeric), 0) AS q4,   -- Q4: Okt + Nop + Des
        COALESCE(CAST(NULLIF(bm.realizationn1, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn2, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn3, '') AS numeric), 0) AS realizationq1,  -- Realisasi Q1
 COALESCE(CAST(NULLIF(bm.realizationn4, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn5, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn6, '') AS numeric), 0) AS realizationq2,  -- Realisasi Q2
COALESCE(CAST(NULLIF(bm.realizationn7, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn8, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn9, '') AS numeric), 0) AS realizationq3,  -- Realisasi Q3
COALESCE(CAST(NULLIF(bm.realizationn10, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn11, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn12, '') AS numeric), 0) AS realizationq4,  -- Realisasi Q4
NOW() AS created_at,   -- Tanggal saat data diinput
        NOW() AS updated_at    -- Tanggal saat data diupdate
    FROM 
        budget_monitorings bm
    WHERE 
        bm.deleted_by IS NULL;");

        $data = DB::table('budgetterm')->get();
        return $data;
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);
        $data = BudgetMonitoring::whereRaw(" \"kodebeban\" like '%$search%' ")
            ->whereNull('deleted_by')
            ->select(
                'id',
                'kodebeban',
                'kodedivisi',
                'expense',
                'expensegroup',
                'groupbeban',
                'groupcostcenter',
                'costcenter',
                'totalfinal',
                'total',
                'jan',
                'feb',
                'mar',
                'apr',
                'mei',
                'jun',
                'jul',
                'ags',
                'sep',
                'okt',
                'nop',
                'des',
                'realizationn1',
                'realizationn2',
                'realizationn3',
                'realizationn4',
                'realizationn5',
                'realizationn6',
                'realizationn7',
                'realizationn8',
                'realizationn9',
                'realizationn10',
                'realizationn11',
                'realizationn12',
                'totalrealization',
                'year'
            )
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();
        return $data;
    }
}
