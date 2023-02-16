<?php
namespace App\Http\Controllers\Api;

use App\Laravue\JsonResponse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PDFController extends BaseController
{
    public function workSummaryPDF(Request $request){ 
        $workdone = $materials = $labour = $equipment = $profitloss = 0;        
       // $spreadsheet_id = '7438';
       $spreadsheet_id = $request->selected;
       
       $elementarysummary = [];
       $dBData = DB::connection('mysql')->select( DB::raw("
                    SELECT 
                                bl.id,
                                bl.title,
                                bl.parent,
                                (SELECT 
                                        IFNULL(SUM(wd.quantity * wd.rate), 0) workdone
                                    FROM
                                        work_done wd
                                    WHERE
                                        wd.parent IN (SELECT 
                                                CONCAT(cell)
                                            FROM
                                                boq_cells bc
                                            WHERE
                                                bc.parent = bl.id)) workdone,
                                (SELECT 
                                        IFNULL(SUM(m.quantity * m.price), 0) materials
                                    FROM
                                        materials_used m
                                    WHERE
                                        m.parent IN (SELECT 
                                                CONCAT(cell)
                                            FROM
                                                boq_cells bc
                                            WHERE
                                                bc.parent = bl.id)) materials,
                                (SELECT 
                                        IFNULL(SUM(l.number * l.rate), 0) labour
                                    FROM
                                        labour l
                                    WHERE
                                        l.parent IN (SELECT 
                                                CONCAT(cell)
                                            FROM
                                                boq_cells bc
                                            WHERE
                                                bc.parent = bl.id)) labour,
                                (SELECT 
                                        IFNULL(SUM(e.quantity * e.rate), 0) equipment
                                    FROM
                                        equipment e
                                    WHERE
                                        e.parent IN (SELECT 
                                                CONCAT(cell)
                                            FROM
                                                boq_cells bc
                                            WHERE
                                                bc.parent = bl.id)) equipment
                            FROM
                                boq_levels bl
                            WHERE
                                bl.level = 1
       "));

       foreach ($dBData as $key =>$resultdata){
        $item = sprintf("%02d",intval($resultdata->title)); 
        $profit = $resultdata->workdone - ($resultdata->materials + $resultdata->labour + $resultdata->equipment);
        $elementarysummary[] = array($item,preg_replace('/\d/', '',trim($resultdata->title)),number_format($resultdata->workdone,2),number_format($resultdata->materials,2),number_format($resultdata->labour,2),number_format($resultdata->equipment,2),number_format($profit,2),$resultdata->id);  
        } 
       
       /*$elementarysummary = [];
       $dBData = DB::connection('mysql')->select( DB::raw("
        SELECT 
                bl.id,
                bl.title,
                bl.parent,
                GROUP_CONCAT(bc.cell),
                FORMAT(IFNULL(SUM(wd.quantity * wd.rate), 0),
                    2) workdone,
                FORMAT(IFNULL(SUM(m.quantity * m.price), 0),
                    2) materials,
                FORMAT(IFNULL(SUM(l.number * l.rate), 0),
                    2) labour,
                FORMAT(IFNULL(SUM(e.quantity * e.rate), 0),
                    2) equipment,
                FORMAT(IFNULL(SUM(wd.quantity * wd.rate), 0) - (IFNULL(SUM(m.quantity * m.price), 0) + IFNULL(SUM(l.number * l.rate), 0) + IFNULL(SUM(e.quantity * e.rate), 0)),
                    2) profitloss
            FROM
                boq_levels bl
                    JOIN
                boq_cells bc ON bc.parent = bl.id
                    LEFT JOIN
                work_done wd ON wd.parent IN (CONCAT(bc.cell))
                    LEFT JOIN
                materials_used m ON m.parent IN (CONCAT(bc.cell))
                    LEFT JOIN
                labour l ON l.parent IN (CONCAT(bc.cell))
                    LEFT JOIN
                equipment e ON e.parent IN (CONCAT(bc.cell))
            WHERE
                bl.level = 1
            GROUP BY bl.id
       "));

       foreach ($dBData as $key =>$resultdata){
        $item = sprintf("%02d",intval($resultdata->title)); 
        $elementarysummary[] = array($item,preg_replace('/\d/', '',$resultdata->title),$resultdata->workdone,$resultdata->materials,$resultdata->labour,$resultdata->equipment,$resultdata->profitloss,$resultdata->id);  
        } */

        $elements = [];
        foreach ($elementarysummary as $key => $element) {
            $dBDatas = DB::connection('mysql')->select( DB::raw("
            SELECT 
                *
            FROM
                boq_levels bl
            WHERE
                parent = '".$element[7]."';
            "));
           
            foreach ($dBDatas as $key =>$resultdata2){
                $index = preg_replace("/[^0-9\.]/", '', trim($resultdata2->title));
                $title = preg_replace('/\d/', '',str_replace('.','',trim($resultdata2->title))); 

                $dBDatasItem = DB::connection('mysql')->select( DB::raw("
                     SELECT 
                        bl.id,
                        bl.title,
                        COALESCE(wd.quantity, 0) quantity,
                        COALESCE(wd.rate, bc.rate) rate,
                        COALESCE(wd.unit, bc.unit) unit,
                        FORMAT(IFNULL(SUM(wd.quantity * wd.rate), 0),
                            2) amount
                    FROM
                        boq_levels bl
                            JOIN
                        boq_cells bc ON bl.id = CONCAT(bc.parent)
                            LEFT JOIN
                        work_done wd ON wd.parent = bc.cell
                            AND bc.boq_id = wd.bill_of_qty_id
                    WHERE
                        bl.parent = '".$resultdata2->id."' AND wd.quantity > 0 
                    GROUP BY bl.id;
                "));
                if(count($dBDatasItem) > 0){
                    $qty = $dBDatasItem[0]->quantity;
                    if($qty > 0 ){
                        $elements[$element[1]][] = array($index,trim($title),$dBDatasItem);
                    } 
                }
                
            }
        }
        //var_dump($elements); //exit();


        $workdonesummary = [];
        $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT 
                SUM(w.quantity) quantity,
                w.unit,
                w.rate,
                FORMAT((SUM(w.quantity) * w.rate), 2) amount,
                bc.title,
                w.parent,
                w.timestamp,
                DATE_FORMAT(w.timestamp, '%W %M %e %Y') wdate,
                bl2.title maintitle
            FROM
                work_done w
                    LEFT JOIN
                boq_cells bc ON bc.boq_id = w.bill_of_qty_id
                    AND ExtractNumber(bc.cell) = ExtractNumber(w.parent)
                    LEFT JOIN
                boq_levels bl ON bl.id = bc.parent
                    LEFT JOIN
                boq_levels bl2 ON bl2.id = bl.parent AND bl.level = 3
            WHERE
                w.bill_of_qty_id = '".$spreadsheet_id ."'
                AND bl.level <> 0
                AND bl2.level <> 0
            GROUP BY w.parent
            ORDER BY w.timestamp DESC
        "));
        $workdonesummary = []; 
        foreach ($dBData as $key =>$resultdata){
            $workdonesummary[] = array($resultdata->maintitle,$resultdata->unit,$resultdata->quantity,$resultdata->rate,$resultdata->amount);
            $workdone = $workdone + ($resultdata->quantity * $resultdata->rate );
        } 
        sort($workdonesummary);
        
        $materialsummary = [];
        $dBData = DB::connection('mysql')->select( DB::raw("
             SELECT 
                mu.description material,
                SUM(mu.quantity) quantity,
                mu.unit,
                mu.price,
                FORMAT((SUM(mu.quantity) * mu.price), 2) amount,
                bc.title,
                mu.parent,
                mu.timestamp,
                DATE_FORMAT(mu.timestamp, '%W %M %e %Y') mdate,
                bl2.title maintitle
            FROM
                materials_used mu
                    LEFT JOIN
                boq_cells bc ON bc.boq_id = mu.bill_of_qty_id
                    AND ExtractNumber(bc.cell) = ExtractNumber(mu.parent)
                    LEFT JOIN
                boq_levels bl ON bl.id = bc.parent
                    LEFT JOIN
                boq_levels bl2 ON bl2.id = bl.parent AND bl.level = 3
            WHERE
                mu.bill_of_qty_id = '".$spreadsheet_id ."'
                AND bl.level <> 0
                AND bl2.level <> 0
            GROUP BY mu.description
            ORDER BY mu.timestamp DESC
        "));
        $materialsummary = []; 
        foreach ($dBData as $key =>$resultdata){
            $materialsummary[] = array($resultdata->material,$resultdata->unit,$resultdata->quantity,$resultdata->price, $resultdata->amount);
            $materials = $materials + ($resultdata->quantity * $resultdata->price );
        } 
        sort($materialsummary);  


        $laboursummary = [];
        $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT 
                l.labourer,
                sum(l.number) number,
                l.rate,
                FORMAT((sum(l.number) * l.rate), 2) amount,
                bc.title,
                l.parent,
                l.timestamp,
                DATE_FORMAT(l.timestamp, '%W %M %e %Y') ldate,
                bl2.title maintitle
            FROM
                labour l
                    LEFT JOIN
                boq_cells bc ON bc.boq_id = l.bill_of_qty_id
                    AND ExtractNumber(bc.cell) = ExtractNumber(l.parent)
                    LEFT JOIN
                boq_levels bl ON bl.id = bc.parent
                    LEFT JOIN
                boq_levels bl2 ON bl2.id = bl.parent AND bl.level = 3
            WHERE
                l.bill_of_qty_id = '".$spreadsheet_id ."'
                AND bl.level <> 0
                AND bl2.level <> 0
            GROUP BY l.labourer
            ORDER BY l.timestamp DESC
        "));
        $laboursummary = []; 
        foreach ($dBData as $key =>$resultdata){
            $laboursummary[] = array($resultdata->labourer,'',$resultdata->number,$resultdata->rate, $resultdata->amount);
            $labour = $labour + ($resultdata->number * $resultdata->rate);
        } 
        sort($laboursummary);


        $equipmentsummary = [];
        $dBData = DB::connection('mysql')->select( DB::raw("
                SELECT 
                    e.equipment,
                    sum(e.quantity) quantity,
                    e.rate,
                    FORMAT((sum(e.quantity) * e.rate), 2) amount,
                    bc.title,
                    e.parent,
                    e.timestamp,
                    DATE_FORMAT(e.timestamp, '%W %M %e %Y') edate,
                    bl2.title maintitle
                FROM
                    equipment e
                        LEFT JOIN
                    boq_cells bc ON bc.boq_id = e.bill_of_qty_id
                        AND ExtractNumber(bc.cell) = ExtractNumber(e.parent)
                        LEFT JOIN
                    boq_levels bl ON bl.id = bc.parent
                        LEFT JOIN
                    boq_levels bl2 ON bl2.id = bl.parent AND bl.level = 3
                WHERE
                    e.bill_of_qty_id = '".$spreadsheet_id ."'
                    AND bl.level <> 0
                    AND bl2.level <> 0
                GROUP BY e.id
                ORDER BY e.timestamp DESC
        "));
        $equipmentsummary = []; 
        foreach ($dBData as $key =>$resultdata){
            $equipmentsummary[] = array($resultdata->equipment,'',$resultdata->quantity,$resultdata->rate, $resultdata->amount);
            $equipment = $equipment + ($resultdata->quantity * $resultdata->rate);
        } 
        sort($equipmentsummary); 
       /*
        //work done and others queries work done
       $dBData = DB::connection('mysql')->select( DB::raw("
       SELECT 
            SUM(quantity) quantity,
            SUM(rate) rate,
            SUM(quantity * rate) amount
        FROM
            work_done
        WHERE
            bill_of_qty_id = '".$spreadsheet_id ."'
        "));
        foreach ($dBData as $key =>$resultdata){
            $workdone  = $resultdata->amount;
        } 

      //materials and others queries work done
       $dBData = DB::connection('mysql')->select( DB::raw("
       SELECT 
            SUM(quantity) quantity,
            SUM(price) price,
            SUM(quantity * price) amount
        FROM
        materials_used
        WHERE
            bill_of_qty_id = '".$spreadsheet_id ."'
        "));
        foreach ($dBData as $key =>$resultdata){
            $materials  = $resultdata->amount;
        } 

      //labour and others queries work done
       $dBData = DB::connection('mysql')->select( DB::raw("
       SELECT 
            SUM(number) quantity,
            SUM(rate) price,
            SUM(number * rate) amount
        FROM
        labour
        WHERE
            bill_of_qty_id = '".$spreadsheet_id ."'
        "));
        foreach ($dBData as $key =>$resultdata){
            $labour  = $resultdata->amount;
        } 

       //equipment and others queries work done
       $dBData = DB::connection('mysql')->select( DB::raw("
       SELECT 
            SUM(quantity) quantity,
            SUM(rate) price,
            SUM(quantity * rate) amount
        FROM
        equipment
        WHERE
            bill_of_qty_id = '".$spreadsheet_id ."'
        "));
        foreach ($dBData as $key =>$resultdata){
            $equipment  = $resultdata->amount;
        } 
*/
//var_dump($elementarysummary); exit();
        $profitloss =  $workdone - ($materials + $labour + $equipment);

        $data = [
            'workdone' => number_format($workdone,2),
            'materials' => number_format($materials,2),
            'labour' => number_format($labour,2),
            'equipment' => number_format($equipment,2),
            'profitloss' => number_format($profitloss,2),
            'workdonesummary' => $workdonesummary,
            'materialsummary' => $materialsummary,
            'laboursummary' => $laboursummary,
            'equipmentsummary' => $equipmentsummary,
            'elementarysummary' => $elementarysummary,
            'elements'=>$elements
        ];

        //return view("work-summary", $data);       
        $pdf = Pdf::loadView('work-summary', $data);

        $pdf->render();
        $output = $pdf->output();
        file_put_contents('downloads/Work-Summary.pdf', $output);
        $responseBody = array("message"=>"success","file"=>'downloads/Work-Summary.pdf');        
        return response($responseBody)->header('Content-Type', 'application/json');  
        //return $pdf->download('downloads/Work-Summary.pdf');
    }

}