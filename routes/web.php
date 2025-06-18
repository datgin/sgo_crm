<?php

use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\BirthDayController;
use App\Http\Controllers\Backend\ContractTypeController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\EmployeeController;
use App\Http\Controllers\PdfController;
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

    Route::group(['prefix' => 'employees', 'controller' => EmployeeController::class, 'as' => 'employees.'], function () {
        Route::get('/', 'index');
        Route::get('save/{id?}', 'save');
        Route::post('save', 'store');
        Route::put('save/{id}', 'update');
        Route::get('/view/{id}', 'view')->name('view');
        Route::get('/information', 'information')->name('information');
    });

    Route::group(['prefix' => 'birthdays', 'controller' => BirthDayController::class, 'as' => 'birthdays.'], function () {
        Route::get('/', 'index')->name('index');
    });

    Route::group(['prefix' => 'contactTypes', 'controller' => ContractTypeController::class, 'as' => 'contactTypes.'], function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    Route::get('/pdf-form', [PdfController::class, 'form'])->name('pdf.form');
    Route::post('/pdf-extract', [PdfController::class, 'extract'])->name('pdf.extract');
});

Route::middleware('admin.guest')->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'authenticate']);
});
