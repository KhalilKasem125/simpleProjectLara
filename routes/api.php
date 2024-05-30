<?php

use App\Http\Controllers\Api\AdminsControlller;
use App\Http\Controllers\Api\BooksControlller;
use App\Http\Controllers\Api\CourcesControlller;
use App\Http\Controllers\Api\ExamsControlller;
use App\Http\Controllers\Api\FilesControlller;
use App\Http\Controllers\Api\OptionsControlller;
use App\Http\Controllers\Api\QuestionsControlller;
use App\Http\Controllers\Api\StudyingPlaneControlller;
use App\Http\Controllers\Api\SubjectsControlller;
use App\Http\Controllers\Api\TeachersControlller;
use App\Http\Controllers\Api\UserControlller;
use App\Http\Controllers\Api\VideoControlller;
use App\Http\Controllers\Api\WebTestControlller;
use App\Http\Controllers\ApiAccessControlController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post("registration/user",[UserControlller::class,'register']);
Route::post("login/user",[UserControlller::class,'login']);

Route::post("login/admin",[AdminsControlller::class,'AdminLogin']);
Route::post("registration/super_admin",[AdminsControlller::class,'SuperAdminRegister']);

    //JWT-Authentication that user should use
Route::group(["middleware"=>["auth:api"]],function(){
    //user Routes
    Route::get("profile/user",[UserControlller::class,"profile"]);
    Route::get("logout/user",[UserControlller::class,"logout"]);
    Route::get("refresh-token/user",[UserControlller::class,"refreshToken"]);
    Route::get("getBooks/user/{id}",[BooksControlller::class,'getBooks']);
    Route::get("show-teachers-info/user/{id}",[TeachersControlller::class,"showTeachersDetailsForStudents"]);
    Route::get('retriveTHEPlanes/user/{id}',[StudyingPlaneControlller::class,'getPhotos']);
    Route::get('retrivePdf/user/{id}',[FilesControlller::class,'getPdf']);
    Route::get("getVideos/user/{id}",[VideoControlller::class,'getVideos']);
    Route::get('getExamTemplate/{id}',[ExamsControlller::class,'getExamTemplate'])->middleware('time_limited_access');
    Route::post('Submit/user/{id}',[ExamsControlller::class,'submitExam'])->middleware(['time_limited_access']);
    // Route::post('Submit/user/{id}',[ExamsControlller::class,'submitExam'])->middleware(['time_limited_access','check_half_time']);

});

    //JWT-Authentication that Adimns should use
Route::group(["middleware"=>["auth:admin-api"]],function()
{

    //Super Admin Routes only
    Route::group(["middleware"=>["super_admin"]],function(){

        Route::get("show-admins-Info",[AdminsControlller::class,'AdminsInformationsShowing']);
        Route::delete("delete-admin/{id}",[AdminsControlller::class,'AdminDelete']);
        Route::post("registration/admin",[AdminsControlller::class,'AdminRegister']);

    });

    //Admins And Super_Admins Routes
    Route::group(["middleware"=>["authorization"]],function()
    {
        Route::get("profile/admin",[AdminsControlller::class,"profile"]);
        Route::get("logout/admin",[AdminsControlller::class,"logout"]);
        Route::get("refresh-token/admin",[AdminsControlller::class,"refreshToken"]);
        Route::post("addSubject",[SubjectsControlller::class,"addSubject"]);
        Route::get("getSingleSubject/admin/{id}",[SubjectsControlller::class,"getSingleSubject"]);
        Route::get("getSubjects/admin",[SubjectsControlller::class,"getSubjects"]);
        Route::delete("deleteSubject/{id}",[SubjectsControlller::class,"deleteSubject"]);
        Route::post("insertVideo/{id}",[VideoControlller::class,'insertVideo']);
        Route::get("getVideos/admin/{id}",[VideoControlller::class,'getVideos']);
        Route::post('storePdf/{id}',[FilesControlller::class,'storePdf']);
        Route::get('retrivePdf/admin/{id}',[FilesControlller::class,'getPdf']);
        Route::post("store-book/{id}",[BooksControlller::class,"storeBook"]);
        Route::get("getBooks/admin/{id}",[BooksControlller::class,'getBooks']);
        Route::post('insertAPlne/{id}',[StudyingPlaneControlller::class,'insertPhoto']);
        Route::get('retriveTHEPlanes/admin/{id}',[StudyingPlaneControlller::class,'getPhotos']);
        Route::get("showingAllTeachers",[TeachersControlller::class,"showingAllTeachers"]);
        Route::delete("deleteTeacher/admin/{id}",[TeachersControlller::class,"deleteTeacher"]);
        Route::post("addTeacher/admin/{id}",[TeachersControlller::class,"addTeacherfromSubject"]);
        Route::get("show-teachers-info/admin/{id}",[TeachersControlller::class,"showTeachersDetailsForAdmins"]);
        Route::post('setOption/{id}',[OptionsControlller::class,'setOption']);
        Route::post('addQuestion/{id}',[QuestionsControlller::class,'setQuestion']);
        Route::post('setExam/{id}',[ExamsControlller::class,'setExam']);
        Route::get('getExamTemplateForWeb/{id}',[ExamsControlller::class,'getExamTemplateForWeb']);
        Route::delete('deleteBook/{id}',[BooksControlller::class,'deleteBook']);
        Route::delete('/pdfs/{id}', [FilesControlller::class,'deletePdf']);
        Route::delete('/subjects/exams/{examId}', [ExamsControlller::class,'deleteExam']);
        Route::delete('deleteOption/{option_id}',[OptionsControlller::class,'deleteOption']);
        Route::delete('deleteQuestion/{question_id}',[QuestionsControlller::class,'deleteَQuestion']);
        Route::delete('deleteVideo/{vid_id}',[VideoControlller::class,'deleteVideo']);
        Route::delete('deletePhoto/{photo_id}',[StudyingPlaneControlller::class,'deletePhoto']);
        Route::get('getExamTemplate/{id}/admin',[ExamsControlller::class,'getExamTemplate']);

    });
});


Route::get('getExam/{id}',[ExamsControlller::class,'getExams']);
Route::get('getQuestions/{id}',[QuestionsControlller::class,'getQuestions']);
Route::get('getOptions/{id}',[OptionsControlller::class,'getOptions']);
Route::get('getSub/{id}',[SubjectsControlller::class,'getOptions']);



