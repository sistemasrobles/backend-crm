<?php 

namespace App\Http\Controllers\V1\Leads;
use App\Http\Controllers\Controller;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Lead;
use App\Models\Activity;
use App\Models\Interaction;
use App\Models\Customer;
use DB;
use JWTAuth;

class LeadController extends Controller
{   

    protected $GlobalUser;

    public function __construct(Request $request)
    {
        $token = $request->header('Authorization');

        if($token != '')
            
            $this->GlobalUser = JWTAuth::parseToken()->authenticate();
    }


    protected function addCustomer(Request $request){



        try {
            

            DB::beginTransaction();

            $validate = $request->only('id','name','surename','id_document','ndocument','email','phone','landline','country','ubigeo','address','birthdate','gender','status_civil');
      
            $validator = Validator::make($validate, [
            
              'id'=>'required|integer',
              'name'=>'required|string|max:250',
              'surename'=>'required|string|max:250',
              'id_document'=>'required|integer',
              'ndocument'=>'required|string|max:20',
              'email'=>'required|string|email|max:250',
              'phone'=>'required|string|max:20',
              'landline'=>'nullable|string|max:20',
              'country'=>'required|string|max:100',
              'ubigeo'=>'required|string|max:10',
              'address'=>'required|string|max:250',
              'birthdate'=>'required|string|max:10',
              'gender'=>'required|integer',
              'status_civil'=>'required|integer',
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                return response()->json($middleRpta,400);
            }


            //valida duplicados 

            $uniques_keys =["ndocument","email","phone"];

            foreach($uniques_keys as $values){

                $filter = trim($request->{$values});

                if($request->id == 0){

                    $count = Customer::where($values,'=',$filter)->count();

                }else{

                    $count = Customer::where($values,'=',$filter)->where('id','!=',$request->id)->count();
                }
                

             

                if($count > 0){

                    if($values == "ndocument"){

                        $msj = 'número de documento';

                    }
                    if($values == "email"){

                        $msj = 'correo electrónico';
                        
                    }
                    if($values == "phone"){

                        $msj = 'celular';
                        
                    }
                    $middleRpta = $this->setRpta('error','el campo '.$msj.' ya se encuentra registrado',[]);

                    return response()->json($middleRpta,400);

                }
            }


           
            



            Customer::addCustomer($request);

            $operation = ($request->id == 0)?'crear':'editar';


            $description = 'El usuario '.$this->GlobalUser->name.' acaba de '.$operation.' un cliente con número de documento :'.$request->ndocument;
            Activity::insertActivity($description);

            DB::commit();


             $middleRpta = $this->setRpta('ok','Operación de cliente de manera exitosa',[]);

             return response()->json($middleRpta,200);

        } catch (\Exception $e) {
            
            DB::rollBack();

             $middleRpta = $this->setRpta('error',$e->getMessage(),[]);


             return response()->json($middleRpta,400);
        }


    }


    protected function addSpouse(Request $request){



        try {
            


    
            DB::beginTransaction();

            $validate = $request->only('id','name','surename','id_document','ndocument','email','phone','country','birthdate','gender','status_civil');
      
            $validator = Validator::make($validate, [
            
              'id' => 'required|integer|exists:customers,id',
              'name'=>'required|string|max:250',
              'surename'=>'required|string|max:250',
              'id_document'=>'required|integer',
              'ndocument'=>'required|string|max:20',
              'email'=>'required|string|email|max:250',
              'phone'=>'required|string|max:20',
              'country'=>'required|string|max:100',
              'birthdate'=>'required|string|max:10',
              'gender'=>'required|integer',
              'status_civil'=>'required|integer',
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                return response()->json($middleRpta,400);
            }

        
            Customer::addSpouse($request);

           
            $description = 'El usuario '.$this->GlobalUser->name.' editó el conyugue del cliente con id :'.$request->id;
            Activity::insertActivity($description);

            DB::commit();


             $middleRpta = $this->setRpta('ok','Creación de conyugue de manera exitosa',[]);

             return response()->json($middleRpta,200);

        } catch (\Exception $e) {
            
            DB::rollBack();

             $middleRpta = $this->setRpta('error',$e->getMessage(),[]);


             return response()->json($middleRpta,400);
        }


    }


    protected function assignUser(Request $request){



        try {
            

            DB::beginTransaction();

            $validate = $request->only('id_lead','id_user');
      
            $validator = Validator::make($validate, [
            
              'id_lead' => 'required|integer|exists:leads,id',
              'id_user' => 'required|integer|exists:users,id',
              
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                return response()->json($middleRpta,400);
            }

        
            Lead::assignUser($request);

            $to_user = User::find($request->id_user);

            $description = 'El usuario '.$this->GlobalUser->name.' cambió la asignación del lead con id :'.$request->id_lead.' al usuario '.$to_user->name;

            Activity::insertActivity($description);

            DB::commit();


             $middleRpta = $this->setRpta('ok','Asignación creada correctamente',[]);

             return response()->json($middleRpta,200);

        } catch (\Exception $e) {
            
            DB::rollBack();

             $middleRpta = $this->setRpta('error',$e->getMessage(),[]);


             return response()->json($middleRpta,400);
        }


    }



    protected function addInteraction(Request $request)
    {
        
        try {
            

            DB::beginTransaction();

            $validate = $request->only('id_lead','id_interaction','id_project','id_item','observation','id_interest','id_event','name_event','date_event','id_drop_reason','add_event');
      
            $validator = Validator::make($validate, [
            
              'id_lead' => 'required|integer',
              'id_interaction' => 'required|integer',
              'id_project' => 'required|integer',
              'id_interest' => 'required|integer',
              'observation'=>'nullable|string|max:250',
              'id_event'=>'required_if:add_event,==,1|nullable',
              'name_event'=>'required_if:add_event,==,1|nullable|string|max:250',
              'date_event'=>'required_if:add_event,==,1|nullable',
              'id_drop_reason'=>'required_if:id_interest,==,1|nullable',
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                return response()->json($middleRpta,400);
            }

        
           Interaction::insertInteraction($request);

            $description = 'El usuario '.$this->GlobalUser->name.' agregó una nueva interacción del lead con id :'.$request->id_lead;

            Activity::insertActivity($description);


            DB::commit();


             $middleRpta = $this->setRpta('ok','interacción creada correctamente',[]);

             return response()->json($middleRpta,200);

        } catch (\Exception $e) {
            
            DB::rollBack();

             $middleRpta = $this->setRpta('error',$e->getMessage(),[]);


             return response()->json($middleRpta,400);
        }


        
    }
}
