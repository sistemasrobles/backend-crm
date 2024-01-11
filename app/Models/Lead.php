<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use JWTAuth;
use Carbon\Carbon;


class Lead extends Model
{
    use HasFactory;


    


    protected static function assignUser($request){

         $id_lead        = $request->id_lead ;
         $id_user        = $request->id_user ;

         $now = Carbon::now()->format('Y-m-d H:i:s');
         
         $user = JWTAuth::parseToken()->authenticate();
        

         
           $lead = self::find($id_lead);

            if ($lead) {

                $lead->assigned_user = $id_user;
                $lead->date_assigned = $now;
                $lead->updated_at = $now;
                $lead->updated_by = $user->id;

              

                $lead->save();
            }


    }
    
}