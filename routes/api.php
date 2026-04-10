<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\PackageController;
use App\Http\Controllers\API\EnquieryController;
use App\Http\Controllers\API\TestimonialController;
use App\Http\Controllers\API\ImageuploadController;
use App\Http\Controllers\API\PackagegroupController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
Route::post('enquiery/add', [EnquieryController::class,'add']);
Route::post('category/list', [CategoryController::class,'list']);
Route::post('packagegroup', [PackagegroupController::class,'index']);
Route::post('enquiery', [EnquieryController::class,'index']);
 Route::post('package', [PackageController::class,'index']);
 Route::post('testimonial', [TestimonialController::class,'index']);
  Route::post('category/details', [CategoryController::class,'details']);
  Route::post('package/details', [PackageController::class,'details']);
  Route::post('testimonial/details', [TestimonialController::class,'details']);

Route::middleware('auth:api')->group( function () {
    Route::post('category/remove', [CategoryController::class,'remove']);
    Route::post('category/add', [CategoryController::class,'add']);
    Route::post('category/update', [CategoryController::class,'update']);
    //Route::post('category/details', [CategoryController::class,'details']);
    Route::post('category', [CategoryController::class,'index']);
    Route::post('category/statusupdate', [CategoryController::class,'statusupdate']);
    //Route::post('category/list', [CategoryController::class,'list']);
    
    Route::post('package/remove', [PackageController::class,'remove']);
    Route::post('package/add', [PackageController::class,'add']);
    Route::post('package/update', [PackageController::class,'update']);
   // Route::post('package/details', [PackageController::class,'details']);
  //  Route::post('package', [PackageController::class,'index']);
    Route::post('package/statusupdate', [PackageController::class,'statusupdate']);
    Route::post('package/upload', [PackageController::class,'upload']);
    
    
    Route::post('testimonial/remove', [TestimonialController::class,'remove']);
    Route::post('testimonial/add', [TestimonialController::class,'add']);
    Route::post('testimonial/update', [TestimonialController::class,'update']);
    //Route::post('testimonial/details', [TestimonialController::class,'details']);
   // Route::post('testimonial', [TestimonialController::class,'index']);
    
   // Route::post('enquiery', [EnquieryController::class,'index']);
    Route::post('imageupload/store', [ImageuploadController::class,'store']);
   // Route::post('packagegroup', [PackagegroupController::class,'index']);

});
