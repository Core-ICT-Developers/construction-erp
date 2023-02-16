<?php
namespace App\Http\Controllers\Api;

use App\Laravue\JsonResponse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Laravue\Models\ProjectManagement;
use App\Laravue\Models\Buildings;
use DateTime;
use DateTimeZone;

class ProjectManagementController extends BaseController
{
    public function createProject(Request $request){ 
        $json = file_get_contents('php://input');
        $request = json_decode($json);
        $issuccess = false;
        $message = ""; 
              
        $workingdays = [];  

        $date = new DateTime($request->starttime, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('Africa/Nairobi'));        
        $starttime =  $date->format('H:i:s');

        $date = new DateTime($request->endtime, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('Africa/Nairobi'));        
        $endtime =  $date->format('H:i:s');

        $date = new DateTime($request->startdate, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('Africa/Nairobi'));        
        $startdate =  $date->format('Y-m-d');

        $mon =array("Monday",$request->workingdaysmon);
        array_push($workingdays,$mon);

        $tue =array("Tuesday",$request->workingdaystue);
        array_push($workingdays,$tue);

        $wed =array("Wednesday",$request->workingdayswed);
        array_push($workingdays,$wed);

        $thur =array("Thursday",$request->workingdaysthur);
        array_push($workingdays,$thur);

        $fri =array("Friday",$request->workingdaysfri);
        array_push($workingdays,$fri);

        $sat =array("Saturday",$request->workingdayssat);
        array_push($workingdays,$sat);

        $sun =array("Sunday",$request->workingdayssun);
        array_push($workingdays,$sun);

        //$string =  implode("",$workingdays);
        $workstring = json_encode($workingdays);
        
        //var_dump($workingdays);        
        //exit();

        ProjectManagement::create([
            'durationstep' => $request->durationstep,
            'starttime' =>$starttime,
            'endtime' =>$endtime,
            'projectname' =>$request->projectname,
            'startdate' =>$startdate,
            'templates'=>$request->templates,
            'workingdays' => $workstring,
            'created_at' =>date('Y-m-d H:i:s')
        ]);
        $issuccess = true;
        $message = "Successfully added a new project!";
        $responseBody = array("message"=>$message,"success"=>$issuccess);        
        return response($responseBody)->header('Content-Type', 'application/json');

    }

