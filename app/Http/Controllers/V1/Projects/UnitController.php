<?php 

namespace App\Http\Controllers\V1\Projects;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use JWTAuth;
use App\Models\Project;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Str;
class UnitController extends Controller
{   

    protected $GlobalUser;

    public function __construct(Request $request)
    {
        $token = $request->header('Authorization');

        if($token != '')
            
            $this->GlobalUser = JWTAuth::parseToken()->authenticate();
    }


    


    protected function listUnits(Request $request)
    {
        
        
           $validate = $request->only('project');
      
              $validator = Validator::make($validate, [
            
              'project'=>'required|integer',
            
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                 return response()->json($middleRpta,400);
             }

           

            $data = DB::select("exec sp_list_units ?" , array($request->project));


            $middleRpta = $this->setRpta('ok','consulta de manera exitosa',$data);

            return response()->json($middleRpta,200);
        
    }


     protected function getDetails(Request $request)
    {
        
        
           $validate = $request->only('id');
      
              $validator = Validator::make($validate, [
            
              'id'=>'required|integer',
            
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                 return response()->json($middleRpta,400);
             }

           

            $data = DB::select("exec sp_get_details_units ?" , array($request->id));


            $middleRpta = $this->setRpta('ok','consulta de manera exitosa',$data);

            return response()->json($middleRpta,200);
        
    }
   
   

    
     


}
