<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MKaryawan extends Model
{
    use SoftDeletes;
    protected $table = 'm_karyawans';

    public function department() {
        return $this->belongsTo(MDepartment::class, 'kode_department', 'kode_department');
    }
}