    public function projects(Request $request)
    {   
        $rowsNumber = 10;
        $data = [];
        $dBData = DB::connection('mysql')->select( DB::raw("
        SELECT 
            * FROM  projectmanagement WHERE parent IS NULL 
            ;
        "));     
        foreach ($dBData as $key =>$resultdata){
            $date = date_create($resultdata->startdate);        
                    $row = [
                        'id' => $resultdata->id,                  
                        'durationstep' => $resultdata->durationstep,
                        'starttime' => $resultdata->starttime,
                        'endtime' => $resultdata->endtime,
                        'projectname' =>$resultdata->projectname,
                        'startdate' =>date_format($date, 'Y-m-d'),
                        'templates' => $resultdata->templates,
                        'workingdays' => $resultdata->workingdays,
                        'updated_at' => $resultdata->updated_at,
                        'created_at' => $resultdata->created_at   
                    ];
                    $data[] = $row;
        }           
        return response()->json(new JsonResponse(['items' => $data, 'total' =>count($data)]));
    }

    public function fetchProjectGantt(Request $request)
    {   
        $data = [];
        $dBData = DB::connection('mysql')->select( DB::raw("
        SELECT 
            * FROM  projectmanagement WHERE id = '".$request->id."' 
            ;
        ")); 
        foreach ($dBData as $key =>$resultdata){
            $date = date_create($resultdata->startdate);
            $start_date = date_format($date, 'd-m-Y');
            $data[] = array("id"=>$resultdata->id,"text"=>$resultdata->projectname,"type"=>"gantt.config.types.project", "progress"=>$resultdata->progress ,"open"=>$resultdata->open,"start_date"=>$start_date); 
       
            $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT 
                * FROM  projectmanagement WHERE parent = '".$resultdata->id."' 
                ;
            ")); 
            foreach ($dBData as $key =>$resultdata){
                $date = date_create($resultdata->startdate);
                $start_date = date_format($date, 'd-m-Y');
                $data[] = array("id"=>$resultdata->id,"text"=>$resultdata->projectname,"progress"=>$resultdata->progress ,"open"=>$resultdata->open,"start_date"=>$start_date,"duration"=>$resultdata->duration,"parent"=>$resultdata->parent);  
           
                $dBDatal = DB::connection('mysql')->select( DB::raw("
                SELECT 
                    * FROM  projectmanagement WHERE parent = '".$resultdata->id."' 
                    ;
                ")); 
                foreach ($dBDatal as $key =>$resultdatal){
                    $date = date_create($resultdatal->startdate);
                    $start_date = date_format($date, 'd-m-Y');
                    $data[] = array("id"=>$resultdatal->id,"text"=>$resultdatal->projectname,"progress"=>$resultdatal->progress ,"open"=>$resultdatal->open,"start_date"=>$start_date,"duration"=>$resultdatal->duration,"parent"=>$resultdatal->parent);  
                
                    $dBDatal2 = DB::connection('mysql')->select( DB::raw("
                    SELECT 
                        * FROM  projectmanagement WHERE parent = '".$resultdatal->id."' 
                        ;
                    ")); 
                    foreach ($dBDatal2 as $key =>$resultdatal2){
                        $date = date_create($resultdatal2->startdate);
                        $start_date = date_format($date, 'd-m-Y');
                        $data[] = array("id"=>$resultdatal2->id,"text"=>$resultdatal2->projectname,"progress"=>$resultdatal2->progress ,"open"=>$resultdatal2->open,"start_date"=>$start_date,"duration"=>$resultdatal2->duration,"parent"=>$resultdatal2->parent);  
                    }                 
                }  
           
            } 
        } 

        //$data[0] = array("id"=>1, "text"=>"Project #1", "type"=>"gantt.config.types.project", "progress"=> "0.6", "open"=> "true");
        //$data[1] = array("id"=>2, "text"=>"Task #1", "start_date"=>"03-04-2018", "duration"=>"5", "parent"=>"1", "progress"=> 1, "open"=>true);        

        return response()->json(new JsonResponse(['items' => $data, 'total' =>count($data)]));
    }
    public function createTask(Request $request)
    {  
        $json = file_get_contents('php://input');
        $request = json_decode($json);
        $message = "";

        $date = new DateTime($request->planned_start, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('Africa/Nairobi'));        
        $planned_start =  $date->format('Y-m-d H:i:s');

        $date = new DateTime($request->planned_end, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('Africa/Nairobi'));        
        $planned_end =  $date->format('Y-m-d H:i:s');

        $date = new DateTime($request->start_date, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('Africa/Nairobi'));        
        $start_date =  $date->format('Y-m-d H:i:s');

        $dBData = DB::connection('mysql')->select( DB::raw("
        SELECT 
            * FROM  projectmanagement WHERE projectname = '".$request->text."' AND parent = '".$request->parent."' 
            ;
        "));
        if(count($dBData) == 0 && $request->is_new == true){
            ProjectManagement::create([
                'parent' => $request->parent,
                'planned_start' =>$planned_start,
                'planned_end' =>$planned_end,
                'startdate' =>$start_date,
                'projectname' =>$request->text,
                'progress'=>$request->progress,
                'open' => 0,
                'created_at' =>date('Y-m-d H:i:s')
            ]);
            $message = "Successfully added a new task!";
        }
        if($request->is_new == false){
            $dBData = DB::connection('mysql')->select( DB::raw("
            SELECT 
                * FROM  projectmanagement WHERE id = '".$request->id."' 
                ;
            "));
            if(count($dBData) > 0){                
            $dbData = ProjectManagement::find($request->id);
            $dbData->update(array(
                    'planned_start' =>$planned_start,
                    'planned_end' =>$planned_end,
                    'startdate' =>$start_date,
                    'projectname' =>$request->text,
                    'progress'=>$request->progress,
                    'open' => 0,               
                    'updated_at' =>date('Y-m-d H:i:s') 
                ));
                $message = "Successfully updated the task!";              
            }
        } 
        $issuccess = true;        
        $responseBody = array("message"=>$message,"success"=>$issuccess);        
        return response($responseBody)->header('Content-Type', 'application/json');
    }


    public function createBuilding(Request $request){ 
        $json = file_get_contents('php://input');
        $request = json_decode($json);
        $issuccess = false;
        $message = ""; 
        $id = Buildings::create([
            'building_name' => $request->buildingname,
            'project_id' =>$request->projectid,
            'created_at' =>date('Y-m-d H:i:s')
        ])->id;
        $issuccess = true;
        $message = "Successfully added a new building!";
        $responseBody = array("message"=>$message,"success"=>$issuccess,"id"=>$id);        
        return response($responseBody)->header('Content-Type', 'application/json');

    }

    public function buildings(Request $request)
    {   
        $rowsNumber = 10;
        $data = [];
        $dBData = DB::connection('mysql')->select( DB::raw("
        SELECT 
            * FROM  buildings WHERE project_id = '".$request->project_id."'
            ;
        "));     
        foreach ($dBData as $key =>$resultdata){                 
                    $row = [
                        'id' => $resultdata->id,                  
                        'project_id' => $resultdata->project_id,
                        'building_name' => $resultdata->building_name,
                        'updated_at' => $resultdata->updated_at,
                        'created_at' => $resultdata->created_at   
                    ];
                    $data[] = $row;
        }           
        return response()->json(new JsonResponse(['items' => $data, 'total' =>count($data)]));
    }


}