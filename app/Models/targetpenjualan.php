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

    //     public function get_data_x($search, $arr_pagination)
    //     {
    //         if (!empty($search)) $arr_pagination['offset'] = 0;

    //         // Menambahkan fitur search berdasarkan keyword
    //         $searchQuery = "";
    //         if (!empty($search)) {
    //             $search = strtolower($search);
    //             $searchQuery = "AND (LOWER(a.brandcode) LIKE '%$search%' OR LOWER(a.distcode) LIKE '%$search%')";
    //         }

    //         $data = DB::connection('pgsql')->select("SELECT 
    //     a.yop, 
    //     a.mop, 
    //     a.distcode, 
    //     a.brandcode, 
    //     a.brandname,   -- Menambahkan brandname dari sales
    //     SUM(a.sales) as sales, 
    //     SUM(a.target) as target, 
    //     ROUND((SUM(a.sales)/SUM(a.target)) * 100, 2) as achievement
    // FROM
    // (
    //     SELECT yop, mop, distcode, brandcode, brandname, SUM(sales) as sales, 0 as target
    //     FROM sales
    //     GROUP BY yop, mop, distcode, brandcode, brandname

    //     UNION ALL

    //     SELECT yop, mop, distcode, brandcode, brandname, 0 as sales, SUM(target) as target
    //     FROM targetpenjualan
    //     GROUP BY yop, mop, distcode, brandcode, brandname
    // ) AS a
    // GROUP BY a.yop, a.mop, a.distcode, a.brandcode, a.brandname");
    //         return $data;
    //     }

    public function get_data_x($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;

        // Menambahkan fitur search berdasarkan keyword
        $searchQuery = "";
        if (!empty($search)) {
            $search = strtolower($search);
            // Pencarian pada kolom non-agregat (brandcode, distcode, brandname)
            $searchQuery = "AND (LOWER(a.brandcode) LIKE '%$search%' 
                          OR LOWER(a.distcode) LIKE '%$search%' 
                          OR LOWER(a.distname) LIKE '%$search%' 
                          OR LOWER(a.brandname) LIKE '%$search%')";
        }

        // Query yang diperbaiki
        $data = DB::connection('pgsql')->select("SELECT 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname,
        a.brandcode, 
        a.brandname,
        SUM(a.sales) as sales, 
        SUM(a.target) as target, 
        CASE 
        WHEN SUM(a.target) > 0 THEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
        ELSE 0
    END as achievement
FROM 
    (   SELECT yop, mop, distcode, distname, brandcode, brandname, SUM(sales) as sales, 0 as target
        FROM sales
        GROUP BY yop, mop, distcode, distname, brandcode, brandname
        UNION ALL
        SELECT yop, mop, distcode, distname, brandcode, brandname, 0 as sales, SUM(target) as target
        FROM targetpenjualan
        GROUP BY yop, mop, distcode, distname, brandcode, brandname
    ) AS a
    WHERE 1=1
    $searchQuery  -- Menambahkan query pencarian
    GROUP BY a.yop, a.mop, a.distcode, a.distname, a.brandcode, a.brandname
    LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']}");

        return $data;
    }



    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);
        $data = targetpenjualan::where(function ($query) use ($search) {
            $query->whereRaw("LOWER(\"brandcode\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"brandname\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"itemname\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"itemcode\") LIKE ?", ["%$search%"])
                ->orWhere("target", "LIKE", "%$search%")
                ->orWhere("yop", "LIKE", "%$search%")
                ->orWhere("mop", "LIKE", "%$search%")
                ->orWhere("distcode", "LIKE", "%$search%")
                ->orWhere("distname", "LIKE", "%$search%");
        })
            ->select(
                'id',
                'brandcode',
                'brandname',
                'itemname',
                'itemcode',
                'distcode',
                'distname',
                'target',
                'yop',
                'mop',
            )
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC');
        // ->toSql();
        // ->orderBy('id', 'ASC')->toSql();
        return $data;
    }
    public function getsearch($distcode, $brandcode, $yop, $mop, $arr_pagination)
    { 
        if ($distcode == 'ALL') {
            $distcode = '';
        }
        if ($brandcode == 'ALL') {
            $brandcode = '';
        }
        if ($yop == 'ALL') {
            $yop = '';
        }
        if ($mop == 'ALL') {
            $mop = '';
        }

        if (!empty($search)) $arr_pagination['offset'] = 0;
        $data = DB::select("SELECT 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname,
        a.brandcode, 
        a.brandname,
        SUM(a.sales) as sales, 
        SUM(a.target) as target, 
        CASE 
        WHEN SUM(a.target) > 0 THEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
        ELSE 0
    END as achievement
FROM 
    (   SELECT yop, mop, distcode, distname, brandcode, brandname, SUM(sales) as sales, 0 as target
        FROM sales
        GROUP BY yop, mop, distcode, distname, brandcode, brandname
        UNION ALL
        SELECT yop, mop, distcode, distname, brandcode, brandname, 0 as sales, SUM(target) as target
        FROM targetpenjualan
        GROUP BY yop, mop, distcode, distname, brandcode, brandname
    ) AS a
    WHERE a.brandcode LIKE '%$brandcode%' AND a.distcode LIKE '%$distcode%' AND 
    CAST(a.yop AS TEXT) LIKE '%$yop%' 
    AND CAST(a.mop AS TEXT) LIKE '%$mop%'
    GROUP BY a.yop, a.mop, a.distcode, a.distname, a.brandcode, a.brandname");
    
        return $data;
    }
}
