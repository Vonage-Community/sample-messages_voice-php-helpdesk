<?php

use App\Http\Controllers\IncomingSmsTicketController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::get('/dashboard', [TicketController::class, 'index'])->name('ticket.index');
Route::get('/tickets/create', [TicketController::class, 'create'])->name('ticket.create');
Route::post('/tickets/create', [TicketController::class, 'store'])->name('ticket.store');
Route::get('/tickets/show/{ticket:id}', [TicketController::class, 'show'])->name('ticket.show');
Route::post('/tickets/webhook', [IncomingSmsTicketController::class, 'store'])->name('ticket.webhook');
Route::post('tickets/update/{ticket:id}', [TicketController::class, 'update'])->name('ticket.update');

require __DIR__.'/auth.php';
