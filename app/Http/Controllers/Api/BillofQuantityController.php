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

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;



class BillofQuantityController extends BaseController
{

    /*public function extractLevels($boq_id,$finalFile){
        $boqid_level_1 =  $boqid_level_2 =  $boqid_level_3 = "";
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($finalFile);
        $worksheet = $spreadsheet->getActiveSheet();
        $rowcount = 2;
        foreach ($worksheet->getRowIterator() as $row) {
           $level1 = $worksheet->getCell('C'.$rowcount)->getFormattedValue();
           $index = $worksheet->getCell('A'.$rowcount)->getCalculatedValue(); 
            if(mb_strtoupper($level1, 'utf-8') == $level1 && $level1 != ""){ 
                $boqid_level_1 =  BOQLevels::create([
                    'title' => $level1,
                    'parent' =>$boq_id,
                    'level'=>1,
                    'boq_id' =>$boq_id,
                    'created_at' =>date('Y-m-d H:i:s')
                ])->id;               
                $explodespace = explode(' ',$level1);   
                if (count($explodespace) <= 1 && $level1 != ""){
                    $level2 = $explodespace[0];
                    $boqid_level_2 =  BOQLevels::create([
                        'title' => $level2,
                        'parent' =>$boqid_level_1,
                        'level'=>2,
                        'boq_id' =>$boq_id,
                        'created_at' =>date('Y-m-d H:i:s')
                    ])->id;   
                }
            }
            if($level1 != ""){
                if((mb_strtoupper($level1, 'utf-8') != $level1) &&  $index == ""){
                    $level3 = $level1;
                    $boqid_level_2 =  BOQLevels::create([
                        'title' => $level3,
                        'parent' =>$boqid_level_2,
                        'level'=>3,
                        'boq_id' =>$boq_id,
                        'created_at' =>date('Y-m-d H:i:s')
                    ])->id;    
                } 
            }
     
            $rowcount++;
        }
    }*/
    public function insertCells($boq_id,$parent,$cell,$title,$unit,$quantity,$rate){
        $boq_cell =  BOQCells::create([
            'cell' => $cell,
            'parent' =>$parent,     
            'boq_id' =>$boq_id,
            'title' =>$title,
            'unit' =>$unit,
            'quantity' =>$quantity,
            'rate' =>$rate,
            'created_at' =>date('Y-m-d H:i:s')
        ])->id; 
      return $boq_cell;       
    }
    public function extractLevels($boq_id,$finalFile){
        $rowcount = 4;
        $boqid_level_1 =  $boqid_level_2 =  $boqid_level_3 = "";
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($finalFile);
        $worksheet = $spreadsheet->getActiveSheet();
        foreach ($worksheet->getRowIterator() as $row) {
            $code = $worksheet->getCell('B'.$rowcount)->getCalculatedValue();
            $description = $worksheet->getCell('C'.$rowcount)->getFormattedValue();

            $unit = $worksheet->getCell('D'.$rowcount)->getFormattedValue();
            $quantity = $worksheet->getCell('E'.$rowcount)->getFormattedValue();
            $rate = $worksheet->getCell('F'.$rowcount)->getFormattedValue();

            if($code != ""){
            $arraycode = explode(".",$code);
            if(count($arraycode) > 0){
                if(count($arraycode) == 1){
                    $boqid_level_1 =  BOQLevels::create([
                        'title' => $code." ".$description,
                        'parent' =>$boq_id,
                        'level'=>1,
                        'cell'=>'G'.$rowcount,
                        'boq_id' =>$boq_id,
                        'created_at' =>date('Y-m-d H:i:s')
                    ])->id;
                  $this->insertCells($boq_id,$boq_id,'G'.$rowcount,$description,$unit,$quantity,$rate);  
                }
                if(count($arraycode) == 2){
                    $boqid_level_2 =  BOQLevels::create([
                        'title' => $code." ".$description,
                        'parent' =>$boqid_level_1,
                        'level'=>2,
                        'cell'=>'G'.$rowcount,
                        'boq_id' =>$boq_id,
                        'created_at' =>date('Y-m-d H:i:s')
                    ])->id;
                    //insert children cells
                    $this->insertCells($boq_id,$boqid_level_1,'G'.$rowcount,$description,$unit,$quantity,$rate);  
                }
                if(count($arraycode) == 3){
                    $boqid_level_3 =  BOQLevels::create([
                        'title' => $code." ".$description,
                        'parent' =>$boqid_level_1,//boqid_level_2
                        'level'=>3,
                        'cell'=>'G'.$rowcount,
                        'boq_id' =>$boq_id,
                        'created_at' =>date('Y-m-d H:i:s')
                    ])->id; 
                    $this->insertCells($boq_id,$boqid_level_1,'G'.$rowcount,$description,$unit,$quantity,$rate); 
                    $this->insertCells($boq_id,$boqid_level_2,'G'.$rowcount,$description,$unit,$quantity,$rate);
                    $this->insertCells($boq_id,$boqid_level_3,'G'.$rowcount,$description,$unit,$quantity,$rate);                     
                }
                //var_dump(count($arraycode)); echo  $level1 ;
            }
        }
            //echo "<br>";echo "<hr>";
            $rowcount++;
        }        
        //exit();
    }
    
