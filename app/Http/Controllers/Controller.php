<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Cia;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    
    public function setRpta($status,$description,$data){


        return array('status' => $status,'description' => $description,'data' => $data);

        
    }

    public function parametersEmail(){


      

        return array(

            'smtp_encryption'=>'tls',
            'smtp_host'=>'smtp.gmail.com',
            'smtp_port'=>'587',
            'smtp_username'=>'sistemasrobles23@gmail.com',
            'smtp_password'=>'fsfd tsdg pous pbif',
            'smtp_alias'=>'Sistemas Robles & Yasikov',

        );

    }


}
