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
