<?php 

namespace App\Http\Controllers\V1\Maintenance;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use JWTAuth;
use App\Models\Maintenance;
use App\Models\Ubigeo;
use Carbon\Carbon;
use DB;

class MaintenanceController extends Controller
{   

    protected $GlobalUser;

    public function __construct(Request $request)
    {
        $token = $request->header('Authorization');

        if($token != '')
            
            $this->GlobalUser = JWTAuth::parseToken()->authenticate();
    }


    

    protected function getTable(Request $request)
    {
        
        
          $validate = $request->only('id');
      
            $validator = Validator::make($validate, [
            
              'id'=>'required|integer',
            
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                return response()->json($middleRpta,400);
            }

            $list = Maintenance::select('id','label')->where('table_id','=',$request->id)->where('active_row','=',1)->from(DB::raw('masters  WITH (NOLOCK)'))->get();

            $middleRpta = $this->setRpta('ok','consulta de manera exitosa',$list);

            return response()->json($middleRpta,200);
        
    }


    protected function getDepartment(Request $request)
    {
        
        

            $list= DB::table('ubigeos')
                    ->select('department AS id', 'department as text')
                    ->where('province', '=', '')
                    ->where('district', '=', '')
                    ->from(DB::raw('ubigeos WITH (NOLOCK)'))
                    ->get();


            $middleRpta = $this->setRpta('ok','consulta de manera exitosa',$list);

            return response()->json($middleRpta,200);
        
    }


    protected function getProvince(Request $request)
    {
            

            $validate = $request->only('department');
      
            $validator = Validator::make($validate, [
            
              'department'=>'required|string|max:100',
            
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                return response()->json($middleRpta,400);
            }



        
            $list = DB::table('ubigeos')
                    ->select(DB::raw('DISTINCT province AS id'),'province as text')
                    ->where('department', '=', $request->department)
                    ->where('province', '!=', '')
                    ->from(DB::raw('ubigeos WITH (NOLOCK)'))
                    ->get();

            $middleRpta = $this->setRpta('ok','consulta de manera exitosa',$list);

            return response()->json($middleRpta,200);
        
    }

     protected function getDistrict(Request $request)
    {
        
            

            $validate = $request->only('province');
      
            $validator = Validator::make($validate, [
            
              'province'=>'required|string|max:100',
            
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                return response()->json($middleRpta,400);
            }


            $list= DB::table('ubigeos')
                    ->select('code AS id', 'district as text')
                    ->where('province', '=', $request->province)->where('district','!=','')
                    ->from(DB::raw('ubigeos WITH (NOLOCK)'))
                    ->get();


            $middleRpta = $this->setRpta('ok','consulta de manera exitosa',$list);


            return response()->json($middleRpta,200);
        
    }
}
