<?php
namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
   
    public function register(Request $request)
    {
       
        $data = $request->only('name', 'email', 'password');
      

        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',
        ]);
        

        if ($validator->fails()) {

            $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

            return response()->json($middleRpta,400);
        }

      
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
       
        $credentials = $request->only('email', 'password');
        
        $newuser = ['token' => JWTAuth::attempt($credentials),
                    'user' => $user];

        $middleRpta = $this->setRpta('ok','usuario creado satisfactoriamente',$newuser);

        return response()->json($middleRpta,Response::HTTP_OK);
    }


   
    public function authenticate(Request $request)
    {
      
        $credentials = $request->only('email', 'password');
       
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);
        
        if ($validator->fails()) {

            $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

            return response()->json($middleRpta, 400);
        }
       
        try {

            if (!$token = JWTAuth::attempt($credentials)) {
                
                $middleRpta = $this->setRpta('error','login fallido',[]);

                return response()->json($middleRpta, 401);
            }

        } catch (JWTException $e) {
            
            $middleRpta = $this->setRpta('error',$e->getMessage(),[]);

            return response()->json($middleRpta, 500);
        }
        
        $user_validate = [
                        'token' => $token,
                        'user' => Auth::user()
                        ];

        $middleRpta = $this->setRpta('ok','usuario autenticado correctamente',$user_validate);
                        
        return response()->json($middleRpta,Response::HTTP_OK);


    }
    

    public function logout(Request $request)
    {
       
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);
        
        if ($validator->fails()) {


            $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());


            return response()->json($middleRpta, 400);
        }

        try {
           
            JWTAuth::invalidate($request->token);

            $middleRpta = $this->setRpta('ok','usuario desconectado correctamente',[]);

            return response()->json($middleRpta,Response::HTTP_OK);

        } catch (JWTException $e) {
            
            $middleRpta = $this->setRpta('error',$e->getMessage(),[]);

            return response()->json($middleRpta,Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
  

    public function getUser(Request $request)
    {
       
        $this->validate($request, [
            'token' => 'required'
        ]);
       
        $user = JWTAuth::authenticate($request->token);
     
        if(!$user){


            $middleRpta = $this->setRpta('error','token invalido / token expirado',[]);

            return response()->json($middleRpta, 401);
        }


        $newuser = ['user' => $user]; 
        
        $middleRpta = $this->setRpta('ok','usuario encontrado',$newuser);
      
        return response()->json($middleRpta);
    }
}