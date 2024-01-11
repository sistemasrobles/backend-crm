<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon\Carbon;

use JWTAuth;

class Project extends Model
{
    use HasFactory;

    public $timestamps = false;

  
     protected static function createDirectory($string){


        $slug = str_replace(' ', '-', strtolower($string));

        return $slug;

     }

     protected static function saveProject($request){

         $id                = $request->id ;
         $name              = $request->name ;
         $description       = $request->description ;
         $id_type_project   = $request->id_type_project ;
         $razon_social      = $request->razon_social ;
         $ubigeo            = $request->ubigeo ;
         $direccion         = $request->direccion ;
         $facebook          = $request->facebook ;

         $youtube           = $request->youtube ;
         $instagram         = $request->instagram ;
         $pagina_web        = $request->pagina_web ;
       

         $now = Carbon::now()->format('Y-m-d H:i:s');
         
         $user = JWTAuth::parseToken()->authenticate();
        
         

        

         if($id == 0){

            $project = self::make();   

            $project->created_by = $user->id;
            $project->created_at = $now;


            //crear directorio para nuevo 

            $slug = self::createDirectory($name);

            $project->slug = $slug;

            $directorio = 'projects/'.$slug;

            if (!is_dir($directorio)) {

                mkdir($directorio, 0777, true);

            }


         }else{

            $project = self::find($id);

            $project->updated_by = $user->id;
            $project->updated_at = $now;

         }
              //cargar logo


                if($request->file('logo')){

                    $directory =  'projects/'.$project->slug ;
                    
                    $file_name = $request->file('logo')->getClientOriginalName();
                
                    $path_full = url('/').'/'.$directory.'/'.$file_name;

                    if($request->file('logo')->move($directory, $file_name)){

                        $project->logo = $path_full;

                    }

                }
        
                $project->name = $name;
                $project->description = $description;
                $project->type_project = $id_type_project;
                $project->business_name = $razon_social;
                $project->ubigeo = $ubigeo;
                $project->address = $direccion;
                $project->facebook = $facebook;
                $project->youtube = $youtube;
                $project->instagram = $instagram;
                $project->web = $pagina_web;
                
                $project->cia_id = 1;
              

                $project->save();


    }


    
    
    
}