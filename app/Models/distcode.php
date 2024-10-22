<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class distcode extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'distcode';  // Correct table name from the migration
    protected $guarded = [];  // Guarded attributes are kept empty to allow mass assignment

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    public function get_data_x($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        // $search = strtolower($search);
        $data = DB::select("SELECT id, distcode, distname FROM distcode");

        // $data = "SELECT id, distcode, distname FROM distcode WHERE distcode like '%" . $search . "%' OR distname like '%" . $search . "%'";
        return $data;
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);
        $data = distcode::whereRaw("LOWER(distcode) LIKE ?", ["%$search%"])
            ->orWhereRaw("LOWER(distname) LIKE ?", ["%$search%"])
            ->whereNull('deleted_by')
            ->select(
                'id',
                'distcode',
                'distname'
            )
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();
        return $data;
    }
}
