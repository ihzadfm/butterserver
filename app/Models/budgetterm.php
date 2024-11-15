<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BudgetTerm extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'budgetterm';  // Table yang sesuai dengan migration budgetterm
    protected $guarded = [];  // Guarded attributes kept empty for mass assignment

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
        $data = DB::connection('pgsql')->select("INSERT INTO budgetterm (
    kodebeban, 
    q1, q2, q3, q4, 
    realizationq1, realizationq2, realizationq3, realizationq4, 
    created_at, updated_at
)
SELECT 
    kodebeban, 
    -- Agregasi bulanan menjadi kuartal dengan CAST ke numeric
    (CAST(NULLIF(jan, '') AS numeric) + CAST(NULLIF(feb, '') AS numeric) + CAST(NULLIF(mar, '') AS numeric)) AS q1,   -- Q1: Jan + Feb + Mar
    (CAST(NULLIF(apr, '') AS numeric) + CAST(NULLIF(mei, '') AS numeric) + CAST(NULLIF(jun, '') AS numeric)) AS q2,   -- Q2: Apr + Mei + Jun
    (CAST(NULLIF(jul, '') AS numeric) + CAST(NULLIF(ags, '') AS numeric) + CAST(NULLIF(sep, '') AS numeric)) AS q3,   -- Q3: Jul + Ags + Sep
    (CAST(NULLIF(okt, '') AS numeric) + CAST(NULLIF(nop, '') AS numeric) + CAST(NULLIF(des, '') AS numeric)) AS q4,   -- Q4: Okt + Nop + Des
    (CAST(NULLIF(realizationn1, '') AS numeric) + CAST(NULLIF(realizationn2, '') AS numeric) + CAST(NULLIF(realizationn3, '') AS numeric)) AS realizationq1,  -- Realisasi Q1
    (CAST(NULLIF(realizationn4, '') AS numeric) + CAST(NULLIF(realizationn5, '') AS numeric) + CAST(NULLIF(realizationn6, '') AS numeric)) AS realizationq2,  -- Realisasi Q2
    (CAST(NULLIF(realizationn7, '') AS numeric) + CAST(NULLIF(realizationn8, '') AS numeric) + CAST(NULLIF(realizationn9, '') AS numeric)) AS realizationq3,  -- Realisasi Q3
    (CAST(NULLIF(realizationn10, '') AS numeric) + CAST(NULLIF(realizationn11, '') AS numeric) + CAST(NULLIF(realizationn12, '') AS numeric)) AS realizationq4,  -- Realisasi Q4
    NOW() AS created_at,   -- Tanggal saat data diinput
    NOW() AS updated_at    -- Tanggal saat data diupdate
FROM 
    budget_monitorings
WHERE 
    deleted_by IS NULL;");
        return $data;
    }

    public function get_data_accrued($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;

        $data = DB::connection('pgsql')->select("
            SELECT 
                bt.kodebeban,
                bt.q1,
                bt.realizationq1,
                COALESCE(SUM(a.nilai_realisasi) FILTER (WHERE a.bulan IN (1, 2, 3)), 0) AS accrued_q1, -- Accrued Q1
                (bt.q1-bt.realizationq1) outstandingbudgetrealisasiq1,
                (bt.q1-(COALESCE(SUM(a.nilai_realisasi) FILTER (WHERE a.bulan IN (1, 2, 3)), 0))) outstandingbudgetaccruedq1,
                bt.q2,
                bt.realizationq2,
                COALESCE(SUM(a.nilai_realisasi) FILTER (WHERE a.bulan IN (4, 5, 6)), 0) AS accrued_q2, -- Accrued Q2
                (bt.q2-bt.realizationq2) outstandingbudgetrealisasiq2,
                (bt.q2-(COALESCE(SUM(a.nilai_realisasi) FILTER (WHERE a.bulan IN (4, 5, 6)), 0))) outstandingbudgetaccruedq2,
                bt.q3,
                bt.realizationq3,
                COALESCE(SUM(a.nilai_realisasi) FILTER (WHERE a.bulan IN (7, 8, 9)), 0) AS accrued_q3, -- Accrued Q3
                (bt.q3-bt.realizationq3) outstandingbudgetrealisasiq3,
                (bt.q3-(COALESCE(SUM(a.nilai_realisasi) FILTER (WHERE a.bulan IN (7, 8, 9)), 0))) outstandingbudgetaccruedq3,
                bt.q4,
                bt.realizationq4,
                COALESCE(SUM(a.nilai_realisasi) FILTER (WHERE a.bulan IN (10, 11, 12)), 0) AS accrued_q4, -- Accrued Q4
                (bt.q4-bt.realizationq4) outstandingbudgetrealisasiq4,
                (bt.q4-(COALESCE(SUM(a.nilai_realisasi) FILTER (WHERE a.bulan IN (10, 11, 12)), 0))) outstandingbudgetaccruedq4,
                bt.created_at,
                bt.updated_at
            FROM 
                budgetterm bt
            LEFT JOIN 
                accrued a ON bt.kodebeban = a.kodebeban
            GROUP BY 
                bt.kodebeban, bt.q1, bt.realizationq1, bt.q2, bt.realizationq2, bt.q3, bt.realizationq3, bt.q4, bt.realizationq4, bt.created_at, bt.updated_at
            ORDER BY 
                bt.kodebeban
            OFFSET ? LIMIT ?
        ", [$arr_pagination['offset'], $arr_pagination['limit']]);

        return $data;
    }


    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);

        $data = BudgetTerm::where(function ($query) use ($search) {
            $query->whereRaw("LOWER(\"kodebeban\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"q1\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"q2\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"q3\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"q4\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"realizationq1\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"realizationq2\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"realizationq3\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"realizationq4\") LIKE ?", ["%$search%"]);
        })
            ->whereNull('deleted_by')
            ->select(
                'id',
                'kodebeban',
                'q1',
                'q2',
                'q3',
                'q4',
                'realizationq1',
                'realizationq2',
                'realizationq3',
                'realizationq4',
                'created_at',
                'updated_at'
            )
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }
}