    public function upload(Request $request)
    {
     if ( !file_exists( 'uploads/bill-of-quantities/' ) ) {
            mkdir( 'uploads/bill-of-quantities/', 0777, true);
        }
        $response = "Error!";
        $finalFile ="";
        $billofquantities = 'uploads/bill-of-quantities/';
        if (!empty($_FILES)) {
            
            $temp = $_FILES['file']['tmp_name'];
            $target =  $billofquantities;             
            $finalFile =  $target . $_FILES['file']['name'];    //var_dump($finalFile); exit();      
            move_uploaded_file( $temp, $finalFile );
            //insert into database
            $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT 
                * FROM  boq_files WHERE filename = '".$_FILES['file']['name']."'
                ;
            ")); 
            if(count($dBData)==0){
                $id =  BOQFiles::create([
                    'filename' => $_FILES['file']['name'],
                    'filepath' =>$finalFile,
                    'created_at' =>date('Y-m-d H:i:s')
                ])->id;

                $boqid =  BOQLevels::create([
                    'title' => $_FILES['file']['name'],
                    'parent' =>0,
                    'level'=>0,
                    'boq_id' =>$id,
                    'created_at' =>date('Y-m-d H:i:s')
                ])->id;

                $this->extractLevels($boqid,$finalFile);

            } 
            $response = "Successfully saved!";           
        } 
        //$finalFile = "uploads/bill-of-quantities/1 - POTTERHOUSE SCHOOL - BUILDERS WORK -EXCEL.xlsx";
        //$finalFile = "uploads/bill-of-quantities/Excel BQ.xlsx";
        //$this->extractLevels(6569,$finalFile);
        echo json_encode(array("response"=>$response,"file"=>$finalFile));
    }
    public function materialsUsed(Request $request)
    {
        $date = date_create($request->selected_date);
        $selected_date = date_format($date, 'Y-m-d');

        $rowsNumber = 10;
        $data = [];
        $dBData = DB::connection('mysql')->select( DB::raw("
        SELECT 
            * FROM  materials_used WHERE parent = '".$request->selected_cell."' AND bill_of_qty_id = '".$request->selected_spreadsheet."' AND DATE(timestamp) = '".$selected_date."'
            ;
        "));      
        foreach ($dBData as $key =>$resultdata){
            $date = date_create($resultdata->timestamp);
                    $row = [
                        'id' => $resultdata->id,
                        'description' => $resultdata->description,
                        'unit' => $resultdata->unit,
                        'price' => $resultdata->price,
                        'amount' => number_format(($resultdata->price * $resultdata->quantity),2),
                        'quantity' => $resultdata->quantity,
                        'timestamp' => date_format($date, 'Y-m-d H:i:s'),
                        'bill_of_qty_id' => $resultdata->bill_of_qty_id,
                        'updated_at' => $resultdata->updated_at,
                        'created_at' => $resultdata->created_at   
                    ];
                    $data[] = $row;
        }           
        return response()->json(new JsonResponse(['items' => $data, 'total' =>count($data)]));

    }
    public function materialsUsedCreate(Request $request){
        $issuccess = false;
        $message = "";
        $date = date_create($request->timestamp);  
        $id = MaterialsUsed::create([
            'unit' => $request->unit,
            'quantity' =>$request->quantity,
            'price' =>$request->price,
            'created_at' =>date('Y-m-d H:i:s'),
            'timestamp' =>date_format($date, 'Y-m-d H:i:s'),                  
            'description' =>$request->description,
            'parent'=>$request->selected_cell,
            'bill_of_qty_id' => $request->selected_spreadsheet
        ])->id;
        $issuccess = true;
        $message = "Successfully added a material!";
        $responseBody = array("message"=>$message,"success"=>$issuccess,"id"=>$id);        
        return response($responseBody)->header('Content-Type', 'application/json');
    }

    public function materialsUsedUpdate(Request $request){
        $issuccess = false;
        $message = "";
        $dbData = MaterialsUsed::find($request->id);
        $dbData->update(array(
                'unit' => $request->unit,
                'quantity' => $request->quantity,
                'price' =>$request->price,                
                'description' =>$request->description,//'timestamp' =>$request->timestamp,
                'updated_at' =>date('Y-m-d H:i:s')
            ));
        $issuccess = true;
        $message = "Successfully updated a material!";
        $responseBody = array("message"=>$message,"success"=>$issuccess);        
        return response($responseBody)->header('Content-Type', 'application/json'); 
    }

    public function materialsUsedDelete(Request $request){
        $issuccess = false;
        $message = "";
        $bool= "success";
        $dbData = MaterialsUsed::find($request->id);
        if( $dbData ){
            $issuccess = $dbData->delete();
            $message = "Successfully deleted material!";          
        }else{
           $message= "Record does not exist.Please refresh!";
           $bool= "error";
        }    
        $responseBody = array("message"=>$message,"success"=>$issuccess,"bool"=>$bool);    
        return response($responseBody)->header('Content-Type', 'application/json');  
    }


////////////////////////////////work done 
    public function workDone(Request $request)
    {
        $date = date_create($request->selected_date);
        $selected_date = date_format($date, 'Y-m-d');

        $rowsNumber = 10;
        $data = [];
        $dBData = DB::connection('mysql')->select( DB::raw("
        SELECT 
            * FROM  work_done WHERE parent = '".$request->selected_cell."' AND bill_of_qty_id = '".$request->selected_spreadsheet."' AND DATE(timestamp) = '".$selected_date."' 
            ;
        "));     
        foreach ($dBData as $key =>$resultdata){
            $date = date_create($resultdata->timestamp);
                    $row = [
                        'id' => $resultdata->id,                  
                        'unit' => $resultdata->unit,
                        'done' => $resultdata->done,
                        'quantity' => $resultdata->quantity,
                        'rate' => $resultdata->rate,
                        'amount' => number_format(($resultdata->quantity * $resultdata->rate),2),
                        'timestamp' => date_format($date, 'Y-m-d H:i:s'),
                        'bill_of_qty_id' => $resultdata->bill_of_qty_id,
                        'updated_at' => $resultdata->updated_at,
                        'created_at' => $resultdata->created_at   
                    ];
                    $data[] = $row;
        }           
        return response()->json(new JsonResponse(['items' => $data, 'total' =>count($data)]));

    }
    public function workDoneCreate(Request $request){
        $issuccess = false;
        $message = "";
        $date = date_create($request->timestamp);  
        $id = WorkDone::create([
            'unit' => $request->unit,
            'done' => $request->done,
            'rate' => $request->rate,
            'bill_of_qty_id' => $request->selected_spreadsheet,
            'quantity' =>$request->quantity,
            'created_at' =>date('Y-m-d H:i:s'),
            'timestamp' =>date_format($date, 'Y-m-d H:i:s'),
            'parent'=>$request->selected_cell
        ])->id;
        $issuccess = true;
        $message = "Successfully added a work done entry!";
        $responseBody = array("message"=>$message,"success"=>$issuccess,"id"=>$id);        
        return response($responseBody)->header('Content-Type', 'application/json');
    }

    public function workDoneUpdate(Request $request){
        $issuccess = false;
        $message = "";
        $dbData = WorkDone::find($request->id);
        $dbData->update(array(
                'unit' => $request->unit,
                'rate' => $request->rate,
                'done' => $request->done,
                'quantity' => $request->quantity,                             
                'updated_at' =>date('Y-m-d H:i:s')// 'timestamp' =>$request->timestamp,  
            ));
        $issuccess = true;
        $message = "Successfully updated a work done!";
        $responseBody = array("message"=>$message,"success"=>$issuccess);        
        return response($responseBody)->header('Content-Type', 'application/json');  

    }

    public function workDoneDelete(Request $request){
        $issuccess = false;
        $message = "";
        $bool= "success";
        $dbData = WorkDone::find($request->id);
        if( $dbData ){
            $issuccess = $dbData->delete();
            $message = "Successfully deleted workdone!";          
        }else{
           $message= "Record does not exist.Please refresh!";
           $bool= "error";
        }    
        $responseBody = array("message"=>$message,"success"=>$issuccess,"bool"=>$bool);    
        return response($responseBody)->header('Content-Type', 'application/json');  
    }


    ////////////////////////////////labour
    public function labour(Request $request)
    {
        $date = date_create($request->selected_date);
        $selected_date = date_format($date, 'Y-m-d');
        $rowsNumber = 10;
        $data = [];
        $dBData = DB::connection('mysql')->select( DB::raw("
        SELECT 
            * FROM  labour WHERE parent = '".$request->selected_cell."' AND bill_of_qty_id = '".$request->selected_spreadsheet."' AND DATE(timestamp) = '".$selected_date."'  
            ;
        "));     
        foreach ($dBData as $key =>$resultdata){
            $date = date_create($resultdata->timestamp);
                    $row = [
                        'id' => $resultdata->id,                  
                        'labourer' => $resultdata->labourer,
                        'number' => $resultdata->number,
                        'rate' => $resultdata->rate,
                        'amount' => number_format(($resultdata->number * $resultdata->rate),2),
                        'timestamp' => date_format($date, 'Y-m-d H:i:s'),
                        'bill_of_qty_id' => $resultdata->bill_of_qty_id,
                        'updated_at' => $resultdata->updated_at,
                        'created_at' => $resultdata->created_at   
                    ];
                    $data[] = $row;
        }           
        return response()->json(new JsonResponse(['items' => $data, 'total' =>count($data)]));

    }
    public function labourCreate(Request $request){
        $issuccess = false;
        $message = "";  
        $date = date_create($request->timestamp);    
        $id = Labour::create([
            'labourer' => $request->labourer,
            'number' =>$request->number,
            'rate' => $request->rate, 
            'created_at' =>date('Y-m-d H:i:s'),
            'timestamp' =>date_format($date, 'Y-m-d H:i:s'),
            'parent'=>$request->selected_cell,
            'bill_of_qty_id' => $request->selected_spreadsheet
        ])->id;
        $issuccess = true;
        $message = "Successfully added a labour entry!";
        $responseBody = array("message"=>$message,"success"=>$issuccess,"id"=>$id);        
        return response($responseBody)->header('Content-Type', 'application/json');
    }

    public function labourUpdate(Request $request){
        $issuccess = false;
        $message = "";   

        $dbData = Labour::find($request->id);
        $dbData->update(array(
                'labourer' => $request->labourer,
                'number' => $request->number,
                'rate' => $request->rate,                 
                'updated_at' =>date('Y-m-d H:i:s') // 'timestamp' =>$request->timestamp, 
            ));
        $issuccess = true;
        $message = "Successfully updated a labour!";
        $responseBody = array("message"=>$message,"success"=>$issuccess);        
        return response($responseBody)->header('Content-Type', 'application/json');  

    }

    public function labourDelete(Request $request){
        $issuccess = false;
        $message = "";
        $bool= "success";
        $dbData = Labour::find($request->id);
        if( $dbData ){
            $issuccess = $dbData->delete();
            $message = "Successfully deleted material!";          
        }else{
           $message= "Record does not exist.Please refresh!";
           $bool= "error";
        }    
        $responseBody = array("message"=>$message,"success"=>$issuccess,"bool"=>$bool);    
        return response($responseBody)->header('Content-Type', 'application/json');  
    }

    public function fetchQuantityWorkDone(Request $request){      
        /*$dBData = DB::connection('mysql')->select( DB::raw("
            SELECT 
            SUM(mu.quantity * mu.price) + (SELECT 
                    SUM(eq.quantity * eq.rate)
                FROM
                    equipment eq
                WHERE
                    eq.parent = mu.parent AND eq.bill_of_qty_id = '".$request->spreadsheet_id."') + (SELECT 
                    SUM(l.number * l.rate)
                FROM
                    labour l
                WHERE
                    l.parent = mu.parent AND l.bill_of_qty_id = '".$request->spreadsheet_id."') amount,
            mu.parent
        FROM
            materials_used mu WHERE mu.bill_of_qty_id = '".$request->spreadsheet_id."'
        GROUP BY mu.parent
        "));*/
        $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT 
                bl.id,
                SUM(mu.quantity * mu.price) material,
                SUM(eq.quantity * eq.rate) equipment,
                SUM(l.number * l.rate) labour,
                (COALESCE(SUM(mu.quantity * mu.price), 0) + COALESCE(SUM(eq.quantity * eq.rate), 0) + COALESCE(SUM(l.number * l.rate), 0)) amount,
                mu.parent
            FROM
                boq_files bf
                    JOIN
                boq_levels bl ON bf.id = bl.boq_id AND bl.parent = 0
                    JOIN
                materials_used mu ON mu.bill_of_qty_id = bl.id
                    LEFT JOIN
                equipment eq ON eq.bill_of_qty_id = bl.id
                    AND mu.parent = eq.parent
                    LEFT JOIN
                labour l ON l.bill_of_qty_id = bl.id
                    AND mu.parent = l.parent
            WHERE
                bl.id = '".$request->spreadsheet_id."'
            GROUP BY mu.parent
            "));
        $costofwork = [];
        foreach ($dBData as $key =>$resultdata){
            $costofwork[] = array("cell"=>$resultdata->parent,"qty"=>$resultdata->amount);
        } 
        $dBData = DB::connection('mysql')->select( DB::raw("SELECT sum(quantity) qty,parent,rate FROM work_done WHERE bill_of_qty_id = '".$request->spreadsheet_id."' AND parent='".$request->cell."'  GROUP BY parent"));        
        $quantitydone = [];
        foreach ($dBData as $key =>$resultdata){
            $quantitydone[] = array("cell"=>$resultdata->parent,"qty"=>$resultdata->qty,"rate"=>$resultdata->rate);
        }
        $responseBody = array("quantitydone"=>$quantitydone,"costofwork"=>$costofwork);        
        return response($responseBody)->header('Content-Type', 'application/json'); 
    }


        ////////////////////////////////equipment
        public function equipment(Request $request)
        {  
            $date = date_create($request->selected_date);
            $selected_date = date_format($date, 'Y-m-d');         
            $data = [];
            $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT 
                * FROM  equipment WHERE parent = '".$request->selected_cell."' AND bill_of_qty_id = '".$request->selected_spreadsheet."' AND DATE(timestamp) = '".$selected_date."'  
                ;
            "));     
            foreach ($dBData as $key =>$resultdata){ 
                $date = date_create($resultdata->timestamp);             
                        $row = [
                            'id' => $resultdata->id,                  
                            'equipment' => $resultdata->equipment,
                            'quantity' => $resultdata->quantity,
                            'rate' => $resultdata->rate,
                            'amount' => number_format($resultdata->rate * $resultdata->quantity,2),
                            'timestamp' => date_format($date, 'Y-m-d H:i:s'),
                            'updated_at' => $resultdata->updated_at,
                            'created_at' => $resultdata->created_at   
                        ];
                        $data[] = $row;
            }           
            return response()->json(new JsonResponse(['items' => $data, 'total' =>count($data)]));
    
        }
        public function equipmentCreate(Request $request){
            $issuccess = false;
            $message = "";  
            $date = date_create($request->timestamp);    
            $id = Equipment::create([
                'equipment' => $request->equipment,
                'quantity' =>$request->quantity,
                'rate' =>$request->rate,
                'created_at' =>date('Y-m-d H:i:s'),
                'timestamp' =>date_format($date, 'Y-m-d H:i:s'),
                'parent'=>$request->selected_cell,
                'bill_of_qty_id' => $request->selected_spreadsheet
            ])->id;
            $issuccess = true;
            $message = "Successfully added a equipment entry!";
            $responseBody = array("message"=>$message,"success"=>$issuccess,"id"=>$id);        
            return response($responseBody)->header('Content-Type', 'application/json');
        }
    
        public function equipmentUpdate(Request $request){
            $issuccess = false;
            $message = "";   
    
            $dbData = Equipment::find($request->id);
            $dbData->update(array(
                    'equipment' => $request->equipment,
                    'quantity' => $request->quantity,
                    'rate' => $request->rate,                 
                    'updated_at' =>date('Y-m-d H:i:s') // 'timestamp' =>$request->timestamp, 
                ));
            $issuccess = true;
            $message = "Successfully updated a equipment!";
            $responseBody = array("message"=>$message,"success"=>$issuccess);        
            return response($responseBody)->header('Content-Type', 'application/json');      
        }

        public function equipmentDelete(Request $request){
            $issuccess = false;
            $message = "";
            $bool= "success";
            $dbData = Equipment::find($request->id);
            if( $dbData ){
                $issuccess = $dbData->delete();
                $message = "Successfully deleted material!";          
            }else{
               $message= "Record does not exist.Please refresh!";
               $bool= "error";
            }    
            $responseBody = array("message"=>$message,"success"=>$issuccess,"bool"=>$bool);    
            return response($responseBody)->header('Content-Type', 'application/json');  
        }

        public function spreadsheetList(Request $request){
            $data = [];
            $dBData = DB::connection('mysql')->select( DB::raw("
             SELECT * FROM boq_levels WHERE level = 0"));     
            foreach ($dBData as $key =>$resultdata){              
                        $row = [
                            'id' => $resultdata->id,                  
                            'title' => $resultdata->title,
                            'parent' => $resultdata->parent,
                            'boq_id' => $resultdata->boq_id,
                            'level' => $resultdata->level,
                            'updated_at' => $resultdata->updated_at,
                            'created_at' => $resultdata->created_at   
                        ];
                        $data[] = $row;
            }           
            return response()->json(new JsonResponse(['items' => $data, 'total' =>count($data)]));
        }
        
        public function fetchWork(Request $request){
            $data = [];
            $dBData = DB::connection('mysql')->select( DB::raw("
             SELECT * FROM boq_levels WHERE level = 0"));     
            foreach ($dBData as $key =>$resultdata){              
                        $row = [
                            'id' => $resultdata->id,                  
                            'title' => $resultdata->title,
                            'parent' => $resultdata->parent,
                            'boq_id' => $resultdata->boq_id,
                            'level' => $resultdata->level,
                            'updated_at' => $resultdata->updated_at,
                            'created_at' => $resultdata->created_at   
                        ];
                        $data[] = $row;
            }           
            return response()->json(new JsonResponse(['items' => $data, 'total' =>count($data)]));
        }
        public function levelOneTotals($paretn_id){
          $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT 
                FORMAT(IFNULL(SUM(bc.quantity * bc.rate), 0),
                2) amount,
                FORMAT(IFNULL(SUM(bc.quantity_done), 0),
                    2) quantity_done,
                FORMAT(IFNULL(SUM(bc.cost_of_work), 0),
                    2) cost_of_work,
                FORMAT(IFNULL(SUM(bc.value_of_work), 0),
                    2) value_of_work,
                FORMAT(IFNULL(SUM(bc.profit_loss), 0),
                    2) profit_loss
            FROM
                boq_cells bc
            WHERE
                parent = '".$paretn_id."';
            "));
            $totalSummation = array("amount"=>0,"quantity_done"=>0,"cost_of_work"=>0,"profit_loss"=>0);
            if(count($dBData)>0){             
                $totalSummation = array("amount"=>$dBData[0]->amount,"quantity_done"=>$dBData[0]->quantity_done,"value_of_work"=>$dBData[0]->value_of_work,"cost_of_work"=>$dBData[0]->cost_of_work,"profit_loss"=>$dBData[0]->profit_loss);
            }
          return $totalSummation;
        }

        public function spreadsheetLevelOne(Request $request){
            $data = [];

            $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT 
                bl.id,
                bl.title,
                bl.parent,
                bl.boq_id,
                bl.cell,
                bl.level,
                EXTRACTNUMBER(bl.cell) rownumber,
                bl.created_at,
                bl.updated_at,
                bc.unit,
                bc.quantity,
                bc.rate,              
                bc.id bc_id,
                bc.quantity_done,
                bc.cost_of_work,
                bc.value_of_work,
                bc.profit_loss
            FROM
                boq_levels bl
                    LEFT JOIN
                boq_cells bc ON bc.parent = bl.parent
                    AND bc.cell = bl.cell
            WHERE
                bl.boq_id = '".$request->selected."'"));              
            $filteredrows = [];
            foreach ($dBData as $key =>$resultdata){    
                $index = preg_replace("/[^0-9\.]/", '', trim($resultdata->title));
                $title = preg_replace('/\d/', '',str_replace('.','',trim($resultdata->title))); 

                $amount =  (float)$resultdata->quantity * (float)str_replace(',', '',$resultdata->rate);
                $amount = ($amount>0)?number_format($amount,2):"";
                $levelOneTotals = $this->levelOneTotals($resultdata->id);
                $amount = ($resultdata->level == 1)?$levelOneTotals["amount"]:$amount;

                $quantity_done = ($resultdata->level == 1)?$levelOneTotals["quantity_done"]:$resultdata->quantity_done;
                $cost_of_work = ($resultdata->level == 1)?$levelOneTotals["cost_of_work"]:$resultdata->cost_of_work;
                $value_of_work = ($resultdata->level == 1)?$levelOneTotals["value_of_work"]:$resultdata->value_of_work;
                $profit_loss = ($resultdata->level == 1)?$levelOneTotals["profit_loss"]:$resultdata->profit_loss;

               // $quantity_done = ($resultdata->level == 1)?

                        $row = [
                            'id' => $resultdata->id,
                            'index' => $index,                  
                            'title' => trim($title),
                            'parent' => $resultdata->parent,
                            'boq_id' => $resultdata->boq_id,
                            'level' => $resultdata->level,
                            'updated_at' => $resultdata->updated_at,
                            'created_at' => $resultdata->created_at,
                            'cell' => $resultdata->cell,
                            'rownumber' => $resultdata->rownumber,
                            'unit' => $resultdata->unit,
                            'quantity' => $resultdata->quantity,
                            'rate' => $resultdata->rate,
                            'bc_id' => $resultdata->bc_id,
                            'amount' => $amount,
                            'quantity_done' => $quantity_done,
                            'cost_of_work' => $cost_of_work,
                            'value_of_work' => $value_of_work,
                            'profit_loss' => $profit_loss       
                        ];
                        $data[] = $row;
                        $filteredrows[] = $resultdata->rownumber; 
            } 

            
            return response()->json(new JsonResponse(['items' => $data, 'total' =>count($data)]));
        }

        public function spreadsheetLevel(Request $request){
            $data = [];
           /* $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT 
                *
            FROM
                boq_levels
            WHERE
                level = '".$request->level."' AND parent = '".$request->selected."';")); */

            $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT 
                bl.id,
                bl.title,
                bl.parent,
                bl.boq_id,
                bl.cell,
                bl.level,
                EXTRACTNUMBER(bl.cell) rownumber,
                bl.created_at,
                bl.updated_at,
                bc.unit,
                bc.quantity,
                bc.rate,
                FORMAT(IFNULL(SUM(bc.quantity * bc.rate), 0),
                2) amount,
                bc.id bc_id,
                bc.quantity_done,
                bc.cost_of_work,
                bc.value_of_work,
                bc.profit_loss
            FROM
                boq_levels bl
                    LEFT JOIN
                boq_cells bc ON bc.parent = bl.parent
                    AND bc.cell = bl.cell
            WHERE
                 bl.parent = '".$request->selected."' 
            GROUP BY bc.cell;")); //bl.level = '".$request->level."' AND
                
            //remove rows of the excel not needed.
           /* $finalFile = "uploads\bill-of-quantities\SampleRemoveRows.xlsx";
            $editedFile = "uploads\bill-of-quantities\EditedRows.xlsx";
            $reader = new Xlsx();
            $spreadsheet = $reader->load($finalFile);
            $sheet = $spreadsheet->getActiveSheet();*/

          if($request->level != 1){ 
            $dBDataLevelOne = DB::connection('mysql')->select( DB::raw("
            SELECT 
                *
            FROM
                boq_levels bc
            WHERE
                id = '".$request->selected."' 
            "));
            $data = [];
            if(count($dBDataLevelOne) > 0){
                $index = preg_replace("/[^0-9\.]/", '', trim($dBDataLevelOne[0]->title));
                $title = preg_replace('/\d/', '',str_replace('.','',trim($dBDataLevelOne[0]->title)));
                $index = rtrim($index, "0"); 
               
                $levelOneTotals = $this->levelOneTotals($request->selected);
                $amount = $levelOneTotals["amount"];
                $quantity_done = $levelOneTotals["quantity_done"];
                $cost_of_work = $levelOneTotals["cost_of_work"];
                $value_of_work =$levelOneTotals["value_of_work"];
                $profit_loss = $levelOneTotals["profit_loss"];
                
                $row = [
                    'id' => uniqid(),                  
                    'index' => $index,                  
                    'title' => trim($title),
                    'parent' => 0,
                    'boq_id' =>$request->selected,
                    'level' => 1,
                    'updated_at' => "",
                    'created_at' => "",
                    'cell' => "",
                    'rownumber' => "",
                    'unit' => "",
                    'quantity' => "",
                    'rate' =>"",
                    'amount' => $amount,
                    'quantity_done' => $quantity_done,
                    'cost_of_work' => $cost_of_work,
                    'value_of_work' => $value_of_work,
                    'profit_loss' => $profit_loss       
                ];
                array_push($data,$row);
            }
        }
             
           
            foreach ($dBData as $key =>$resultdata){ 
                $index = preg_replace("/[^0-9\.]/", '', trim($resultdata->title));
                $title = preg_replace('/\d/', '',str_replace('.','',trim($resultdata->title))); 
                $amount = ($resultdata->amount>0)?$resultdata->amount:"";               
                         
                        $row = [
                            'id' => $resultdata->id,                  
                            'index' => $index,                  
                            'title' => trim($title),
                            'parent' => $resultdata->parent,
                            'boq_id' => $resultdata->boq_id,
                            'level' => $resultdata->level,
                            'updated_at' => $resultdata->updated_at,
                            'created_at' => $resultdata->created_at,
                            'cell' => $resultdata->cell,
                            'rownumber' => $resultdata->rownumber,
                            'unit' => $resultdata->unit,
                            'quantity' => $resultdata->quantity,
                            'rate' => $resultdata->rate,
                            'amount' =>$amount,
                            'bc_id' => $resultdata->bc_id,
                            'quantity_done' => $resultdata->quantity_done ,
                            'cost_of_work' => $resultdata->cost_of_work ,
                            'value_of_work' => $resultdata->value_of_work ,
                            'profit_loss' => $resultdata->profit_loss        
                        ];
                        array_push($data,$row);
                        ///$data[] = $row;
                        ////$filteredrows[] = $resultdata->rownumber; 
            } 

            //$spreadsheet->getActiveSheet()->removeRow(1,4);

           /* for ($x = 1; $x <= 500; $x++) {
                if (!in_array($x, $filteredrows)) { 
                 $spreadsheet->getActiveSheet()->removeRow($x);
                }
              }
       
            $spreadsheet->getActiveSheet()->removeRow(90,1);
            $writer = new Writer($spreadsheet);
            $writer->save($editedFile);*/
           // $spreadsheet->getActiveSheet()->removeRow(91,125);  
           /* $count = 1;
            $deletestart = 1;
            foreach ($sheet->getRowIterator() as $row) {               
               if (!in_array($count, $filteredrows)) { 
                   $spreadsheet->getActiveSheet()->removeRow((int)$count,1); 
                }             
		    $count++;
            }*/
            //exit();
            //var_dump($count); 
           /* $deletestart = 1;
            foreach ($filteredrows as $key =>$cellnumber){

                $number_rows = $cellnumber - $deletestart; 
                
                $spreadsheet->getActiveSheet()->removeRow($deletestart,$number_rows);
                $deletestart = $cellnumber + 1; 
               
                
                //var_dump($cellnumber); exit();
            }*/


            //exit();
     
         
            
        ///////var_dump($filteredrows); exit();
           /*
            $spreadsheet->getActiveSheet()->removeRow(5, 1);
            $writer = new Writer($spreadsheet);
            $writer->save($editedFile);
            */

          
            
            return response()->json(new JsonResponse(['items' => $data, 'total' =>count($data)]));
        }


        public function spreadsheetCells(Request $request){
            $data = [];
            $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT * FROM boq_cells WHERE parent = '".$request->selected."';"));     
            foreach ($dBData as $key =>$resultdata){              
                        $row = [
                            'id' => $resultdata->id,                  
                            'cell' => $resultdata->cell,
                            'parent' => $resultdata->parent,
                            'boq_id' => $resultdata->boq_id,                           
                            'updated_at' => $resultdata->updated_at,
                            'created_at' => $resultdata->created_at   
                        ];
                        $data[] = $row;
            }           
            return response()->json(new JsonResponse(['items' => $data, 'total' =>count($data)]));
        }



    public function updateBqTotals(Request $request){
        $issuccess = false;
        $message = ""; //var_dump($request->cell); exit();
        $dbData = BOQCells::find($request->cell);
        $dbData->update(array(
                'quantity_done' => $request->quantity_done,
                'cost_of_work' => $request->cost_of_work,
                'value_of_work' =>$request->value_of_work,                
                'profit_loss' =>$request->profit_loss
            ));
        $issuccess = true;
        $message = "Successfully updated the summation!";
        $responseBody = array("message"=>$message,"success"=>$issuccess,"quantity_done"=>$request->quantity_done,"cost_of_work"=>$request->cost_of_work,"value_of_work"=>$request->value_of_work,"profit_loss"=>$request->profit_loss);        
        return response($responseBody)->header('Content-Type', 'application/json');  

    }











}