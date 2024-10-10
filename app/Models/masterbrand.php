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

    /**
     * Serialize date to a specific format.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);
        $data = masterbrand::whereRaw(" \"brandcode\" like '%$search%' ")
            ->whereNull('deleted_by')
            ->select(
                'id',
                            'brandcode', 
                            'brandname', 
                        )
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();
            // ->toSql();
            // ->orderBy('id', 'ASC')->toSql();
        return $data;

    /**
     * Example method to fetch filtered data based on search.
     */
    // public function get_data_($search, $arr_pagination)
    // {
    //     if (!empty($search)) {
    //         $arr_pagination['offset'] = 0;
    //     }
    //     $search = strtolower($search);

    //     $data = masterbrand::whereRaw(" KodeBeban like '%$search%' 
    //     AND deleted_by IS NULL")
    //         ->select(
    //             'KodeBeban', 
    //             'KodeDivisi', 
    //             'Expense', 
    //             'ExpenseGroup', 
    //             'GroupBeban', 
    //             'GroupCostCenter', 
    //             'CostCenter', 
    //             'TOTALFINAL', 
    //             'TOTAL', 
    //             'JAN', 'FEB', 'MAR', 'APR', 'MEI', 
    //             'JUN', 'JUL', 'AGS', 'SEP', 'OKT', 
    //             'NOP', 'DES', 
    //             'RealizationN1', 'RealizationN2', 'RealizationN3', 'RealizationN4', 
    //             'RealizationN5', 'RealizationN6', 'RealizationN7', 'RealizationN8', 
    //             'RealizationN9', 'RealizationN10', 'RealizationN11', 'RealizationN12', 
    //             'TotalRealization', 
    //             'Balance', 
    //             'FA', 
    //             'Year', 
    //             'Status', 
    //             'Type', 
    //             'status_viewed', 
    //             'userid', 
    //             'ipaddress', 
    //             'modtime'
    //         )
    //         ->offset($arr_pagination['offset'])
    //         ->limit($arr_pagination['limit'])
    //         ->orderBy('id', 'ASC')
    //         ->get();
    //         // ->toSql();

    //     return $data;
    // }
            }
}
