<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon\Carbon;
use JWTAuth;

class Activity extends Model
{
    use HasFactory;


    protected $table = 'activities';

    
    protected static function insertActivity($description){


       
        $user = JWTAuth::parseToken()->authenticate();
        $activity = new Activity();  
        $activity->description_max = $description;
        $activity->created_at= Carbon::now();
        $activity->updated_at= Carbon::now();
        $activity->created_by= $user->id;
        $activity->updated_by= $user->id;
        $activity->save();

    }
    
}