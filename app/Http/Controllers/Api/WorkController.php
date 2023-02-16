<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use App\Laravue\JsonResponse;
use App\Laravue\Models\Permission;
use App\Laravue\Models\Role;
use App\Laravue\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

use App\Laravue\Models\MaterialsUsed;
use Illuminate\Support\Facades\DB;
use App\Laravue\Models\WorkDone;
use App\Laravue\Models\Labour;
use App\Laravue\Models\BOQFiles;
use App\Laravue\Models\Equipment;
use App\Laravue\Models\BOQLevels;
use App\Laravue\Models\BOQCells;


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



class WorkController extends BaseController
{

    public function generateWorkExcel(Request $request){        

        $boq = $request->id;
        $active_element = $request->active_element;
        $workdone = $materialused = $labour = $profit = $equipment = 0;

        $date = date_create($request->startdate);
        $start_date =  date_format($date, 'Y-m-d');

        $date = date_create($request->enddate);
        $end_date =  date_format($date, 'Y-m-d');

        $mySpreadsheet = new Spreadsheet();
        $mySpreadsheet->removeSheetByIndex(0);
        $worksheet_workdone = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($mySpreadsheet, "Work Done");
        $mySpreadsheet->addSheet($worksheet_workdone, 0);      
        $worksheet_material_used = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($mySpreadsheet, "Materials Used");
        $mySpreadsheet->addSheet($worksheet_material_used, 1);
        $worksheet_labour = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($mySpreadsheet, "Labour");
        $mySpreadsheet->addSheet($worksheet_labour, 2);
        $worksheet_equipment = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($mySpreadsheet, "Equipment");
        $mySpreadsheet->addSheet($worksheet_equipment, 3);

        $dBData = DB::connection('mysql')->select( DB::raw("
        SELECT 
            *
        FROM
            boq_levels
        WHERE
            parent = '".$active_element."';
        "));
        $dataheadings = []; 
        foreach ($dBData as $key =>$resultdata){
            $dataheadings[] =$resultdata->title;
        }     
        
        $workDoneData = [
            ["WorkDone"],
            ['','Date',  "Description", "Quantity done" ,"Unit","Rate","Amount"], 
        ];
        $workdoneqry = "
            SELECT 
                w.quantity,
                w.unit,
                w.rate,
                FORMAT((w.quantity * w.rate), 2) amount,
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

                    LEFT JOIN boq_levels bl ON bl.id = bc.parent
                    LEFT JOIN boq_levels bl2 ON bl2.id = bl.parent
            WHERE
                w.bill_of_qty_id = '". $boq."'
                    AND DATE(w.timestamp) >= '".$start_date."'
                    AND DATE(w.timestamp) <= '".$end_date."'
            GROUP BY w.id
            ORDER BY w.timestamp DESC
        ";
        $dBData = DB::connection('mysql')->select( DB::raw($workdoneqry));
        $data = [];  
        foreach ($dBData as $key =>$resultdata){
            if (in_array($resultdata->maintitle,$dataheadings)){
              $data[] = $resultdata->maintitle; 
            } 
            $workdone = $workdone + ($resultdata->quantity * $resultdata->rate);           
         }        
         $data = array_unique($data);         
         sort($data);

         foreach ($data as $key =>$dataheadingslabel){
            $data = array($dataheadingslabel);            
            array_push($workDoneData, $data);
            $dBData = DB::connection('mysql')->select( DB::raw( "
                SELECT 
                    w.quantity,
                    w.unit,
                    w.rate,
                    FORMAT((w.quantity * w.rate), 2) amount,
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

                        LEFT JOIN boq_levels bl ON bl.id = bc.parent
                        LEFT JOIN boq_levels bl2 ON bl2.id = bl.parent
                WHERE
                    w.bill_of_qty_id = '". $boq."'
                        AND DATE(w.timestamp) >= '".$start_date."'
                        AND DATE(w.timestamp) <= '".$end_date."'
                        AND  bl2.title = '".$dataheadingslabel."'
                GROUP BY w.id
                ORDER BY w.timestamp DESC
            "));
            $data = [];  
            foreach ($dBData as $key =>$resultdata){
               $data = array('',$resultdata->wdate,$resultdata->title,$resultdata->quantity,$resultdata->unit,$resultdata->rate,$resultdata->amount);
               array_push($workDoneData, $data);        
             }
         }


      
        $materialUsedData = [
            ["Material Used"],
            ['',"Date","Description","Material","Unit","Quantity","Price","Amount"]
        ];
        $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT
                mu.description material, 
                mu.quantity,
                mu.unit,
                mu.price,
                FORMAT((mu.quantity * mu.price), 2) amount,
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
                    
                    LEFT JOIN boq_levels bl ON bl.id = bc.parent
                    LEFT JOIN boq_levels bl2 ON bl2.id = bl.parent
            WHERE
            mu.bill_of_qty_id = '". $boq."'
                    AND DATE(mu.timestamp) >= '".$start_date."'
                    AND DATE(mu.timestamp) <= '".$end_date."'
            GROUP BY mu.id
            ORDER BY mu.timestamp DESC
        "));


        $data = [];     
        foreach ($dBData as $key =>$resultdata){
            if (in_array($resultdata->maintitle,$dataheadings)){
                $data[] = $resultdata->maintitle; 
              } 
              $materialused = $materialused + ($resultdata->quantity * $resultdata->price); 
        }
        $data = array_unique($data);         
        sort($data);

        foreach ($data as $key =>$dataheadingslabel){
            $data = array($dataheadingslabel);            
            array_push($materialUsedData, $data);

            $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT
                mu.description material, 
                mu.quantity,
                mu.unit,
                mu.price,
                FORMAT((mu.quantity * mu.price), 2) amount,
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
                    
                    LEFT JOIN boq_levels bl ON bl.id = bc.parent
                    LEFT JOIN boq_levels bl2 ON bl2.id = bl.parent
            WHERE
            mu.bill_of_qty_id = '". $boq."'
                    AND DATE(mu.timestamp) >= '".$start_date."'
                    AND DATE(mu.timestamp) <= '".$end_date."'
                    AND  bl2.title = '".$dataheadingslabel."'
            GROUP BY mu.id
            ORDER BY mu.timestamp DESC
        "));

        $data = [];  
        foreach ($dBData as $key =>$resultdata){
            $data = array('',$resultdata->mdate,$resultdata->title,$resultdata->material,$resultdata->quantity,$resultdata->unit,$resultdata->price,$resultdata->amount);
            array_push($materialUsedData, $data);
        }
      }

        

        $labourData = [
            ["Labour"],
            ['',"Date","Description","Labourer","Number","Rate/day","Amount"]
        ];
        $dBData = DB::connection('mysql')->select( DB::raw("
        SELECT 
                l.labourer,
                l.number,
                l.rate,
                FORMAT((l.number * l.rate), 2) amount,
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

                    LEFT JOIN boq_levels bl ON bl.id = bc.parent
                    LEFT JOIN boq_levels bl2 ON bl2.id = bl.parent
            WHERE
            l.bill_of_qty_id = '". $boq."'
                    AND DATE(l.timestamp) >= '".$start_date."'
                    AND DATE(l.timestamp) <= '".$end_date."'
            GROUP BY l.id
            ORDER BY l.timestamp DESC
        "));

        $data = [];     
        foreach ($dBData as $key =>$resultdata){
            if (in_array($resultdata->maintitle,$dataheadings)){
                $data[] = $resultdata->maintitle; 
              } 
              $labour = $labour + ($resultdata->number * $resultdata->rate); 

        }

        $data = array_unique($data);         
        sort($data);
        foreach ($data as $key =>$dataheadingslabel){
            $data = array($dataheadingslabel);            
            array_push($labourData, $data);

            $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT 
                    l.labourer,
                    l.number,
                    l.rate,
                    FORMAT((l.number * l.rate), 2) amount,
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
    
                        LEFT JOIN boq_levels bl ON bl.id = bc.parent
                        LEFT JOIN boq_levels bl2 ON bl2.id = bl.parent
                WHERE
                l.bill_of_qty_id = '". $boq."'
                        AND DATE(l.timestamp) >= '".$start_date."'
                        AND DATE(l.timestamp) <= '".$end_date."'
                        AND  bl2.title = '".$dataheadingslabel."'
                GROUP BY l.id
                ORDER BY l.timestamp DESC
            "));
            $data = [];  
            foreach ($dBData as $key =>$resultdata){
                    $data = array('',$resultdata->ldate,$resultdata->title,$resultdata->labourer,$resultdata->number,$resultdata->rate,$resultdata->amount);
                    array_push($labourData, $data);
            }

        }

        $equipmentData = [
            ["Equipment"],
            ['',"Date","Description","Equipment","Unit","Rate","Amount"]
        ];
        $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT 
                e.equipment,
                e.quantity,
                e.rate,
                FORMAT((e.quantity * e.rate), 2) amount,
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

                    LEFT JOIN boq_levels bl ON bl.id = bc.parent
                    LEFT JOIN boq_levels bl2 ON bl2.id = bl.parent
            WHERE
            e.bill_of_qty_id = '". $boq."'
                    AND DATE(e.timestamp) >= '".$start_date."'
                    AND DATE(e.timestamp) <= '".$end_date."'
            GROUP BY e.id
            ORDER BY e.timestamp DESC
        "));
        $data = [];     
        foreach ($dBData as $key =>$resultdata){
            if (in_array($resultdata->maintitle,$dataheadings)){
                $data[] = $resultdata->maintitle; 
              }
              $equipment = $equipment + ($resultdata->quantity * $resultdata->rate);  
        }
        $data = array_unique($data);         
        sort($data);

        foreach ($data as $key =>$dataheadingslabel){
           $data = array($dataheadingslabel);            
           array_push($equipmentData, $data);

                $dBData = DB::connection('mysql')->select( DB::raw("
                SELECT 
                    e.equipment,
                    e.quantity,
                    e.rate,
                    FORMAT((e.quantity * e.rate), 2) amount,
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

                        LEFT JOIN boq_levels bl ON bl.id = bc.parent
                        LEFT JOIN boq_levels bl2 ON bl2.id = bl.parent
                WHERE
                e.bill_of_qty_id = '". $boq."'
                        AND DATE(e.timestamp) >= '".$start_date."'
                        AND DATE(e.timestamp) <= '".$end_date."'
                        AND  bl2.title = '".$dataheadingslabel."'
                GROUP BY e.id
                ORDER BY e.timestamp DESC
            "));
            $data = [];  
            foreach ($dBData as $key =>$resultdata){
               $data = array('',$resultdata->edate,$resultdata->title,$resultdata->equipment,$resultdata->quantity,$resultdata->rate,$resultdata->amount);
               array_push($equipmentData, $data);
            }
        }

        $worksheet_workdone->fromArray($workDoneData);
        $worksheet_material_used->fromArray($materialUsedData);
        $worksheet_labour->fromArray($labourData);
        $worksheet_equipment->fromArray($equipmentData);

  

        $worksheet_workdone->setCellValue('H3', 'Summary');
        $worksheet_workdone->getStyle('H3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $worksheet_workdone->getStyle('H3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_workdone->getStyle('H3')->getFill()->getStartColor()->setARGB('008000');

        $worksheet_workdone->getStyle('I3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $worksheet_workdone->getStyle('I3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_workdone->getStyle('I3')->getFill()->getStartColor()->setARGB('008000');

        $worksheet_workdone->setCellValue('H4', 'Workdone');
        $worksheet_workdone->getStyle('H4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_workdone->getStyle('H4')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_workdone->setCellValue('I4',number_format($workdone,2));

        $worksheet_workdone->setCellValue('H5', 'Material Used');
        $worksheet_workdone->getStyle('H5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_workdone->getStyle('H5')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_workdone->setCellValue('I5',number_format($materialused,2));

        $worksheet_workdone->setCellValue('H6', 'Labour');
        $worksheet_workdone->getStyle('H6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_workdone->getStyle('H6')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_workdone->setCellValue('I6',number_format($labour,2));

        $worksheet_workdone->setCellValue('H7', 'Equipment');
        $worksheet_workdone->getStyle('H7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_workdone->getStyle('H7')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_workdone->setCellValue('I7',number_format($equipment,2));
        
        $profit =  $workdone - ($materialused + $labour + $equipment);
        $worksheet_workdone->setCellValue('H8', 'Profit / Loss');
        $worksheet_workdone->getStyle('H8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_workdone->getStyle('H8')->getFill()->getStartColor()->setARGB('7FFF00');
   
        $worksheet_workdone->setCellValue('I8',number_format($profit,2));
        if($profit < 0){
            $worksheet_workdone->getStyle('I8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $worksheet_workdone->getStyle('I8')->getFill()->getStartColor()->setARGB('ff0202');

            $worksheet_material_used->getStyle('J8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $worksheet_material_used->getStyle('J8')->getFill()->getStartColor()->setARGB('ff0202');

            $worksheet_labour->getStyle('I8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $worksheet_labour->getStyle('I8')->getFill()->getStartColor()->setARGB('ff0202');

            $worksheet_equipment->getStyle('I8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $worksheet_equipment->getStyle('I8')->getFill()->getStartColor()->setARGB('ff0202');
        }else{
            $worksheet_workdone->getStyle('I8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $worksheet_workdone->getStyle('I8')->getFill()->getStartColor()->setARGB('7FFF00');
            
            $worksheet_material_used->getStyle('J8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $worksheet_material_used->getStyle('J8')->getFill()->getStartColor()->setARGB('7FFF00'); 

            $worksheet_labour->getStyle('I8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $worksheet_labour->getStyle('I8')->getFill()->getStartColor()->setARGB('7FFF00'); 

            $worksheet_equipment->getStyle('I8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $worksheet_equipment->getStyle('I8')->getFill()->getStartColor()->setARGB('7FFF00'); 
        }
        ///////////////
        $worksheet_material_used->setCellValue('I3', 'Summary');
        $worksheet_material_used->getStyle('I3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $worksheet_material_used->getStyle('I3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_material_used->getStyle('I3')->getFill()->getStartColor()->setARGB('008000');

        $worksheet_material_used->getStyle('J3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $worksheet_material_used->getStyle('J3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_material_used->getStyle('J3')->getFill()->getStartColor()->setARGB('008000');

        $worksheet_material_used->setCellValue('I4', 'Workdone');
        $worksheet_material_used->getStyle('I4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_material_used->getStyle('I4')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_material_used->setCellValue('J4',number_format($workdone,2));

        $worksheet_material_used->setCellValue('I5', 'Material Used');
        $worksheet_material_used->getStyle('I5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_material_used->getStyle('I5')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_material_used->setCellValue('J5',number_format($materialused,2));

        $worksheet_material_used->setCellValue('I6', 'Labour');
        $worksheet_material_used->getStyle('I6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_material_used->getStyle('I6')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_material_used->setCellValue('J6',number_format($labour,2));

        $worksheet_material_used->setCellValue('I7', 'Equipment');
        $worksheet_material_used->getStyle('I7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_material_used->getStyle('I7')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_material_used->setCellValue('J7',number_format($equipment,2));

        $worksheet_material_used->setCellValue('I8', 'Profit / Loss');
        $worksheet_material_used->getStyle('I8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_material_used->getStyle('I8')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_material_used->setCellValue('J8',number_format($profit,2));
        /////////////////////
        $worksheet_labour->setCellValue('H3', 'Summary');
        $worksheet_labour->getStyle('H3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $worksheet_labour->getStyle('H3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_labour->getStyle('H3')->getFill()->getStartColor()->setARGB('008000');

        $worksheet_labour->getStyle('I3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $worksheet_labour->getStyle('I3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_labour->getStyle('I3')->getFill()->getStartColor()->setARGB('008000');

        $worksheet_labour->setCellValue('H4', 'Workdone');
        $worksheet_labour->getStyle('H4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_labour->getStyle('H4')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_labour->setCellValue('I4',number_format($workdone,2));

        $worksheet_labour->setCellValue('H5', 'Material Used');
        $worksheet_labour->getStyle('H5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_labour->getStyle('H5')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_labour->setCellValue('I5',number_format($materialused,2));

        $worksheet_labour->setCellValue('H6', 'Labour');
        $worksheet_labour->getStyle('H6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_labour->getStyle('H6')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_labour->setCellValue('I6',number_format($labour,2));

        $worksheet_labour->setCellValue('H7', 'Equipment');
        $worksheet_labour->getStyle('H7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_labour->getStyle('H7')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_labour->setCellValue('I7',number_format($equipment,2));

        $worksheet_labour->setCellValue('H8', 'Profit / Loss');
        $worksheet_labour->getStyle('H8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_labour->getStyle('H8')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_labour->setCellValue('I8',number_format($profit,2));
        ////////////////////
        $worksheet_equipment->setCellValue('H3', 'Summary');
        $worksheet_equipment->getStyle('H3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $worksheet_equipment->getStyle('H3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_equipment->getStyle('H3')->getFill()->getStartColor()->setARGB('008000');

        $worksheet_equipment->getStyle('I3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $worksheet_equipment->getStyle('I3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_equipment->getStyle('I3')->getFill()->getStartColor()->setARGB('008000');

        $worksheet_equipment->setCellValue('H4', 'Workdone');
        $worksheet_equipment->getStyle('H4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_equipment->getStyle('H4')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_equipment->setCellValue('I4',number_format($workdone,2));

        $worksheet_equipment->setCellValue('H5', 'Material Used');
        $worksheet_equipment->getStyle('H5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_equipment->getStyle('H5')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_equipment->setCellValue('I5',number_format($materialused,2));

        $worksheet_equipment->setCellValue('H6', 'Labour');
        $worksheet_equipment->getStyle('H6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_equipment->getStyle('H6')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_equipment->setCellValue('I6',number_format($labour,2));

        $worksheet_equipment->setCellValue('H7', 'Equipment');
        $worksheet_equipment->getStyle('H7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_equipment->getStyle('H7')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_equipment->setCellValue('I7',number_format($equipment,2));

        $worksheet_equipment->setCellValue('H8', 'Profit / Loss');
        $worksheet_equipment->getStyle('H8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $worksheet_equipment->getStyle('H8')->getFill()->getStartColor()->setARGB('7FFF00');
        $worksheet_equipment->setCellValue('I8',number_format($profit,2));
        ///////////////////        

        $worksheets = [$worksheet_workdone, $worksheet_material_used, $worksheet_labour, $worksheet_equipment];
        foreach ($worksheets as $worksheet)
        {
            foreach ($worksheet->getColumnIterator() as $column)
            {
                $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }
        $message = "Successfully exported the excel!";
        $file = 'downloads\output.xlsx';        
        $writer = new Xlsx($mySpreadsheet);
        $writer->save($file);       
        $responseBody = array("message"=>$message,"file"=>$file);        
        return response($responseBody)->header('Content-Type', 'application/json');  

    }



}
