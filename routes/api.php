<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\Maintenance\EmailController;
use App\Http\Controllers\V1\Leads\LeadController;
use App\Http\Controllers\V1\Maintenance\MaintenanceController;
use App\Http\Controllers\V1\Projects\ProjectController;
use App\Http\Controllers\V1\Projects\UnitController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::prefix('v1')->group(function () {
  
    Route::post('login', [AuthController::class, 'authenticate']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('recovery-password', [EmailController::class, 'recoveryPassword']);


    Route::group(['middleware' => ['jwt.verify','block.bots']], function() {
       
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('get-user', [AuthController::class, 'getUser']);

      
    });


     Route::group(['prefix' => 'leads','middleware' => ['jwt.verify']], function () {

            Route::post('add-interaction', [LeadController::class, 'addInteraction']);
            Route::put('assign-user', [LeadController::class, 'assignUser']);
            Route::put('add-spouse', [LeadController::class, 'addSpouse']);
            Route::post('add-customer', [LeadController::class, 'addCustomer']);
            
     
    });



     Route::group(['prefix' => 'maintenance','middleware' => ['jwt.verify']], function () {

            
            Route::get('table', [MaintenanceController::class, 'getTable']);

            Route::get('ubigeo/department', [MaintenanceController::class, 'getDepartment']);
            Route::get('ubigeo/province', [MaintenanceController::class, 'getProvince']);
            Route::get('ubigeo/district', [MaintenanceController::class, 'getDistrict']);
           
            

           

     
    });



     Route::group(['prefix' => 'projects','middleware' => ['jwt.verify']], function () {

            
            Route::get('list', [ProjectController::class, 'listProjects']);

             Route::get('edit', [ProjectController::class, 'editProject']);

             Route::post('save', [ProjectController::class, 'saveProject']);

             Route::post('files/upload', [ProjectController::class, 'uploadFiles']);

             Route::delete('files/delete', [ProjectController::class, 'deletedFiles']);

              Route::get('files/download', [ProjectController::class, 'downloadFiles']);
     
    });

     Route::group(['prefix' => 'units','middleware' => ['jwt.verify']], function () {

            
            Route::get('list', [UnitController::class, 'listUnits']);

             Route::get('get-details', [UnitController::class, 'getDetails']);

            

           
     
    });

});