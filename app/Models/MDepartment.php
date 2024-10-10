<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MKaryawan;

class MDepartment extends Model
{
    use SoftDeletes;
    protected $table = 'm_departments';

    public function karyawans() {
        return $this->hasMany(MKaryawan::class, 'kode_department', 'kode_department');
    }
}
