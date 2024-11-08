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
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        // Query dasar
        $query = "SELECT
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
                    mbb.kodebeban IS NOT NULL";

        // Menambahkan kondisi pencarian jika keyword tersedia
        if (!empty($search)) {
            // Menambahkan keyword untuk mencari di kolom-kolom brandcode, brandname, atau kodebeban
            $search = strtolower($search); // Ubah menjadi huruf kecil untuk pencarian tidak peka huruf besar-kecil
            $query .= " AND (LOWER(mbb.brandcode) LIKE '%$search%'
                        OR LOWER(mb.brandname) LIKE '%$search%'
                        OR LOWER(mbb.kodebeban) LIKE '%$search%')";
        }

        // Menjalankan query
        $data = DB::connection('pgsql')->select($query);

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
