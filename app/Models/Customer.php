<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use Carbon\Carbon;

use JWTAuth;


class Customer extends Model
{
    use HasFactory;


     public $timestamps = false;
   

     protected static function addCustomer($request){

         $id                = $request->id ;
         $name              = $request->name ;
         $surename          = $request->surename ;
         $id_document       = $request->id_document ;
         $ndocument         = $request->ndocument ;
         $email             = $request->email ;
         $phone             = $request->phone ;
         $landline          = $request->landline ;

         $country           = $request->country ;
         $ubigeo           = $request->ubigeo ;
         $address           = $request->address ;
         $birthdate         = Carbon::parse($request->birthdate)->format('Y-m') ;
         $gender            = $request->gender ;
         $status_civil      = $request->status_civil ;

         $now = Carbon::now()->format('Y-m-d H:i:s');
         
         $user = JWTAuth::parseToken()->authenticate();
        

         if($id == 0){

            $customer = self::make();   

            $customer->created_by = $user->id;
            $customer->created_at = $now;


         }else{

            $customer = self::find($id);

            $customer->updated_by = $user->id;
            $customer->updated_at = $now;

         }
           
        
                $customer->name = $name;
                $customer->surename = $surename;
                $customer->id_document = $id_document;
                $customer->ndocument = $ndocument;
                $customer->email = $email;
                $customer->phone = $phone;
                $customer->landline = $landline;
                $customer->country = $country;
                $customer->ubigeo = $ubigeo;
                $customer->address = $address;
                $customer->birthdate = $birthdate;
                $customer->id_gender = $gender;
                $customer->id_status_civil = $status_civil;
               

              

                $customer->save();


    }

  protected static function addSpouse($request){

         $id                = $request->id ;
         $name              = $request->name ;
         $surename          = $request->surename ;
         $id_document       = $request->id_document ;
         $ndocument         = $request->ndocument ;
         $email             = $request->email ;
         $phone             = $request->phone ;
         $country           = $request->country ;
         $birthdate         = Carbon::parse($request->birthdate)->format('Y-m') ;
         $gender            = $request->gender ;
         $status_civil      = $request->status_civil ;

         $now = Carbon::now()->format('Y-m-d H:i:s');
         
         $user = JWTAuth::parseToken()->authenticate();
        

         
           $customer = self::find($id);

            if ($customer) {

                $customer->name2 = $name;
                $customer->surename2 = $surename;
                $customer->id_document2 = $id_document;
                $customer->ndocument2 = $ndocument;
                $customer->email2 = $email;
                $customer->phone2 = $phone;
                $customer->country2 = $country;
                $customer->birthdate2 = $birthdate;
                $customer->id_gender2 = $gender;
                $customer->id_status_civil2 = $status_civil;
                $customer->updated_at = $now;
                $customer->updated_by = $user->id;

              

                $customer->save();
            }


    }


    
    
    
}