<?php

namespace App\Models;

use DateTimeInterface;
use FTP\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;

class targetpenjualan extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'targetpenjualan';  // Correct table name from the migration
    protected $guarded = [];  // Guarded attributes are kept empty to allow mass assignment

    /**
     * Serialize date to a specific format.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function get_data_y($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;

        // Menambahkan fitur search berdasarkan keyword
        $searchQuery = "";
        if (!empty($search)) {
            $search = strtolower($search);
            $searchQuery = "AND (LOWER(mb.brandcode) LIKE '%$search%' OR LOWER(mb.brandname) LIKE '%$search%' OR LOWER(mbb.kodebeban) LIKE '%$search%')";
        }

        $data = DB::connection('pgsql')->select("SELECT 
        mbb.kodebeban, 
        mb.brandcode, 
        mb.brandname, 
        SUM(a.sales) AS sales, 
        SUM(a.target) AS target, 
        ROUND(CASE WHEN SUM(a.target) > 0 THEN (SUM(a.sales) / SUM(a.target)) * 100 ELSE 0 END, 2) AS achievement
    FROM
    (
        SELECT brandcode, SUM(sales) AS sales, 0 AS target
        FROM sales
        GROUP BY brandcode

        UNION ALL

        SELECT brandcode, 0 AS sales, SUM(target) AS target
        FROM targetpenjualan
        GROUP BY brandcode
    ) AS a
    LEFT JOIN m_bridging_budget AS mbb ON mbb.brandcode = a.brandcode
    LEFT JOIN masterbrand AS mb ON mb.brandcode = a.brandcode
    WHERE mbb.kodebeban IS NOT NULL
    $searchQuery
    GROUP BY mbb.kodebeban, mb.brandcode, mb.brandname
    LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']};");

        return $data;
    }

    public function get_data_x($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;

        // Menambahkan fitur search berdasarkan keyword
        $searchQuery = "";
        if (!empty($search)) {
            $search = strtolower($search);
            $searchQuery = "AND (LOWER(a.brandcode) LIKE '%$search%' OR LOWER(a.distcode) LIKE '%$search%')";
        }

        $data = DB::connection('pgsql')->select("SELECT 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.brandcode, 
        SUM(a.sales) as sales, 
        SUM(a.target) as target, 
        ROUND(CAST((SUM(a.sales)/SUM(a.target)) * 100 AS NUMERIC), 2) as achievement
    FROM
    (
        SELECT yop, mop, distcode, brandcode, SUM(sales) as sales, 0 as target
        FROM sales
        GROUP BY yop, mop, distcode, brandcode

        UNION ALL

        SELECT yop, mop, distcode, brandcode, 0 as sales, SUM(target) as target
        FROM targetpenjualan
        GROUP BY yop, mop, distcode, brandcode
    ) AS a
    WHERE 1=1
    $searchQuery
    GROUP BY a.yop, a.mop, a.distcode, a.brandcode
    LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']};");

        return $data;
    }


    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);
        $data = targetpenjualan::where(function ($query) use ($search) {
            $query->whereRaw("LOWER(\"brandcode\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"itemname\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"itemcode\") LIKE ?", ["%$search%"])
                ->orWhere("target", "LIKE", "%$search%")
                ->orWhere("yop", "LIKE", "%$search%")
                ->orWhere("mop", "LIKE", "%$search%")
                ->orWhere("distcode", "LIKE", "%$search%");
        })
            ->select(
                'id',
                'brandcode',
                'itemname',
                'itemcode',
                'distcode',
                'target',
                'yop',
                'mop',
            )
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();
        // ->toSql();
        // ->orderBy('id', 'ASC')->toSql();
        return $data;
    }
}
