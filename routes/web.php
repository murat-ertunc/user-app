<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Auth;
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

Auth::routes([
    'register' => false,    // Disable registration
    'reset' => false,   // Disable password reset
    'confirm' => false, // Disable email verification
    'verify' => false,  // Disable email verification
    'login' => true,    // Enable login
]); //auth routes (login, register, password reset, etc.)

Route::get('/logout', function(){
    Auth::logout();
    return redirect()->route('login');
})->name('logout')->middleware('auth'); //logout route

//User routes
Route::controller(MainController::class)->middleware('auth')->group(function () {
    Route::get('/', 'index')->name('index'); //index page
    Route::post('/customers', 'customers')->name('customers'); //customers list api
    Route::post('/customer/{id}', 'customer')->name('customer')->middleware(['role:admin']); //customer detail api
    Route::post('/store-customer', 'storeCustomer')->name('storeCustomer')->middleware(['role:admin']); //store customer api
    Route::post('/delete-customer/{id}', 'deleteCustomer')->name('deleteCustomer')->middleware(['role:admin']); //delete customer api
    Route::post('/import', 'import')->name('import')->middleware(['role:admin']); //import api
});
