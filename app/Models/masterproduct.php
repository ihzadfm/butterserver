<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class masterproduct extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'masterproduct';  // Correct table name from the migration
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

        $data = masterproduct::where(function ($query) use ($search) {
            $query->whereRaw("LOWER(\"brandcode\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"brandname\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"itemcode\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"itemname\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"mtgcode\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"parentcode\") LIKE ?", ["%$search%"])
                // Untuk kolom angka, gunakan langsung LIKE tanpa LOWER()
                ->orWhere("id", "LIKE", "%$search%");
        })
        ->whereNull('deleted_by')
        ->select(
            'id',
            'brandcode',
            'brandname',
            'itemcode',
            'mtgcode',
            'parentcode',
            'itemname'
        )
        ->offset($arr_pagination['offset'])
        ->limit($arr_pagination['limit'])
        ->orderBy('id', 'ASC')
        ->get();

        return $data;
    }
}
