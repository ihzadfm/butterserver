<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class accrued extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'accrued';  // Correct table name from the migration
    protected $guarded = [];  // Guarded attributes are kept empty to allow mass assignment

    /**
     * Serialize date to a specific format.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getData($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);

        $data = accrued::where(function ($query) use ($search) {
            $query->whereRaw("LOWER(\"no_pp\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"kodebeban\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"jenis_realisasi\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"divisi\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"nama_pp\") LIKE ?", ["%$search%"])
                ->orWhere("id_detail", "LIKE", "%$search%")
                ->orWhere("bulan", "LIKE", "%$search%")
                ->orWhere("tahun", "LIKE", "%$search%")
                ->orWhere("nilai_pp", "LIKE", "%$search%")
                ->orWhere("nilai_realisasi", "LIKE", "%$search%")
                ->orWhere("status_pp", "LIKE", "%$search%")
                ->orWhere("jenis_accrued", "LIKE", "%$search%")
                ->orWhere("status_approved", "LIKE", "%$search%")
                ->orWhere("status_closed", "LIKE", "%$search%");
        })
        ->whereNull('deleted_by')
        ->select(
            'id',
            'no_pp',
            'id_detail',
            'kodebeban',
            'nilai_pp',
            'bulan',
            'tahun',
            'jenis_realisasi',
            'no_realisasi',
            'tgl_realisasi',
            'nilai_realisasi',
            'status_pp',
            'divisi',
            'nama_pp',
            'jenis_accrued',
            'status_approved',
            'status_closed',
            'tgl_input'
        )
        ->offset($arr_pagination['offset'])
        ->limit($arr_pagination['limit'])
        ->orderBy('id', 'ASC')
        ->get();

        return $data;
    }
}
