<?php 

namespace App\Http\Controllers\V1\Maintenance;
use App\Http\Controllers\Controller;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Cia;
use App\Models\LogEmail;
use App\Models\Activity;
use Carbon\Carbon;
class EmailController extends Controller
{   

    public function svgToBase64 ($filepath){  

        

        if (file_exists($filepath)){

            $filetype = pathinfo($filepath, PATHINFO_EXTENSION);

            if ($filetype==='svg'){
                $filetype .= '+xml';
            }

            $get_img = file_get_contents($filepath);

            return 'data:image/' . $filetype . ';base64,' . base64_encode($get_img );
        }
    }

    

    public function recoveryPassword(Request $request)
    {
        
        try {
            


        
            $validate = $request->only('email');
      
            $validator = Validator::make($validate, [
            
              'email' => 'required|email|exists:users,email',
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                return response()->json($middleRpta,400);
            }



            $person = User::where('email','=',$request->email)->first();

            $parameters = $this->parametersEmail();


            $smtpHost = $parameters['smtp_host'];
            $smtpPort = $parameters['smtp_port'];
            $smtpUsername = $parameters['smtp_username'];
            $smtpPassword = $parameters['smtp_password'];
            $smtpAlias      = $parameters['smtp_alias'];
            $smtpEncryption = $parameters['smtp_encryption'];


            $destinatario = $request->input('email');



            $password = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

            $transport = (new Swift_SmtpTransport($smtpHost, $smtpPort, $smtpEncryption))
                ->setUsername($smtpUsername)
                ->setPassword($smtpPassword);

            
            $mailer = new Swift_Mailer($transport);

          

            $logo = $this->svgToBase64('images/logo.svg');
            
            

            $message = [
              
                'logo'=>$logo,
                'header'=>"Estimado Colaborador : ".$person->name.' '.$person->surename,
                'body'=>"Usted solicitó el restablecimiento de contraseña para su cuenta de aplicación<br><br> <strong>Usuario</strong> : ".$request->email." <br> <strong>Password </strong>: ".$password." <br><br>Para poder acceder a la plataforma ingrese al siguiente link : <a href='https://gruporobles.com.pe/' target='_blank'>Mi Plataforma</a> ,si usted no realizó esta petición ignore este correo",
                'footer'=>"Atentamente <br>Sistema automatizado de mensajaría.",
            ];

            $subjet = 'Envio de nuevas credenciales';

            $mensaje = (new Swift_Message($subjet))
                ->setFrom([$smtpUsername => $smtpAlias])
                ->setTo([$destinatario])
                ->setBody(View::make('emails.recoveryPassword',['message'=>$message])->render(), 'text/html');

            
            $result = $mailer->send($mensaje);

            
            if ($result) {

                
                $activity = new Activity();  
                $activity->description_max = 'El usuario con correo '.$request->email.' actualizó su contrasena';
                $activity->created_at= Carbon::now();
                $activity->created_by= $person->id;
                $activity->save();

                LogEmail::saveLog($message,$subjet,$destinatario,$smtpUsername,$person->id);

                User::where('id', $person->id)->update(['password' => bcrypt($password)]);

                $middleRpta = $this->setRpta('ok','Se acaba de enviar un correo electrónico , siga las instrucciones para recuperar su cuenta',[]);

                return response()->json($middleRpta,Response::HTTP_OK);
            } 

             $middleRpta = $this->setRpta('error','No se pudo enviar el correo electrónico , comuniquese con el área de soporte',[]);

             return response()->json($middleRpta,400);

        } catch (\Exception $e) {
            
             $middleRpta = $this->setRpta('error',$e->getMessage(),[]);

             return response()->json($middleRpta,400);
        }


        
    }
}
