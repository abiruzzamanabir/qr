<?php

use App\Http\Controllers\QRCodeController;
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


Route::get('/', [QRCodeController::class, 'createForm'])->name('form');
Route::post('/generate', [QRCodeController::class, 'generate'])->name('generate');

// routes/web.php

Route::get('/show-qr/{user}', [QRCodeController::class, 'showQRCode'])->name('show.qr');
