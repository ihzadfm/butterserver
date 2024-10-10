<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MItem extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'm_item'; // Nama tabel sesuai dengan migration
    protected $guarded = []; // Tidak ada kolom yang dilarang untuk mass assignment

    /**
     * Menentukan format tanggal yang digunakan untuk kolom created_at, updated_at, dan deleted_at.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Mendapatkan data dengan pencarian dan paginasi
     * @param string $search
     * @param array $arr_pagination
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get_data_($search, $arr_pagination)
    {
        // Jika ada pencarian, reset offset pagination ke 0
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        $search = strtolower($search);

        // Query dengan pencarian dan paginasi
        $data = MItem::whereRaw("
            (lower(item_name) like '%$search%'
            OR lower(item_code) like '%$search%'
            OR lower(mnft_code) like '%$search%'
            OR lower(code_bars) like '%$search%' ) 
            AND deleted_by IS NULL
        ")
            ->select('id', 'item_code', 'item_name', 'code_bars', 'mnft_code', 'sales_item', 'purch_item', 'return_item', 'uom1', 'uom2', 'uom3', 'uom4', 'obj_type', 'flag_active')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }
}
