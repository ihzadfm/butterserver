<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MUser extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'm_users';
    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getData()
    {
        $data = DB::select('SELECT * FROM ' . $this->table . ' WHERE deleted_by IS NULL');

        // $data = MUser::orderBy('id', 'ASC')->get();
        return $data;
    }
}



