<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon\Carbon;

use JWTAuth;


class Interaction extends Model
{
    use HasFactory;


     protected static function insertInteraction($request){

         $id_lead        = $request->id_lead ;
         $id_interaction = $request->id_interaction ;
         $id_project     = $request->id_project ;
         $id_item        = $request->id_item ;
         $observation    = $request->observation ;
         $id_interest    = $request->id_interest ;
         $id_event       = $request->id_event ;
         $name_event     = $request->name_event ;
         $date_event     = (!empty($request->date_event))?Carbon::parse($request->date_event)->format('Y-m-d H:i:s'):null ;
         $id_drop_reason = $request->id_drop_reason ;
         $add_event      = $request->add_event ;
         
         $user = JWTAuth::parseToken()->authenticate();
         $user->id;

         DB::statement('exec sp_insert_interaction ?,?,?,?,?,?,?,?,?,?,?,?', array($id_lead,$id_interaction,$id_project,$id_item,$observation,$id_interest,$id_event,$name_event,$date_event,$id_drop_reason,$user->id,$add_event));


    }
    
}