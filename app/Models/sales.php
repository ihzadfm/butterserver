<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class sales extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'sales';  // Correct table name from the migration
    protected $guarded = [];  // Guarded attributes are kept empty to allow mass assignment

    /**
     * Serialize date to a specific format.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);
        $data = sales::where(function ($query) use ($search) {
            $query->whereRaw("LOWER(\"brandcode\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"brandname\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"itemname\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"itemcode\") LIKE ?", ["%$search%"])
                // Untuk kolom yang berupa angka, gunakan langsung LIKE tanpa LOWER()
                ->orWhere("sales", "LIKE", "%$search%")
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
                'sales',
                'yop',
                'mop',
                'distcode',
                'distname',
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