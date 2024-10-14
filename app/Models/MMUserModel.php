<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MMUserModel extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'm_users';
    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);
        $data = MMUserModel::whereRaw(" (lower(nama) like '%$search%'
        OR lower(nik) like '%$search%' OR lower(telp) like '%$search%' ) AND deleted_by
        is NULL")
            ->select('id', 'nama', 'nik', 'telp', 'alamat')
            ->offset($arr_pagination['offset'])->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')->get();
        return $data;
    }
}
