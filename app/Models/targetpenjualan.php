<?php

namespace App\Models;

use DateTimeInterface;
use FTP\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Static_;

class targetpenjualan extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'targetpenjualan';  // Correct table name from the migration
    protected $guarded = [];  // Guarded attributes are kept empty to allow mass assignment
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function get_data_param($distcode, $brandcode, $yop, $mop, $arr_pagination)
    {

        if ($distcode == 'ALL') {
            $distcode = '';
        }

        if ($brandcode == 'ALL') {
            $brandcode = '';
        }

        if ($yop == 'ALL') {
            $yop = '';
        }

        if ($mop == 'ALL') {
            $mop = '';
        }

        $data = DB::select("SELECT 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname,
        a.brandcode, 
        a.brandname, 
        -- Menambahkan brandname dari sales
        SUM(a.sales) as sales, 
        SUM(a.target) as target, 
        ROUND(
            (
            SUM(a.sales)/ SUM(a.target)
            ) * 100, 
            2
        ) as achievement 
        FROM 
        (
            SELECT 
            yop, 
            mop, 
            distcode, 
            distname, 
            brandcode, 
            brandname, 
            SUM(sales) as sales, 
            0 as target 
            FROM 
            sales 
            GROUP BY 
            yop, 
            mop, 
            distcode, 
            distname, 
            brandcode, 
            brandname 
            UNION ALL 
            SELECT 
            yop, 
            mop, 
            distcode,
            distname,  
            brandcode, 
            brandname, 
            0 as sales, 
            SUM(target) as target 
            FROM 
            targetpenjualan 
            GROUP BY 
            yop, 
            mop, 
            distcode, 
            distname, 
            brandcode, 
            brandname
        ) AS a 
        WHERE 
        1 = 1 
        and a.brandcode LIKE '%" . $brandcode . "%'
        and a.distcode LIKE '%" . $distcode . "%'
        AND CAST(a.yop AS TEXT) LIKE '%" . $yop . "%' 
        AND CAST(a.mop AS TEXT) LIKE '%" . $mop . "%'
        GROUP BY 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname;");

        return $data;
    }

    public function get_data_y($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;

        // Menambahkan fitur search berdasarkan keyword
        $searchQuery = "";
        if (!empty($search)) {
            $search = strtolower($search);
            $searchQuery = "AND (LOWER(mb.brandcode) LIKE '%$search%' OR LOWER(mb.brandname) LIKE '%$search%' OR LOWER(mbb.kodebeban) LIKE '%$search%')";
        }

        $data = DB::connection('pgsql')->select("SELECT 
        mbb.kodebeban, 
        mb.brandcode, 
        mb.brandname, 
        SUM(a.sales) AS sales, 
        SUM(a.target) AS target, 
        ROUND(CASE WHEN SUM(a.target) > 0 THEN (SUM(a.sales) / SUM(a.target)) * 100 ELSE 0 END, 2) AS achievement
    FROM
    (
        SELECT brandcode, SUM(sales) AS sales, 0 AS target
        FROM sales
        GROUP BY brandcode

        UNION ALL

        SELECT brandcode, 0 AS sales, SUM(target) AS target
        FROM targetpenjualan
        GROUP BY brandcode
    ) AS a
    LEFT JOIN m_bridging_budget AS mbb ON mbb.brandcode = a.brandcode
    LEFT JOIN masterbrand AS mb ON mb.brandcode = a.brandcode
    WHERE mbb.kodebeban IS NOT NULL
    $searchQuery
    GROUP BY mbb.kodebeban, mb.brandcode, mb.brandname
    LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']}");

        return $data;
    }

    public function get_data_x($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;

        // Menambahkan fitur search berdasarkan keyword
        $searchQuery = "";
        if (!empty($search)) {
            $search = strtolower($search);
            // Pencarian pada kolom non-agregat (brandcode, distcode, brandname)
            $searchQuery = "AND (LOWER(a.brandcode) LIKE '%$search%' 
                          OR LOWER(a.distcode) LIKE '%$search%' 
                          OR LOWER(a.distname) LIKE '%$search%' 
                          OR LOWER(a.brandname) LIKE '%$search%')";
        }

        // Query yang diperbaiki
        $data = DB::connection('pgsql')->select("SELECT 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname,   -- Menambahkan brandname dari sales
        SUM(a.sales) as sales, 
        SUM(a.target) as target, 
        ROUND((SUM(a.sales)/SUM(a.target)) * 100, 2) as achievement
    FROM
    (
        SELECT yop, mop, distcode, distname, brandcode, brandname, SUM(sales) as sales, 0 as target
        FROM sales
        GROUP BY yop, mop, distcode, distname, brandcode, brandname
        
        UNION ALL
        
        SELECT yop, mop, distcode, distname, brandcode, brandname, 0 as sales, SUM(target) as target
        FROM targetpenjualan
        GROUP BY yop, mop, distcode, distname, brandcode, brandname
    ) AS a
    WHERE 1=1
    $searchQuery  -- Menambahkan query pencarian
    GROUP BY a.yop, a.mop, a.distcode, a.distname, a.brandcode, a.brandname
    LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']}");

        return $data;
    }

    public function get_data_excelmario($distcode, $brandcode, $yop, $mop, $arr_pagination)
    {
        if ($distcode == 'ALL') {
            $distcode = '';
        }

        if ($brandcode == 'ALL') {
            $brandcode = '';
        }

        if ($yop == 'ALL') {
            $yop = '';
        }

        if ($mop == 'ALL') {
            $mop = '';
        }

        if (!empty($search)) $arr_pagination['offset'] = 0;

        // Menambahkan fitur search berdasarkan keyword
        $searchQuery = "";
        if (!empty($search)) {
            $search = strtolower($search);
            // Pencarian pada kolom non-agregat (brandcode, distcode, brandname)
            $searchQuery = "AND (LOWER(a.brandcode) LIKE '%$search%' 
                          OR LOWER(a.distcode) LIKE '%$search%' 
                          OR LOWER(a.distname) LIKE '%$search%' 
                          OR LOWER(a.brandname) LIKE '%$search%')";
        }

        // Query yang diperbaiki
        $data = DB::connection('pgsql')->select("SELECT 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname,   -- Menambahkan brandname dari sales
        SUM(a.sales) as sales, 
        SUM(a.target) as target, 
        ROUND((SUM(a.sales)/SUM(a.target)) * 100, 2) as achievement
    FROM
    (
        SELECT yop, mop, distcode, distname, brandcode, brandname, SUM(sales) as sales, 0 as target
        FROM sales
        GROUP BY yop, mop, distcode, distname, brandcode, brandname
        
        UNION ALL
        
        SELECT yop, mop, distcode, distname, brandcode, brandname, 0 as sales, SUM(target) as target
        FROM targetpenjualan
        GROUP BY yop, mop, distcode, distname, brandcode, brandname
    ) AS a
    WHERE 1 = 1 
    AND a.brandcode LIKE '%" . $brandcode . "%'
    AND a.distcode LIKE '%" . $distcode . "%'
    AND CAST(a.yop AS TEXT) LIKE '%" . $yop . "%' 
    AND CAST(a.mop AS TEXT) LIKE '%" . $mop . "%'
    GROUP BY a.yop, a.mop, a.distcode, a.distname, a.brandcode, a.brandname
    LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']}");

        return $data;
    }

    public function get_data_excelsuggestion($distcode, $brandcode, $term, $arr_pagination)
    {

        if ($distcode == 'ALL') {
            $distcode = '';
        }

        if ($brandcode == 'ALL') {
            $brandcode = '';
        }

        // if ($yop == 'ALL') {
        //     $yop = '';
        // }

        // if($mop == 'ALL'){
        //     $mop = '';
        // }

        if ($term == 1) {
            $data = DB::select("
        SELECT  1 term,
                ROUND(((nowterm + nextterm) * ach.achievement) / 100, 0) AS budgetafterb,
                ROUND(((nowterm * (achievement / 100)) + nextterm), 0) AS budgetaftera,
                ach.* 
            FROM (
                SELECT 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname, 
        a.kodebeban,
        nowterm,
        nextterm,
        SUM(a.sales) as sales, 
        SUM(a.target) as target, 
        CASE
        WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
        ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
        END AS achievement
        FROM 
        (
            SELECT 
            s.yop, 
            s.mop, 
            s.distcode, 
            s.distname, 
            s.brandcode, 
            s.brandname, 
            SUM(sales) as sales, 
            0 as target,
            mbb.kodebeban,
            (CAST(bm.jan AS NUMERIC) + CAST(bm.feb AS NUMERIC)+ CAST(bm.mar AS NUMERIC)) AS nowterm,
            (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nextterm
            FROM 
            sales s
            inner join m_bridging_budget mbb on s.itemcode = mbb.itemcode
            inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
            GROUP BY 
            s.yop, 
            s.mop, 
            s.distcode, 
            s.distname, 
            s.brandcode, 
            s.brandname,
            mbb.kodebeban,
            bm.jan,
            bm.feb,
            bm.mar,
            bm.apr,
            bm.mei,
            bm.jun
            UNION ALL 
            SELECT 
            tp.yop, 
            tp.mop, 
            tp.distcode, 
            tp.distname, 
            tp.brandcode, 
            tp.brandname, 
            0 as sales, 
            SUM(target) as target,
            mbb.kodebeban,
            (CAST(bm.jan AS NUMERIC) + CAST(bm.feb AS NUMERIC)+ CAST(bm.mar AS NUMERIC)) AS nowterm,
            (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nextterm
            FROM 
            targetpenjualan tp
            inner join m_bridging_budget mbb on tp.itemcode = mbb.itemcode
            inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
            GROUP BY 
            tp.yop, 
            tp.mop, 
            tp.distcode, 
            tp.distname, 
            tp.brandcode, 
            tp.brandname,
            mbb.kodebeban,
            bm.jan,
            bm.feb,
            bm.mar,
            bm.apr,
            bm.mei,
            bm.jun
        ) AS a 
        WHERE 
        1 = 1 
        AND a.brandcode LIKE '%" . $brandcode . "%'
        AND a.distcode LIKE '%" . $distcode . "%'
        AND mop in ('1','2','3')
        GROUP BY 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname,
        a.kodebeban,
        a.nowterm, a.nextterm) as ach
        LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']}");
        }

        if ($term == 2) {
            $data = DB::select("
            SELECT 
                2 term,
                ROUND(((nowterm + nextterm) * ach.achievement) / 100, 0) AS budgetafterb,
                ROUND(((nowterm * (achievement / 100)) + nextterm), 0) AS budgetaftera, 
                ach.* 
            FROM (
            SELECT 
            a.yop, 
            a.mop, 
            a.distcode, 
            a.distname, 
            a.brandcode, 
            a.brandname, 
            a.kodebeban,
            nowterm,
            nextterm,
            SUM(a.sales) as sales, 
            SUM(a.target) as target, 
            CASE
            WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
            ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
            END AS achievement
            FROM 
            (
                SELECT 
                s.yop, 
                s.mop, 
                s.distcode, 
                s.distname, 
                s.brandcode, 
                s.brandname, 
                SUM(sales) as sales, 
                0 as target,
                mbb.kodebeban,
                (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nowterm,
                (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nextterm
                FROM 
                sales s
                inner join m_bridging_budget mbb on s.itemcode = mbb.itemcode
                inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                GROUP BY 
                s.yop, 
                s.mop, 
                s.distcode, 
                s.distname, 
                s.brandcode, 
                s.brandname,
                mbb.kodebeban,
                bm.apr,
                bm.mei,
                bm.jun,
                bm.jul,
                bm.ags,
                bm.sep
                UNION ALL 
                SELECT 
                tp.yop, 
                tp.mop, 
                tp.distcode, 
                tp.distname, 
                tp.brandcode, 
                tp.brandname, 
                0 as sales, 
                SUM(target) as target,
                mbb.kodebeban,
                (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nowterm,
                (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nextterm
                FROM 
                targetpenjualan tp
                inner join m_bridging_budget mbb on tp.itemcode = mbb.itemcode
                inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                GROUP BY 
                tp.yop, 
                tp.mop, 
                tp.distcode, 
                tp.distname, 
                tp.brandcode, 
                tp.brandname,
                mbb.kodebeban,
                bm.apr,
                bm.mei,
                bm.jun,
                bm.jul,
                bm.ags,
                bm.sep
            ) AS a 
            WHERE 
            1 = 1 
            AND a.brandcode LIKE '%" . $brandcode . "%'
            AND a.distcode LIKE '%" . $distcode . "%'
            AND mop in ('4','5','6')
            GROUP BY 
            a.yop, 
            a.mop, 
            a.distcode, 
            a.distname, 
            a.brandcode, 
            a.brandname,
            a.kodebeban,
            a.nowterm, a.nextterm) as ach
            LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']}");
        }

        if ($term == 3) {
            $data = DB::select("
                SELECT
                3 term, 
                ROUND(((nowterm + nextterm) * ach.achievement) / 100, 0) AS budgetafterb,
                ROUND(((nowterm * (achievement / 100)) + nextterm), 0) AS budgetaftera, 
                ach.* 
            FROM (
                SELECT 
                a.yop, 
                a.mop, 
                a.distcode, 
                a.distname, 
                a.brandcode, 
                a.brandname, 
                a.kodebeban,
                nowterm,
                nextterm,
                SUM(a.sales) as sales, 
                SUM(a.target) as target, 
                CASE
                WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
                ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
                END AS achievement
                FROM 
                (
                    SELECT 
                    s.yop, 
                    s.mop, 
                    s.distcode, 
                    s.distname, 
                    s.brandcode, 
                    s.brandname, 
                    SUM(sales) as sales, 
                    0 as target,
                    mbb.kodebeban,
                    (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nowterm,
                    (CAST(bm.okt AS NUMERIC) + CAST(bm.nop AS NUMERIC)+ CAST(bm.des AS NUMERIC)) AS nextterm
                    FROM 
                    sales s
                    inner join m_bridging_budget mbb on s.itemcode = mbb.itemcode
                    inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                    GROUP BY 
                    s.yop, 
                    s.mop, 
                    s.distcode, 
                    s.distname, 
                    s.brandcode, 
                    s.brandname,
                    mbb.kodebeban,
                    bm.jul,
                    bm.ags,
                    bm.sep,
                    bm.okt,
                    bm.nop,
                    bm.des
                    UNION ALL 
                    SELECT 
                    tp.yop, 
                    tp.mop, 
                    tp.distcode, 
                    tp.distname, 
                    tp.brandcode, 
                    tp.brandname, 
                    0 as sales, 
                    SUM(target) as target,
                    mbb.kodebeban,
                    (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nowterm,
                    (CAST(bm.okt AS NUMERIC) + CAST(bm.nop AS NUMERIC)+ CAST(bm.des AS NUMERIC)) AS nextterm
                    FROM 
                    targetpenjualan tp
                    inner join m_bridging_budget mbb on tp.itemcode = mbb.itemcode
                    inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                    GROUP BY 
                    tp.yop, 
                    tp.mop, 
                    tp.distcode, 
                    tp.distname, 
                    tp.brandcode, 
                    tp.brandname,
                    mbb.kodebeban,
                    bm.jul,
                    bm.ags,
                    bm.sep,
                    bm.okt,
                    bm.nop,
                    bm.des
                ) AS a 
                WHERE 
                1 = 1 
                AND a.brandcode LIKE '%" . $brandcode . "%'
                AND a.distcode LIKE '%" . $distcode . "%'
                AND mop in ('7','8','9')
                GROUP BY 
                a.yop, 
                a.mop, 
                a.distcode, 
                a.distname, 
                a.brandcode, 
                a.brandname,
                a.kodebeban,
                a.nowterm, a.nextterm) as ach
                LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']}");
        }

        return $data;
    }
    public function get_data_z($distcode, $brandcode, $term, $arr_pagination)
    {

        if ($distcode == 'ALL') {
            $distcode = '';
        }

        if ($brandcode == 'ALL') {
            $brandcode = '';
        }

        // if ($yop == 'ALL') {
        //     $yop = '';
        // }

        // if($mop == 'ALL'){
        //     $mop = '';
        // }

        if ($term == 1) {
            $data = DB::select("
        SELECT  1 term,
                ROUND(((nowterm + nextterm) * ach.achievement) / 100, 0) AS budgetafterb,
                ROUND(((nowterm * (achievement / 100)) + nextterm), 0) AS budgetaftera,
                ach.* 
            FROM (
                SELECT 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname, 
        a.kodebeban,
        nowterm,
        nextterm,
        SUM(a.sales) as sales, 
        SUM(a.target) as target, 
        CASE
        WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
        ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
        END AS achievement
        FROM 
        (
            SELECT 
            s.yop, 
            s.mop, 
            s.distcode, 
            s.distname, 
            s.brandcode, 
            s.brandname, 
            SUM(sales) as sales, 
            0 as target,
            mbb.kodebeban,
            (CAST(bm.jan AS NUMERIC) + CAST(bm.feb AS NUMERIC)+ CAST(bm.mar AS NUMERIC)) AS nowterm,
            (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nextterm
            FROM 
            sales s
            inner join m_bridging_budget mbb on s.itemcode = mbb.itemcode
            inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
            GROUP BY 
            s.yop, 
            s.mop, 
            s.distcode, 
            s.distname, 
            s.brandcode, 
            s.brandname,
            mbb.kodebeban,
            bm.jan,
            bm.feb,
            bm.mar,
            bm.apr,
            bm.mei,
            bm.jun
            UNION ALL 
            SELECT 
            tp.yop, 
            tp.mop, 
            tp.distcode, 
            tp.distname, 
            tp.brandcode, 
            tp.brandname, 
            0 as sales, 
            SUM(target) as target,
            mbb.kodebeban,
            (CAST(bm.jan AS NUMERIC) + CAST(bm.feb AS NUMERIC)+ CAST(bm.mar AS NUMERIC)) AS nowterm,
            (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nextterm
            FROM 
            targetpenjualan tp
            inner join m_bridging_budget mbb on tp.itemcode = mbb.itemcode
            inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
            GROUP BY 
            tp.yop, 
            tp.mop, 
            tp.distcode, 
            tp.distname, 
            tp.brandcode, 
            tp.brandname,
            mbb.kodebeban,
            bm.jan,
            bm.feb,
            bm.mar,
            bm.apr,
            bm.mei,
            bm.jun
        ) AS a 
        WHERE 
        1 = 1 
        AND a.brandcode LIKE '%" . $brandcode . "%'
        AND a.distcode LIKE '%" . $distcode . "%'
        AND mop in ('1','2','3')
        GROUP BY 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname,
        a.kodebeban,
        a.nowterm, a.nextterm) as ach");
        }

        if ($term == 2) {
            $data = DB::select("
            SELECT 
                2 term,
                ROUND(((nowterm + nextterm) * ach.achievement) / 100, 0) AS budgetafterb,
                ROUND(((nowterm * (achievement / 100)) + nextterm), 0) AS budgetaftera, 
                ach.* 
            FROM (
            SELECT 
            a.yop, 
            a.mop, 
            a.distcode, 
            a.distname, 
            a.brandcode, 
            a.brandname, 
            a.kodebeban,
            nowterm,
            nextterm,
            SUM(a.sales) as sales, 
            SUM(a.target) as target, 
            CASE
            WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
            ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
            END AS achievement
            FROM 
            (
                SELECT 
                s.yop, 
                s.mop, 
                s.distcode, 
                s.distname, 
                s.brandcode, 
                s.brandname, 
                SUM(sales) as sales, 
                0 as target,
                mbb.kodebeban,
                (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nowterm,
                (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nextterm
                FROM 
                sales s
                inner join m_bridging_budget mbb on s.itemcode = mbb.itemcode
                inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                GROUP BY 
                s.yop, 
                s.mop, 
                s.distcode, 
                s.distname, 
                s.brandcode, 
                s.brandname,
                mbb.kodebeban,
                bm.apr,
                bm.mei,
                bm.jun,
                bm.jul,
                bm.ags,
                bm.sep
                UNION ALL 
                SELECT 
                tp.yop, 
                tp.mop, 
                tp.distcode, 
                tp.distname, 
                tp.brandcode, 
                tp.brandname, 
                0 as sales, 
                SUM(target) as target,
                mbb.kodebeban,
                (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nowterm,
                (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nextterm
                FROM 
                targetpenjualan tp
                inner join m_bridging_budget mbb on tp.itemcode = mbb.itemcode
                inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                GROUP BY 
                tp.yop, 
                tp.mop, 
                tp.distcode, 
                tp.distname, 
                tp.brandcode, 
                tp.brandname,
                mbb.kodebeban,
                bm.apr,
                bm.mei,
                bm.jun,
                bm.jul,
                bm.ags,
                bm.sep
            ) AS a 
            WHERE 
            1 = 1 
            AND a.brandcode LIKE '%" . $brandcode . "%'
            AND a.distcode LIKE '%" . $distcode . "%'
            AND mop in ('4','5','6')
            GROUP BY 
            a.yop, 
            a.mop, 
            a.distcode, 
            a.distname, 
            a.brandcode, 
            a.brandname,
            a.kodebeban,
            a.nowterm, a.nextterm) as ach");
        }

        if ($term == 3) {
            $data = DB::select("
                SELECT
                3 term, 
                ROUND(((nowterm + nextterm) * ach.achievement) / 100, 0) AS budgetafterb,
                ROUND(((nowterm * (achievement / 100)) + nextterm), 0) AS budgetaftera, 
                ach.* 
            FROM (
                SELECT 
                a.yop, 
                a.mop, 
                a.distcode, 
                a.distname, 
                a.brandcode, 
                a.brandname, 
                a.kodebeban,
                nowterm,
                nextterm,
                SUM(a.sales) as sales, 
                SUM(a.target) as target, 
                CASE
                WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
                ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
                END AS achievement
                FROM 
                (
                    SELECT 
                    s.yop, 
                    s.mop, 
                    s.distcode, 
                    s.distname, 
                    s.brandcode, 
                    s.brandname, 
                    SUM(sales) as sales, 
                    0 as target,
                    mbb.kodebeban,
                    (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nowterm,
                    (CAST(bm.okt AS NUMERIC) + CAST(bm.nop AS NUMERIC)+ CAST(bm.des AS NUMERIC)) AS nextterm
                    FROM 
                    sales s
                    inner join m_bridging_budget mbb on s.itemcode = mbb.itemcode
                    inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                    GROUP BY 
                    s.yop, 
                    s.mop, 
                    s.distcode, 
                    s.distname, 
                    s.brandcode, 
                    s.brandname,
                    mbb.kodebeban,
                    bm.jul,
                    bm.ags,
                    bm.sep,
                    bm.okt,
                    bm.nop,
                    bm.des
                    UNION ALL 
                    SELECT 
                    tp.yop, 
                    tp.mop, 
                    tp.distcode, 
                    tp.distname, 
                    tp.brandcode, 
                    tp.brandname, 
                    0 as sales, 
                    SUM(target) as target,
                    mbb.kodebeban,
                    (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nowterm,
                    (CAST(bm.okt AS NUMERIC) + CAST(bm.nop AS NUMERIC)+ CAST(bm.des AS NUMERIC)) AS nextterm
                    FROM 
                    targetpenjualan tp
                    inner join m_bridging_budget mbb on tp.itemcode = mbb.itemcode
                    inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                    GROUP BY 
                    tp.yop, 
                    tp.mop, 
                    tp.distcode, 
                    tp.distname, 
                    tp.brandcode, 
                    tp.brandname,
                    mbb.kodebeban,
                    bm.jul,
                    bm.ags,
                    bm.sep,
                    bm.okt,
                    bm.nop,
                    bm.des
                ) AS a 
                WHERE 
                1 = 1 
                AND a.brandcode LIKE '%" . $brandcode . "%'
                AND a.distcode LIKE '%" . $distcode . "%'
                AND mop in ('7','8','9')
                GROUP BY 
                a.yop, 
                a.mop, 
                a.distcode, 
                a.distname, 
                a.brandcode, 
                a.brandname,
                a.kodebeban,
                a.nowterm, a.nextterm) as ach");
        }

        return $data;
    }

    public function get_data_penampung($distcode, $brandcode, $term, $arr_pagination)
    {

        if ($distcode == 'ALL') {
            $distcode = '';
        }
        if ($brandcode == 'ALL') {
            $brandcode = '';
        }
        // if ($yop == 'ALL') {
        //     $yop = '';
        // }

        // if($mop == 'ALL'){
        //     $mop = '';
        // }
        if ($term == 1) {
            $data = DB::select("
        SELECT  1 term,
        ROUND(((nextterm * (achievement / 100)) - bt.realizationq1), 0) AS budgetafterq1,
        ach.* 
        FROM (SELECT 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname, 
        a.kodebeban,
        nowterm,
        nextterm,
        SUM(a.sales) as sales, 
        SUM(a.target) as target, 
        CASE
            WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
            ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
        END AS achievement
    FROM (
        SELECT 
            s.yop, 
            s.mop, 
            s.distcode, 
            s.distname, 
            s.brandcode, 
            s.brandname, 
            SUM(sales) as sales, 
            0 as target,
            mbb.kodebeban,
            (CAST(bm.jan AS NUMERIC) + CAST(bm.feb AS NUMERIC) + CAST(bm.mar AS NUMERIC)) AS nowterm,
            (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC) + CAST(bm.jun AS NUMERIC)) AS nextterm
        FROM 
            sales s
        INNER JOIN m_bridging_budget mbb ON s.itemcode = mbb.itemcode
        INNER JOIN budget_monitorings bm ON bm.kodebeban = mbb.kodebeban 
        GROUP BY 
            s.yop, 
            s.mop, 
            s.distcode, 
            s.distname, 
            s.brandcode, 
            s.brandname,
            mbb.kodebeban,
            bm.jan,
            bm.feb,
            bm.mar,
            bm.apr,
            bm.mei,
            bm.jun
        UNION ALL 
        SELECT 
            tp.yop, 
            tp.mop, 
            tp.distcode, 
            tp.distname, 
            tp.brandcode, 
            tp.brandname, 
            0 as sales, 
            SUM(target) as target,
            mbb.kodebeban,
            (CAST(bm.jan AS NUMERIC) + CAST(bm.feb AS NUMERIC) + CAST(bm.mar AS NUMERIC)) AS nowterm,
            (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC) + CAST(bm.jun AS NUMERIC)) AS nextterm
        FROM 
            targetpenjualan tp
        INNER JOIN m_bridging_budget mbb ON tp.itemcode = mbb.itemcode
        INNER JOIN budget_monitorings bm ON bm.kodebeban = mbb.kodebeban 
        GROUP BY 
            tp.yop, 
            tp.mop, 
            tp.distcode, 
            tp.distname, 
            tp.brandcode, 
            tp.brandname,
            mbb.kodebeban,
            bm.jan,
            bm.feb,
            bm.mar,
            bm.apr,
            bm.mei,
            bm.jun
    ) AS a 
    WHERE 
        1 = 1 
        AND a.brandcode LIKE '%" . $brandcode . "%'
        AND a.distcode LIKE '%" . $distcode . "%'
        AND mop IN ('1','2','3')
    GROUP BY 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname,
        a.kodebeban,
        a.nowterm, 
        a.nextterm
) as ach
LEFT JOIN budgetterm bt ON ach.kodebeban = bt.kodebeban");
        }

        if ($term == 2) {
            $data = DB::select("SELECT 2 term,
        ROUND(((nextterm * (achievement / 100)) - bt.realizationq2), 0) AS budgetafterq2,
        ach.* 
FROM (
    SELECT 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname, 
        a.kodebeban,
        nowterm,
        nextterm,
        SUM(a.sales) as sales, 
        SUM(a.target) as target, 
        CASE
            WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
            ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
        END AS achievement
    FROM (
        SELECT 
            s.yop, 
            s.mop, 
            s.distcode, 
            s.distname, 
            s.brandcode, 
            s.brandname, 
            SUM(sales) as sales, 
            0 as target,
            mbb.kodebeban,
            (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nowterm,
            (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nextterm
        FROM 
            sales s
        INNER JOIN m_bridging_budget mbb ON s.itemcode = mbb.itemcode
        INNER JOIN budget_monitorings bm ON bm.kodebeban = mbb.kodebeban 
        GROUP BY 
            s.yop, 
            s.mop, 
            s.distcode, 
            s.distname, 
            s.brandcode, 
            s.brandname,
            mbb.kodebeban,
            bm.apr,
            bm.mei,
            bm.jun,
            bm.jul,
            bm.ags,
            bm.sep
        UNION ALL 
        SELECT 
            tp.yop, 
            tp.mop, 
            tp.distcode, 
            tp.distname, 
            tp.brandcode, 
            tp.brandname, 
            0 as sales, 
            SUM(target) as target,
            mbb.kodebeban,
            (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nowterm,
            (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nextterm
        FROM 
            targetpenjualan tp
        INNER JOIN m_bridging_budget mbb ON tp.itemcode = mbb.itemcode
        INNER JOIN budget_monitorings bm ON bm.kodebeban = mbb.kodebeban 
        GROUP BY 
            tp.yop, 
            tp.mop, 
            tp.distcode, 
            tp.distname, 
            tp.brandcode, 
            tp.brandname,
            mbb.kodebeban,
            bm.apr,
            bm.mei,
            bm.jun,
            bm.jul,
            bm.ags,
            bm.sep
    ) AS a 
    WHERE 
        1 = 1 
        AND a.brandcode LIKE '%" . $brandcode . "%'
        AND a.distcode LIKE '%" . $distcode . "%'
        AND mop IN ('4','5','6')
    GROUP BY 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname,
        a.kodebeban,
        a.nowterm, 
        a.nextterm
) as ach
LEFT JOIN budgetterm bt ON ach.kodebeban = bt.kodebeban");
        }

        if ($term == 3) {
            $data = DB::select("SELECT 3 term,
       ROUND(((nextterm * (achievement / 100)) - bt.realizationq3), 0) AS budgetafterq3,
       ach.* 
FROM (
    SELECT 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname, 
        a.kodebeban,
        nowterm,
        nextterm,
        SUM(a.sales) as sales, 
        SUM(a.target) as target, 
        CASE
            WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
            ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
        END AS achievement
    FROM (
        SELECT 
            s.yop, 
            s.mop, 
            s.distcode, 
            s.distname, 
            s.brandcode, 
            s.brandname, 
            SUM(sales) as sales, 
            0 as target,
            mbb.kodebeban,
            (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC) + CAST(bm.sep AS NUMERIC)) AS nowterm,
            (CAST(bm.okt AS NUMERIC) + CAST(bm.nop AS NUMERIC) + CAST(bm.des AS NUMERIC)) AS nextterm
        FROM 
            sales s
        INNER JOIN m_bridging_budget mbb ON s.itemcode = mbb.itemcode
        INNER JOIN budget_monitorings bm ON bm.kodebeban = mbb.kodebeban 
        GROUP BY 
            s.yop, 
            s.mop, 
            s.distcode, 
            s.distname, 
            s.brandcode, 
            s.brandname,
            mbb.kodebeban,
            bm.jul,
            bm.ags,
            bm.sep,
            bm.okt,
            bm.nop,
            bm.des
        UNION ALL 
        SELECT 
            tp.yop, 
            tp.mop, 
            tp.distcode, 
            tp.distname, 
            tp.brandcode, 
            tp.brandname, 
            0 as sales, 
            SUM(target) as target,
            mbb.kodebeban,
            (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC) + CAST(bm.sep AS NUMERIC)) AS nowterm,
            (CAST(bm.okt AS NUMERIC) + CAST(bm.nop AS NUMERIC) + CAST(bm.des AS NUMERIC)) AS nextterm
        FROM 
            targetpenjualan tp
        INNER JOIN m_bridging_budget mbb ON tp.itemcode = mbb.itemcode
        INNER JOIN budget_monitorings bm ON bm.kodebeban = mbb.kodebeban 
        GROUP BY 
            tp.yop, 
            tp.mop, 
            tp.distcode, 
            tp.distname, 
            tp.brandcode, 
            tp.brandname,
            mbb.kodebeban,
            bm.jul,
            bm.ags,
            bm.sep,
            bm.okt,
            bm.nop,
            bm.des
    ) AS a 
    WHERE 
        1 = 1 
        AND a.brandcode LIKE '%" . $brandcode . "%'
        AND a.distcode LIKE '%" . $distcode . "%'
        AND mop IN ('7','8','9')
    GROUP BY 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname,
        a.kodebeban,
        a.nowterm, 
        a.nextterm
) as ach
LEFT JOIN budgetterm bt ON ach.kodebeban = bt.kodebeban
");
        }

        return $data;
    }

    public function get_data_updatebudget($kodebeban, $term, $arr_pagination)
    {

        if ($term == 1) {
            $data = DB::select("
        SELECT  1 term,
                ROUND(((nowterm + nextterm) * ach.achievement) / 100, 0) AS budgetafterb,
                ROUND(((nowterm * (achievement / 100)) + nextterm), 0) AS budgetaftera,
                ach.* 
            FROM (
                SELECT 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname, 
        a.kodebeban,
        nowterm,
        nextterm,
        SUM(a.sales) as sales, 
        SUM(a.target) as target, 
        CASE
        WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
        ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
        END AS achievement
        FROM 
        (
            SELECT 
            s.yop, 
            s.mop, 
            s.distcode, 
            s.distname, 
            s.brandcode, 
            s.brandname, 
            SUM(sales) as sales, 
            0 as target,
            mbb.kodebeban,
            (CAST(bm.jan AS NUMERIC) + CAST(bm.feb AS NUMERIC)+ CAST(bm.mar AS NUMERIC)) AS nowterm,
            (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nextterm
            FROM 
            sales s
            inner join m_bridging_budget mbb on s.itemcode = mbb.itemcode
            inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
            GROUP BY 
            s.yop, 
            s.mop, 
            s.distcode, 
            s.distname, 
            s.brandcode, 
            s.brandname,
            mbb.kodebeban,
            bm.jan,
            bm.feb,
            bm.mar,
            bm.apr,
            bm.mei,
            bm.jun
            UNION ALL 
            SELECT 
            tp.yop, 
            tp.mop, 
            tp.distcode, 
            tp.distname, 
            tp.brandcode, 
            tp.brandname, 
            0 as sales, 
            SUM(target) as target,
            mbb.kodebeban,
            (CAST(bm.jan AS NUMERIC) + CAST(bm.feb AS NUMERIC)+ CAST(bm.mar AS NUMERIC)) AS nowterm,
            (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nextterm
            FROM 
            targetpenjualan tp
            inner join m_bridging_budget mbb on tp.itemcode = mbb.itemcode
            inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
            GROUP BY 
            tp.yop, 
            tp.mop, 
            tp.distcode, 
            tp.distname, 
            tp.brandcode, 
            tp.brandname,
            mbb.kodebeban,
            bm.jan,
            bm.feb,
            bm.mar,
            bm.apr,
            bm.mei,
            bm.jun
        ) AS a 
        WHERE 
        1 = 1 
        AND mop in ('1','2','3')
        AND kodebeban LIKE '%" . $kodebeban . "%'
        GROUP BY 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname,
        a.kodebeban,
        a.nowterm, a.nextterm) as ach
        LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']}");
        }


        if ($term == 2) {
            $data = DB::select("
            SELECT 
                2 term,
                ROUND(((nowterm + nextterm) * ach.achievement) / 100, 0) AS budgetafterb,
                ROUND(((nowterm * (achievement / 100)) + nextterm), 0) AS budgetaftera, 
                ach.* 
            FROM (
            SELECT 
            a.yop, 
            a.mop, 
            a.distcode, 
            a.distname, 
            a.brandcode, 
            a.brandname, 
            a.kodebeban,
            nowterm,
            nextterm,
            SUM(a.sales) as sales, 
            SUM(a.target) as target, 
            CASE
            WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
            ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
            END AS achievement
            FROM 
            (
                SELECT 
                s.yop, 
                s.mop, 
                s.distcode, 
                s.distname, 
                s.brandcode, 
                s.brandname, 
                SUM(sales) as sales, 
                0 as target,
                mbb.kodebeban,
                (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nowterm,
                (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nextterm
                FROM 
                sales s
                inner join m_bridging_budget mbb on s.itemcode = mbb.itemcode
                inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                GROUP BY 
                s.yop, 
                s.mop, 
                s.distcode, 
                s.distname, 
                s.brandcode, 
                s.brandname,
                mbb.kodebeban,
                bm.apr,
                bm.mei,
                bm.jun,
                bm.jul,
                bm.ags,
                bm.sep
                UNION ALL 
                SELECT 
                tp.yop, 
                tp.mop, 
                tp.distcode, 
                tp.distname, 
                tp.brandcode, 
                tp.brandname, 
                0 as sales, 
                SUM(target) as target,
                mbb.kodebeban,
                (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nowterm,
                (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nextterm
                FROM 
                targetpenjualan tp
                inner join m_bridging_budget mbb on tp.itemcode = mbb.itemcode
                inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                GROUP BY 
                tp.yop, 
                tp.mop, 
                tp.distcode, 
                tp.distname, 
                tp.brandcode, 
                tp.brandname,
                mbb.kodebeban,
                bm.apr,
                bm.mei,
                bm.jun,
                bm.jul,
                bm.ags,
                bm.sep
            ) AS a 
            WHERE 
            1 = 1 
            AND mop in ('4','5','6')
            AND kodebeban LIKE '%" . $kodebeban . "%'
            GROUP BY 
            a.yop, 
            a.mop, 
            a.distcode, 
            a.distname, 
            a.brandcode, 
            a.brandname,
            a.kodebeban,
            a.nowterm, a.nextterm) as ach
            LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']}");
        }

        if ($term == 3) {
            $data = DB::select("
                SELECT
                3 term, 
                ROUND(((nowterm + nextterm) * ach.achievement) / 100, 0) AS budgetafterb,
                ROUND(((nowterm * (achievement / 100)) + nextterm), 0) AS budgetaftera, 
                ach.* 
            FROM (
                SELECT 
                a.yop, 
                a.mop, 
                a.distcode, 
                a.distname, 
                a.brandcode, 
                a.brandname, 
                a.kodebeban,
                nowterm,
                nextterm,
                SUM(a.sales) as sales, 
                SUM(a.target) as target, 
                CASE
                WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
                ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
                END AS achievement
                FROM 
                (
                    SELECT 
                    s.yop, 
                    s.mop, 
                    s.distcode, 
                    s.distname, 
                    s.brandcode, 
                    s.brandname, 
                    SUM(sales) as sales, 
                    0 as target,
                    mbb.kodebeban,
                    (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nowterm,
                    (CAST(bm.okt AS NUMERIC) + CAST(bm.nop AS NUMERIC)+ CAST(bm.des AS NUMERIC)) AS nextterm
                    FROM 
                    sales s
                    inner join m_bridging_budget mbb on s.itemcode = mbb.itemcode
                    inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                    GROUP BY 
                    s.yop, 
                    s.mop, 
                    s.distcode, 
                    s.distname, 
                    s.brandcode, 
                    s.brandname,
                    mbb.kodebeban,
                    bm.jul,
                    bm.ags,
                    bm.sep,
                    bm.okt,
                    bm.nop,
                    bm.des
                    UNION ALL 
                    SELECT 
                    tp.yop, 
                    tp.mop, 
                    tp.distcode, 
                    tp.distname, 
                    tp.brandcode, 
                    tp.brandname, 
                    0 as sales, 
                    SUM(target) as target,
                    mbb.kodebeban,
                    (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nowterm,
                    (CAST(bm.okt AS NUMERIC) + CAST(bm.nop AS NUMERIC)+ CAST(bm.des AS NUMERIC)) AS nextterm
                    FROM 
                    targetpenjualan tp
                    inner join m_bridging_budget mbb on tp.itemcode = mbb.itemcode
                    inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                    GROUP BY 
                    tp.yop, 
                    tp.mop, 
                    tp.distcode, 
                    tp.distname, 
                    tp.brandcode, 
                    tp.brandname,
                    mbb.kodebeban,
                    bm.jul,
                    bm.ags,
                    bm.sep,
                    bm.okt,
                    bm.nop,
                    bm.des
                ) AS a 
                WHERE 
                1 = 1 
                AND mop in ('7','8','9')
                AND kodebeban LIKE '%" . $kodebeban . "%'
                GROUP BY 
                a.yop, 
                a.mop, 
                a.distcode, 
                a.distname, 
                a.brandcode, 
                a.brandname,
                a.kodebeban,
                a.nowterm, a.nextterm) as ach
                LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']}");
        }

        return $data;
    }

    public function get_data_updatepenampung($kodebeban, $term, $arr_pagination)
    {

        if ($term == 1) {
            $data = DB::select("
        SELECT  1 term,
                ROUND(((nextterm * (achievement / 100)) - bt.realizationq1), 0) AS budgetafterq1,
                ach.* 
            FROM (
                SELECT 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname, 
        a.kodebeban,
        nowterm,
        nextterm,
        SUM(a.sales) as sales, 
        SUM(a.target) as target, 
        CASE
        WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
        ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
        END AS achievement
        FROM 
        (
            SELECT 
            s.yop, 
            s.mop, 
            s.distcode, 
            s.distname, 
            s.brandcode, 
            s.brandname, 
            SUM(sales) as sales, 
            0 as target,
            mbb.kodebeban,
            (CAST(bm.jan AS NUMERIC) + CAST(bm.feb AS NUMERIC)+ CAST(bm.mar AS NUMERIC)) AS nowterm,
            (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nextterm
            FROM 
            sales s
            inner join m_bridging_budget mbb on s.itemcode = mbb.itemcode
            inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
            GROUP BY 
            s.yop, 
            s.mop, 
            s.distcode, 
            s.distname, 
            s.brandcode, 
            s.brandname,
            mbb.kodebeban,
            bm.jan,
            bm.feb,
            bm.mar,
            bm.apr,
            bm.mei,
            bm.jun
            UNION ALL 
            SELECT 
            tp.yop, 
            tp.mop, 
            tp.distcode, 
            tp.distname, 
            tp.brandcode, 
            tp.brandname, 
            0 as sales, 
            SUM(target) as target,
            mbb.kodebeban,
            (CAST(bm.jan AS NUMERIC) + CAST(bm.feb AS NUMERIC)+ CAST(bm.mar AS NUMERIC)) AS nowterm,
            (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nextterm
            FROM 
            targetpenjualan tp
            inner join m_bridging_budget mbb on tp.itemcode = mbb.itemcode
            inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
            GROUP BY 
            tp.yop, 
            tp.mop, 
            tp.distcode, 
            tp.distname, 
            tp.brandcode, 
            tp.brandname,
            mbb.kodebeban,
            bm.jan,
            bm.feb,
            bm.mar,
            bm.apr,
            bm.mei,
            bm.jun
        ) AS a 
        WHERE 
        1 = 1 
        AND mop in ('1','2','3')
        AND kodebeban LIKE '%" . $kodebeban . "%'
        GROUP BY 
        a.yop, 
        a.mop, 
        a.distcode, 
        a.distname, 
        a.brandcode, 
        a.brandname,
        a.kodebeban,
        a.nowterm, a.nextterm) as ach
        LEFT JOIN budgetterm bt ON ach.kodebeban = bt.kodebeban
        LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']}");
        }


        if ($term == 2) {
            $data = DB::select("
            SELECT 
                2 term,
               ROUND(((nextterm * (achievement / 100)) - bt.realizationq2), 0) AS budgetafterq2,
                ach.* 
            FROM (
            SELECT 
            a.yop, 
            a.mop, 
            a.distcode, 
            a.distname, 
            a.brandcode, 
            a.brandname, 
            a.kodebeban,
            nowterm,
            nextterm,
            SUM(a.sales) as sales, 
            SUM(a.target) as target, 
            CASE
            WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
            ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
            END AS achievement
            FROM 
            (
                SELECT 
                s.yop, 
                s.mop, 
                s.distcode, 
                s.distname, 
                s.brandcode, 
                s.brandname, 
                SUM(sales) as sales, 
                0 as target,
                mbb.kodebeban,
                (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nowterm,
                (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nextterm
                FROM 
                sales s
                inner join m_bridging_budget mbb on s.itemcode = mbb.itemcode
                inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                GROUP BY 
                s.yop, 
                s.mop, 
                s.distcode, 
                s.distname, 
                s.brandcode, 
                s.brandname,
                mbb.kodebeban,
                bm.apr,
                bm.mei,
                bm.jun,
                bm.jul,
                bm.ags,
                bm.sep
                UNION ALL 
                SELECT 
                tp.yop, 
                tp.mop, 
                tp.distcode, 
                tp.distname, 
                tp.brandcode, 
                tp.brandname, 
                0 as sales, 
                SUM(target) as target,
                mbb.kodebeban,
                (CAST(bm.apr AS NUMERIC) + CAST(bm.mei AS NUMERIC)+ CAST(bm.jun AS NUMERIC)) AS nowterm,
                (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nextterm
                FROM 
                targetpenjualan tp
                inner join m_bridging_budget mbb on tp.itemcode = mbb.itemcode
                inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                GROUP BY 
                tp.yop, 
                tp.mop, 
                tp.distcode, 
                tp.distname, 
                tp.brandcode, 
                tp.brandname,
                mbb.kodebeban,
                bm.apr,
                bm.mei,
                bm.jun,
                bm.jul,
                bm.ags,
                bm.sep
            ) AS a 
            WHERE 
            1 = 1 
            AND mop in ('4','5','6')
            AND kodebeban LIKE '%" . $kodebeban . "%'
            GROUP BY 
            a.yop, 
            a.mop, 
            a.distcode, 
            a.distname, 
            a.brandcode, 
            a.brandname,
            a.kodebeban,
            a.nowterm, a.nextterm) as ach
            LEFT JOIN budgetterm bt ON ach.kodebeban = bt.kodebeban
            LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']}");
        }

        if ($term == 3) {
            $data = DB::select("
                SELECT
                3 term, 
                ROUND(((nextterm * (achievement / 100)) - bt.realizationq3), 0) AS budgetafterq3,
                ach.* 
            FROM (
                SELECT 
                a.yop, 
                a.mop, 
                a.distcode, 
                a.distname, 
                a.brandcode, 
                a.brandname, 
                a.kodebeban,
                nowterm,
                nextterm,
                SUM(a.sales) as sales, 
                SUM(a.target) as target, 
                CASE
                WHEN ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2) > 100 THEN 100
                ELSE ROUND((SUM(a.sales) / SUM(a.target)) * 100, 2)
                END AS achievement
                FROM 
                (
                    SELECT 
                    s.yop, 
                    s.mop, 
                    s.distcode, 
                    s.distname, 
                    s.brandcode, 
                    s.brandname, 
                    SUM(sales) as sales, 
                    0 as target,
                    mbb.kodebeban,
                    (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nowterm,
                    (CAST(bm.okt AS NUMERIC) + CAST(bm.nop AS NUMERIC)+ CAST(bm.des AS NUMERIC)) AS nextterm
                    FROM 
                    sales s
                    inner join m_bridging_budget mbb on s.itemcode = mbb.itemcode
                    inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                    GROUP BY 
                    s.yop, 
                    s.mop, 
                    s.distcode, 
                    s.distname, 
                    s.brandcode, 
                    s.brandname,
                    mbb.kodebeban,
                    bm.jul,
                    bm.ags,
                    bm.sep,
                    bm.okt,
                    bm.nop,
                    bm.des
                    UNION ALL 
                    SELECT 
                    tp.yop, 
                    tp.mop, 
                    tp.distcode, 
                    tp.distname, 
                    tp.brandcode, 
                    tp.brandname, 
                    0 as sales, 
                    SUM(target) as target,
                    mbb.kodebeban,
                    (CAST(bm.jul AS NUMERIC) + CAST(bm.ags AS NUMERIC)+ CAST(bm.sep AS NUMERIC)) AS nowterm,
                    (CAST(bm.okt AS NUMERIC) + CAST(bm.nop AS NUMERIC)+ CAST(bm.des AS NUMERIC)) AS nextterm
                    FROM 
                    targetpenjualan tp
                    inner join m_bridging_budget mbb on tp.itemcode = mbb.itemcode
                    inner join budget_monitorings bm on bm.kodebeban = mbb.kodebeban 
                    GROUP BY 
                    tp.yop, 
                    tp.mop, 
                    tp.distcode, 
                    tp.distname, 
                    tp.brandcode, 
                    tp.brandname,
                    mbb.kodebeban,
                    bm.jul,
                    bm.ags,
                    bm.sep,
                    bm.okt,
                    bm.nop,
                    bm.des
                ) AS a 
                WHERE 
                1 = 1 
                AND mop in ('7','8','9')
                AND kodebeban LIKE '%" . $kodebeban . "%'
                GROUP BY 
                a.yop, 
                a.mop, 
                a.distcode, 
                a.distname, 
                a.brandcode, 
                a.brandname,
                a.kodebeban,
                a.nowterm, a.nextterm) as ach
                LEFT JOIN budgetterm bt ON ach.kodebeban = bt.kodebeban
                LIMIT {$arr_pagination['limit']} OFFSET {$arr_pagination['offset']}");
        }

        return $data;
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);
        $data = targetpenjualan::where(function ($query) use ($search) {
            $query->whereRaw("LOWER(\"brandcode\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"brandname\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"itemname\") LIKE ?", ["%$search%"])
                ->orWhereRaw("LOWER(\"itemcode\") LIKE ?", ["%$search%"])
                ->orWhere("target", "LIKE", "%$search%")
                ->orWhere("yop", "LIKE", "%$search%")
                ->orWhere("mop", "LIKE", "%$search%")
                ->orWhere("distcode", "LIKE", "%$search%")
                ->orWhere("distname", "LIKE", "%$search%");
        })
            ->select(
                'id',
                'brandcode',
                'brandname',
                'itemname',
                'itemcode',
                'distcode',
                'distname',
                'target',
                'yop',
                'mop',
            )
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();
        // ->toSql();
        // ->orderBy('id', 'ASC')->toSql();
        return $data;
    }


}
