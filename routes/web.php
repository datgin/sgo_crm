<?php

use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\BirthDayController;
use App\Http\Controllers\Backend\BulkActionController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ContractTypeController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\EmployeeController;
use App\Http\Controllers\Backend\MediaItemController;

use App\Http\Controllers\Backend\MonthlyWorkdayController;
use App\Http\Controllers\Backend\PayrollController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\SettingController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('admin.auth')->group(function () {

    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::post('handle-bulk-action', [BulkActionController::class, 'handleBulkAction']);

    Route::get('logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'employees', 'controller' => EmployeeController::class, 'as' => 'employees.'], function () {
        Route::get('/', 'index');
        Route::get('save/{id?}', 'save');
        Route::post('save', 'store');
        Route::put('save/{id}', 'update');
        Route::get('/view/{id}', 'view')->name('view');
        Route::get('/information', 'information')->name('information');
        Route::get('permissions', 'showPermissionForm');
        Route::post('permissions', 'assignPermissions');
    });

    Route::group([
        'prefix' => 'permissions',
        'controller' => PermissionController::class,
        'as' => 'permissions.'
    ], function () {
        Route::get('/', 'index');
        Route::get('{id}', 'show');
        Route::post('/', 'store');
        Route::put('/', 'update');
    });

    Route::group(['prefix' => 'media', 'controller' => MediaItemController::class], function () {
        Route::get('/', 'list');
        Route::post('upload', 'upload');
        Route::delete('destroy', 'destroy');
    });


    Route::group(['prefix' => 'birthdays', 'controller' => BirthDayController::class, 'as' => 'birthdays.'], function () {
        Route::get('/', 'index')->name('index');
    });

    Route::group(['prefix' => 'contactTypes', 'controller' => ContractTypeController::class, 'as' => 'contactTypes.'], function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });


    Route::group(['prefix' => 'categorys', 'controller' => CategoryController::class, 'as' => 'categorys.'], function () {
        Route::get('/', 'index')->name('index');
        Route::post('/excel-save', 'updateOrCreate');
        Route::delete('/excel-delete', 'destroy');
    });


    Route::group(['prefix' => 'monthly-workdays', 'controller' => MonthlyWorkdayController::class, 'as' => 'monthlyWorkdays.'], function () {
        Route::get('/', 'index')->name('index');
        Route::post('{id}/update-workdays', [MonthlyWorkdayController::class, 'updateWorkdays']);


    });



    Route::group(['prefix' => 'settings', 'controller' => SettingController::class], function () {
        Route::get('/', 'index');
        Route::post('/', 'save');
    });

});


Route::middleware('admin.guest')->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'authenticate']);
});
