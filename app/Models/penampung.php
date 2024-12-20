<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Penampung extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'penampung';  // Table name based on the migration
    protected $guarded = [];  // Guarded attributes are kept empty to allow mass assignment

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);

        $data = penampung::whereRaw("LOWER(kodebeban) LIKE ?", ["%$search%"])
            ->whereNull('deleted_by')
            ->select(
                'id',
                'kodebeban',
                'term',
                'realizationterm',

            )
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();
        
        return $data;
    }
}
