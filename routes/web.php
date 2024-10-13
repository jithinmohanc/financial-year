<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});
Route::get('/financial-ui', [FinancialController::class, 'financialUi'])->name('financial_ui');
Route::post('/financial-update', [FinancialController::class, 'financialUpdate'])->name('financial_update');
Route::get('/financial-holidays/{year}/{country}', [FinancialController::class, 'financialHolidays'])->name('financial_holidays');
