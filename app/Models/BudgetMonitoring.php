<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BudgetMonitoring extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'budget_monitorings';  // Correct table name from the migration
    protected $guarded = [];  // Guarded attributes are kept empty to allow mass assignment

    /**
     * Serialize date to a specific format.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public static function adjusmentaccrued($kodebeban, $ach)
    {
        $data = self::where('kodebeban', $kodebeban)->first();
        $accruedpp = accrued::where('kodebeban', $kodebeban)->first();

        $q1 = (int) $data->jan + (int) $data->feb + (int) $data->mar;
        $q2 = (int) $data->apr + (int) $data->mei + (int) $data->jun;
        $q3 = (int) $data->jul + (int) $data->ags + (int) $data->sep;
        $q4 = (int) $data->okt + (int) $data->nov + (int) $data->des;


        $hq1 = ((float) $ach / 100) * $q1;
        $hq2 = ((float) $ach / 100) * $q2;
        $hq3 = ((float) $ach / 100) * $q3;
        $hq4 = ((float) $ach / 100) * $q4;

        $updates = [];

        // Perkondisian Q2
        if ((int) $accruedpp->nilai_realisasi > $q1) {
            $selisihrealisasi1 = ($hq1 - $q1);
            $updates['apr'] = $q2 + $selisihrealisasi1;
            $updates['mei'] = '0';
            $updates['jun'] = '0';
        }

        // Perkondisian Q3
        if ((int) $accruedpp->nilai_realisasi > $q2) {
            $selisihrealisasi2 = ($hq2 - $q2);
            $updates['jul'] = $q3 + $selisihrealisasi2;
            $updates['ags'] = '0';
            $updates['sep'] = '0';
        }

        // Perkondisian Q4
        if ((int) $accruedpp->nilai_realisasi > $q3) {
            $selisihrealisasi3 = ($hq3 - $q3);
            $updates['okt'] = $q4 + $selisihrealisasi3;
            $updates['nop'] = '0';
            $updates['des'] = '0';
        }

        if (!empty($updates)) {
            $data->update($updates);
        }

        return [
            'a' => (int) $accruedpp->nilai_realisasi > $q1,
            'b' => (int) $accruedpp->nilai_realisasi > $q2,
            'c' => (int) $accruedpp->nilai_realisasi > $q3,
            'd' => (int) $accruedpp->nilai_realisasi,
            'e' => $q1,
            'f' => $q2,
            'g' => $q3,
            'h' => $accruedpp->nilai_realisasi,
            'i' =>  (int) $q2 + ((int) $hq1 -  (int) $q1),
            'j' => $updates,
            'k' => $hq1,
            'L' => $ach,
        ];
    }

    public static function adjusmentrealisasi($kodebeban, $ach)
    {
        $data = self::where('kodebeban', $kodebeban)->first();
        $accruedpp = accrued::where('kodebeban', $kodebeban)->first();

        $q1 = (int) $data->jan + (int) $data->feb + (int) $data->mar;
        $q2 = (int) $data->apr + (int) $data->mei + (int) $data->jun;
        $q3 = (int) $data->jul + (int) $data->ags + (int) $data->sep;
        $q4 = (int) $data->okt + (int) $data->nov + (int) $data->des;

        $rq1 = (float) $data->realizationn1 + (float) $data->realizationn2 + (float) $data->realizationn3;
        $rq2 = (float) $data->realizationn4 + (float) $data->realizationn5 + (float) $data->realizationn6;
        $rq3 = (float) $data->realizationn7 + (float) $data->realizationn8 + (float) $data->realizationn9;
        $rq4 = (float) $data->realizationn10 + (float) $data->realizationn11 + (float) $data->realizationn12;


        $hq1 = ((float) $ach / 100) * $q1;
        $hq2 = ((float) $ach / 100) * $q2;
        $hq3 = ((float) $ach / 100) * $q3;
        $hq4 = ((float) $ach / 100) * $q4;

        $updates = [];

        // Perkondisian Q2
        if ((int) $rq1 > $q1) {
            $selisihrealisasi1 = ($hq1 - $q1);
            $updates['apr'] = $q2 + $selisihrealisasi1;
            $updates['mei'] = '0';
            $updates['jun'] = '0';
        }

        // Perkondisian Q3
        if ((int) $rq2 > $q2) {
            $selisihrealisasi2 = ($hq2 - $q2);
            $updates['jul'] = $q3 + $selisihrealisasi2;
            $updates['ags'] = '0';
            $updates['sep'] = '0';
        }

        // Perkondisian Q4
        if ((int) $rq3 > $q3) {
            $selisihrealisasi3 = ($hq3 - $q3);
            $updates['okt'] = $q4 + $selisihrealisasi3;
            $updates['nop'] = '0';
            $updates['des'] = '0';
        }

        if (!empty($updates)) {
            $data->update($updates);
        }

        $BudgetMonitoring = BudgetMonitoring::where('kodebeban', $kodebeban)->first();
        return [
            'a' => (int) $rq1 > $q1,
            'b' => (int) $rq2 > $q2,
            'c' => (int) $rq3 > $q3,
            'd' => (int) $rq1,
            'e' => $q1,
            'f' => $q2,
            'g' => $q3,
            'h' => $q4,
            'i' =>  (int) $q2 + ((int) $hq1 -  (int) $q1),
            'L' => $ach,
            'rq2' => $rq2,
            'rq3' => $rq3,
            'rq4' => $rq4,
            'hasil' => $BudgetMonitoring
        ];
    }


    public function get_data_x($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        DB::connection('pgsql')->statement("TRUNCATE budgetterm RESTART IDENTITY CASCADE;");

        // Lanjutkan dengan perintah INSERT
        $insert = DB::connection('pgsql')->select("INSERT INTO budgetterm (
        kodebeban, 
        q1, q2, q3, q4, 
        realizationq1, realizationq2, realizationq3, realizationq4, 
        created_at, updated_at
    )
    SELECT 
        bm.kodebeban, 
        -- Agregasi bulanan menjadi kuartal dengan CAST ke numeric
        COALESCE(CAST(NULLIF(bm.jan, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.feb, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.mar, '') AS numeric), 0) AS q1,   -- Q1: Jan + Feb + Mar
COALESCE(CAST(NULLIF(bm.apr, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.mei, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.jun, '') AS numeric), 0) AS q2,   -- Q2: Apr + Mei + Jun
 COALESCE(CAST(NULLIF(bm.jul, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.ags, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.sep, '') AS numeric), 0) AS q3,   -- Q3: Jul + Ags + Sep
 COALESCE(CAST(NULLIF(bm.okt, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.nop, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.des, '') AS numeric), 0) AS q4,   -- Q4: Okt + Nop + Des
        COALESCE(CAST(NULLIF(bm.realizationn1, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn2, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn3, '') AS numeric), 0) AS realizationq1,  -- Realisasi Q1
 COALESCE(CAST(NULLIF(bm.realizationn4, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn5, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn6, '') AS numeric), 0) AS realizationq2,  -- Realisasi Q2
COALESCE(CAST(NULLIF(bm.realizationn7, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn8, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn9, '') AS numeric), 0) AS realizationq3,  -- Realisasi Q3
COALESCE(CAST(NULLIF(bm.realizationn10, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn11, '') AS numeric), 0) + 
        COALESCE(CAST(NULLIF(bm.realizationn12, '') AS numeric), 0) AS realizationq4,  -- Realisasi Q4
NOW() AS created_at,   -- Tanggal saat data diinput
        NOW() AS updated_at    -- Tanggal saat data diupdate
    FROM 
        budget_monitorings bm
    WHERE 
        bm.deleted_by IS NULL;");

        $data = DB::table('budgetterm')->get();
        return $data;
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);
        $data = BudgetMonitoring::whereRaw("LOWER(\"kodebeban\") like '%$search%' ")
            ->orWhereRaw("LOWER(\"kodedivisi\") like '%$search%' ")
            ->orWhereRaw("LOWER(\"expense\") like '%$search%' ")
            ->orWhereRaw("LOWER(\"expensegroup\") like '%$search%' ")
            ->orWhereRaw("LOWER(\"groupbeban\") like '%$search%' ")
            ->orWhereRaw("LOWER(\"groupcostcenter\") like '%$search%' ")
            ->orWhereRaw("LOWER(\"costcenter\") like '%$search%' ")
            ->orWhereRaw("CAST(\"totalfinal\" AS TEXT) like '%$search%' ")  // Menggunakan CAST untuk angka
            ->orWhereRaw("CAST(\"total\" AS TEXT) like '%$search%' ")  // Menggunakan CAST untuk angka
            ->orWhereRaw("CAST(\"jan\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"feb\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"mar\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"apr\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"mei\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"jun\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"jul\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"ags\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"sep\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"okt\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"nop\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"des\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"realizationn1\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"realizationn2\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"realizationn3\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"realizationn4\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"realizationn5\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"realizationn6\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"realizationn7\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"realizationn8\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"realizationn9\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"realizationn10\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"realizationn11\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"realizationn12\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"totalrealization\" AS TEXT) like '%$search%' ")
            ->orWhereRaw("CAST(\"year\" AS TEXT) like '%$search%' ")  // Tahun juga dicari
            ->whereNull('deleted_by')
            ->select(
                'id',
                'kodebeban',
                'kodedivisi',
                'expense',
                'expensegroup',
                'groupbeban',
                'groupcostcenter',
                'costcenter',
                'totalfinal',
                'total',
                'jan',
                'feb',
                'mar',
                'apr',
                'mei',
                'jun',
                'jul',
                'ags',
                'sep',
                'okt',
                'nop',
                'des',
                'realizationn1',
                'realizationn2',
                'realizationn3',
                'realizationn4',
                'realizationn5',
                'realizationn6',
                'realizationn7',
                'realizationn8',
                'realizationn9',
                'realizationn10',
                'realizationn11',
                'realizationn12',
                'totalrealization',
                'year'
            )
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();
        return $data;
    }
}
