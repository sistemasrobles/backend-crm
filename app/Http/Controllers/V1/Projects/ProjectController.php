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
class ProjectController extends Controller
{   

    protected $GlobalUser;

    public function __construct(Request $request)
    {
        $token = $request->header('Authorization');

        if($token != '')
            
            $this->GlobalUser = JWTAuth::parseToken()->authenticate();
    }


    

    protected function listProjects(Request $request)
    {
        
        
         

           

            $list = DB::select("exec sp_list_projects ?" , array(0));

            $middleRpta = $this->setRpta('ok','consulta de manera exitosa',$list);

            return response()->json($middleRpta,200);
        
    }


    protected function downloadFiles(Request $request)
    {
        
        
           

          $validate = $request->only('id');
      
              $validator = Validator::make($validate, [
            
              'id'=>'required|integer',
        
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                 return response()->json($middleRpta,400);
             }

           

            $row = DB::select("SELECT path_file,local_file FROM projects_files WITH (NOLOCK)  WHERE id=?" , array($request->id));


            if(!empty($row)){

                $directory = $row[0]->local_file;

                  if (file_exists($directory)){

                    $url= $row[0]->path_file;

                    $middleRpta = $this->setRpta('ok','consulta de manera exitosa',$url);

                  }else{

                    $middleRpta = $this->setRpta('error','no se encontro el fichero',[]);
                  }



            }else{

                $middleRpta = $this->setRpta('error','no se encontro el archivo',[]);
            }



             

            
            

            return response()->json($middleRpta,200);
        
    }




    protected function deletedFiles(Request $request){


        $validate = $request->only('id');
      
              $validator = Validator::make($validate, [
            
              'id'=>'required|integer',
        
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                 return response()->json($middleRpta,400);
             }


             $row = DB::select("SELECT local_file FROM projects_files WITH (NOLOCK) WHERE id = ?" ,array($request->id));

            
             if(!empty($row)){

                $directory = $row[0]->local_file;

                

                if (file_exists($directory)) {

                   $row_delete = DB::delete('DELETE FROM projects_files WHERE id = ?', [$request->id]);

                   if ($row_delete > 0) {

                        unlink($directory);

                        $middleRpta =  $this->setRpta('ok','se eliminó el archivo de manera correcta',[]);

                       

                    } else {

                         $middleRpta =  $this->setRpta('error','no se pudo eliminar el archivo',[]);

                       
                    }



                }else{

                    $middleRpta =  $this->setRpta('error','no se pudo encontrar el fichero en el sistema',[]);

                    
                }

             
               
             }else{


                 $middleRpta =  $this->setRpta('error','no se pudo encontrar el path del fichero',[]);

             }

             

              return response()->json($middleRpta,200);
    }



    protected function uploadFiles(Request $request)
    {
        
        $validate = $request->only('file','project_id','type');
      
              $validator = Validator::make($validate, [
            
              'project_id'=>'required|integer|exists:projects,id',
              'file'=>'required|file',    
              'type'=>'required|string|in:galery,blueprints,files',
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                 return response()->json($middleRpta,400);
             }


        
         if($request->file('file')){

              $id_project = $request->project_id;

              $type = $request->type;

              $now = Carbon::now()->format('Y-m-d H:i:s');

              $project = Project::find($id_project);
            
              $slug = $project->slug;

              $directory      = 'projects/'.$slug;

              $user_id = $this->GlobalUser->id;

             


                $extension  = strtolower($request->file('file')->getClientOriginalExtension()); 

                //$file_name = $request->file('file')->getClientOriginalName();
                


               
                $file_name = Str::random(40) . '.' . $extension;





                 $path_full = url('/').'/'.$directory.'/'.$file_name;

                 $path_local = $directory.'/'.$file_name;

                    if($request->file('file')->move($directory, $file_name)){

                      DB::insert("INSERT INTO projects_files(project_id,type_file,path_file,local_file,extension_file,name_file,created_at,created_by) VALUES(?,?,?,?,?,?,?,?);",array($id_project,$type,$path_full,$path_local,$extension,$file_name,$now,$user_id ));

                      $lastInsertId = DB::getPdo()->lastInsertId();

                      $resultData = ["id"=>$lastInsertId];
                      
                      $middleRpta = $this->setRpta('ok','se cargo el archivo de manera correcta',$resultData);

                    }else{

                       $middleRpta = $this->setRpta('error','no se pudo mover el archivo',[]);

                    }




                

                return response()->json($middleRpta,200);


         }

           
          
         
        
    }



    protected function editProject(Request $request)
    {
        
        
           $validate = $request->only('id');
      
              $validator = Validator::make($validate, [
            
              'id'=>'required|integer',
            
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                 return response()->json($middleRpta,400);
             }

           

            $basic = DB::select("exec sp_get_item_project ?" , array($request->id));

            $galery = DB::select("exec sp_get_item_project_fil ?,?" , array($request->id ,'galery'));

            $blueprints = DB::select("exec sp_get_item_project_fil ?,?" , array($request->id,'blueprints'));

            $files = DB::select("exec sp_get_item_project_fil ?,?" , array($request->id,'files'));


            $data = [

                'basic'=> $basic ,
                'galery'=> $galery ,
                'blueprints'=> $blueprints ,
                'files'=> $files ,
                
            ];

            $middleRpta = $this->setRpta('ok','consulta de manera exitosa',$data);

            return response()->json($middleRpta,200);
        
    }
   

    
     


     protected function saveProject(Request $request){



        try {
            


            DB::beginTransaction();

            $validate = $request->only('id','name','description','id_type_project','razon_social','ubigeo','direccion','facebook','youtube','instagram','pagina_web','logo');
      
            $validator = Validator::make($validate, [
            
              'id' => 'required|integer',
              'name'=>'required|string|max:250',
              'description'=>'nullable|string|max:250',
              'id_type_project'=>'required|integer',
              'razon_social'=>'nullable|string|max:250',
              'ubigeo'=>'required|string|max:250',
              'direccion'=>'nullable|string|max:250',
              'facebook'=>'nullable|string|max:250',
              'youtube'=>'nullable|string|max:250',
              'instagram'=>'nullable|string|max:250',
              'pagina_web'=>'nullable|string|max:250',
              'logo'=>'required|file|mimes:jpeg,png,jpg,gif,webp|max:4048',
             
            ]);
        

            if ($validator->fails()) {

                $middleRpta = $this->setRpta('warning','complete los campos requeridos',$validator->messages());

                return response()->json($middleRpta,400);
            }

            
            Project::saveProject($request);

         

            DB::commit();


             $middleRpta = $this->setRpta('ok','Operación realizada de manera exitosa',[]);

             return response()->json($middleRpta,200);

        } catch (\Exception $e) {
            
            DB::rollBack();

             $middleRpta = $this->setRpta('error',$e->getMessage(),[]);


             return response()->json($middleRpta,400);
        }


    }
}
