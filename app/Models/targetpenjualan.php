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

    public function get_data_x($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $data = DB::connection('pgsql')->select("SELECT 
    a.yop, 
    a.mop, 
    a.distcode, 
    a.brandcode, 
    SUM(a.sales) as sales, 
    SUM(a.target) as target, 
    ROUND((SUM(a.sales)/SUM(a.target)) * 100, 2) as achievement
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
GROUP BY a.yop, a.mop, a.distcode, a.brandcode;");
        // ->offset($arr_pagination['offset'])
        // ->limit($arr_pagination['limit'])
        // ->orderBy('id', 'ASC')
        // ->get();
        // ->toSql();
        // ->orderBy('id', 'ASC')->toSql();
        return $data;
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);
        $data = targetpenjualan::whereRaw(" \"brandcode\" like '%$search%' ")
            ->whereNull('deleted_by')
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
