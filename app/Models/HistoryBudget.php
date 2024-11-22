<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryBudget extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'history_budget';  // Tabel sesuai dengan migration
    protected $guarded = [];  // Untuk memungkinkan mass assignment

    /**
     * Serialize date ke format tertentu.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Ambil data dengan filter pencarian dan paginasi.
     */
    public function getData($search, $arr_pagination)
    {
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        $search = strtolower($search);

        $data = self::where(function ($query) use ($search) {
            $query->whereRaw("LOWER(kodehistory) LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(kodebeban1) LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(kodebeban2) LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(bulan1) LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(bulan2) LIKE ?", ["%$search%"])
                ->orWhere("amount", "LIKE", "%$search%")
                ->orWhere("amountbulan1", "LIKE", "%$search%")
                ->orWhere("amountbulan2", "LIKE", "%$search%");
        })
        ->select(
            'id',
            'kodehistory',
            'kodebeban1',
            'kodebeban2',
            'bulan1',
            'bulan2',
            'amount',
            'amountbulan1',
            'amountbulan2'
        )
        ->offset($arr_pagination['offset'])
        ->limit($arr_pagination['limit'])
        ->orderBy('id', 'ASC')
        ->get();

        return $data;
    }
}
