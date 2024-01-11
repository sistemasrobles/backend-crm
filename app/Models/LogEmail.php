<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon\Carbon;


class LogEmail extends Model
{
    use HasFactory;


    protected $table = 'log_emails';

    
    protected static function saveLog($message,$subjet,$destinatario,$smtpUsername,$user){


        unset($message['logo']);

        $logEmail = new LogEmail();  
        $logEmail->subject = $subjet;
        $logEmail->body = json_encode($message);
        $logEmail->recipients = json_encode($destinatario);
        $logEmail->from = json_encode($smtpUsername);
        $logEmail->created_at= Carbon::now();
        $logEmail->created_by= $user;
        $logEmail->updated_by= $user;
        $logEmail->save();

    }
    
}