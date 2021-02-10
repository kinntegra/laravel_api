<?php

use App\Http\Controllers\Associate\AssociateAddressController;
use App\Http\Controllers\Associate\AssociateController;
use App\Http\Controllers\Associate\AssociateEmployeeController;
use App\Http\Controllers\Associate\AssociateUserController;
use App\Http\Controllers\Associate\EmployeeController;
use App\Http\Controllers\Client\LeadController;
use App\Http\Controllers\External\ExternalAssociateController;
use App\Http\Controllers\External\ExternalEmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Master\AddresstypeController;
use App\Http\Controllers\Master\BankcodeController;
use App\Http\Controllers\Master\CommercialController;
use App\Http\Controllers\Master\CommercialtypeController;
use App\Http\Controllers\Master\CountryController;
use App\Http\Controllers\Master\EntitytypeController;
use App\Http\Controllers\Master\HolidayController;
use App\Http\Controllers\Master\ProfessionController;
use App\Http\Controllers\Master\RelationController;
use App\Http\Controllers\Master\StateController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\DesignationController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\User\PasswordResetController;
use App\Http\Controllers\User\UserAssociateController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserEmployeeController;
use App\Models\Associate\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'master'], function () {
    Route::resource('addresstype', AddresstypeController::class, ['except' => ['destroy', 'create']]);
    Route::resource('countries', CountryController::class, ['only' => ['index']]);
    Route::resource('countries.states', StateController::class, ['only' => ['index']]);
    Route::resource('holiday', HolidayController::class, ['except' => ['create']]);
    Route::resource('profession', ProfessionController::class, ['only' => ['index']]);
    Route::resource('commercial', CommercialController::class, ['only' => ['index']]);
    Route::resource('commercialtype', CommercialtypeController::class, ['only' => ['index']]);
    Route::resource('entitytype', EntitytypeController::class, ['only' => ['index']]);
    Route::resource('bankcode', BankcodeController::class, ['only' => ['index', 'store', 'show']]);
    Route::resource('relations', RelationController::class, ['only' => ['index']]);
    Route::resource('department', DepartmentController::class, ['only' => ['index']]);
    Route::resource('designation', DesignationController::class, ['only' => ['index']]);
});
Route::resource('associate', AssociateController::class);
Route::resource('external_associate', ExternalAssociateController::class, ['only' => ['show', 'store']]);
Route::resource('external_employee', ExternalEmployeeController::class, ['only' => ['show', 'store']]);
//Route::get('associate/{id}/details', [AssociateController::class, 'details']);
Route::resource('associate.address', AssociateAddressController::class, ['only' => ['index']]);
Route::resource('employee', EmployeeController::class);
Route::resource('associate.employee', AssociateEmployeeController::class);
Route::resource('associate.user', AssociateUserController::class, ['only' => ['index']]);
//Excel Download
Route::get('associate/download/{id}', [AssociateController::class, 'download']);
Route::get('employee/download/{id}', [EmployeeController::class, 'download']);
//Get Logs
Route::get('associate/logs/{id}', [AssociateController::class, 'getLogs']);
Route::get('employee/logs/{id}', [EmployeeController::class, 'getLogs']);
//Build the Route for Login, Reset Password, Reset PIN,
Route::resource('users', UserController::class, [ 'only' => [ 'index', 'show'] ]);
Route::resource('user.associate', UserAssociateController::class, ['only' => ['index']]);
Route::resource('user.employee', UserEmployeeController::class, ['only' => ['index']]);
//Client MOdule
Route::resource('lead', LeadController::class);

Route::group(['prefix' => 'user'], function(){
    Route::get('/', [UserController::class, 'user']);
    Route::get('checkusername', [UserController::class, 'checkUserName']);
    Route::get('checkauthuserpanno', [UserController::class, 'checkAuthUserPanNo']);
    Route::get('checkauthusermobile', [UserController::class, 'checkAuthUserMobile']);
    Route::get('checkauthuseremail', [UserController::class, 'checkAuthUserEmail']);

    Route::get('logout', [UserController::class, 'logout']);
    Route::group(['prefix' => 'password-pin'], function(){
        Route::post('create', [PasswordResetController::class, 'create']);
        Route::get('find/{token}', [PasswordResetController::class, 'find']);
        Route::post('reset', [PasswordResetController::class, 'reset']);
        Route::post('reset/first', [PasswordResetController::class, 'resetfirst']);
    });
});

Route::post('testfile', [HomeController::class, 'index']);
Route::get('generate-pdf',[HomeController::class, 'generatePDF']);
Route::get('test-email', [HomeController::class, 'testEmail']);
Route::resource('testcase', TestController::class, ['only' => ['index']]);
