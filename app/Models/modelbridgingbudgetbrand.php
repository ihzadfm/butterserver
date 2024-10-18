<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class modelbridgingbudgetbrand extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'm_bridging_budget';  // Correct table name from the migration
    protected $guarded = [];  // Guarded attributes are kept empty to allow mass assignment

    /**
     * Serialize date to a specific format.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        // Query dasar
        $query = modelbridgingbudgetbrand::whereNull('deleted_by')
            ->select(
                'id',
                'brandcode',
                'kodebeban',
                'itemcode',
                'mtgcode',
                'parentcode',
                'itemname'
            );

        // Jika ada kata kunci pencarian, tambahkan filter whereRaw untuk semua kolom yang relevan
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER("brandcode") LIKE ?', ["%$search%"])
                  ->orWhereRaw('LOWER("kodebeban") LIKE ?', ["%$search%"])
                  ->orWhereRaw('LOWER("itemcode") LIKE ?', ["%$search%"])
                  ->orWhereRaw('LOWER("mtgcode") LIKE ?', ["%$search%"])
                  ->orWhereRaw('LOWER("parentcode") LIKE ?', ["%$search%"])
                  ->orWhereRaw('LOWER("itemname") LIKE ?', ["%$search%"]);
            });
        }

        // Mengatur pagination dan sorting
        $data = $query->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }
}
