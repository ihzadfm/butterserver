<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class masterbrand extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'masterbrand';  // Correct table name from the migration
    protected $guarded = [];  // Guarded attributes are kept empty to allow mass assignment

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function get_data_x($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $data = DB::connection('pgsql')->select("SELECT
    mbb.brandcode,
    mb.brandname,
    mbb.kodebeban
FROM
    m_bridging_budget AS mbb
LEFT JOIN
    masterbrand AS mb
ON
    mb.brandcode = mbb.brandcode
WHERE
    mbb.kodebeban IS NOT NULL;");
        return $data;
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);
        $data = masterbrand::whereRaw("LOWER(brandcode) LIKE ?", ["%$search%"])
            ->orWhereRaw("LOWER(brandname) LIKE ?", ["%$search%"])
            ->whereNull('deleted_by')
            ->select(
                'id',
                'brandcode',
                'brandname'
            )
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();
        return $data;
    }
}
