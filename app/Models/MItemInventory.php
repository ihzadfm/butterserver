<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MItemInventory extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'm_item_inventory';
    protected $guarded = [];

    /**
     * Format tanggal yang digunakan saat serialisasi data tanggal.
     *
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Mendapatkan data berdasarkan pencarian dan paginasi.
     *
     * @param string $search
     * @param array $arr_pagination
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) {
            $arr_pagination['offset'] = 0; // Reset offset jika ada pencarian
        }

        $search = strtolower($search);

        // Query dengan pencarian dan paginasi
        $data = MItemInventory::whereRaw("
            (lower(item_code) like '%$search%' 
            OR lower(whs_code) like '%$search%'  )
            AND deleted_by IS NULL
        ")
            ->select('id', 'item_code', 'whs_code', 'on_hand', 'on_order', 'min_stock', 'max_stock', 'min_order', 'reorder_qty', 'on_priority', 'flag_active')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }
}
