<?php

use App\Http\Controllers\Backend\CheckInOutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'attendance', 'controller' => CheckInOutController::class, 'as' => 'attendance.'], function () {
    Route::post('/checkin', 'checkin');
    Route::post('/checkout', 'check)ut');
});
