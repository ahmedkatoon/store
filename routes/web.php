<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\Homecontroller;
use App\Http\Controllers\Front\CheckoutController;
use App\Http\Controllers\Front\ProductsController;
use App\Http\Controllers\Front\Auth\TwoFactotAuthentcatController;


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

Route::get('/',[Homecontroller::class,"index"])->name("home");
// Route::get('/',[DashboardController::class,"index"])->name("home");
Route::get("/products",[ProductsController::class,"index"])->name("productts.index");
Route::get("/products/{product:slug}",[ProductsController::class,"show"])->name("productts.show");
Route::resource("cart",CartController::class);
Route::get("checkout",[CheckoutController::class,"create"])->name("checkout");
Route::post("checkout",[CheckoutController::class,"store"]);



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get("auth/user/2fa",[TwoFactotAuthentcatController::class,"index"])->name("front.2fa");
// require __DIR__.'/auth.php';
require __DIR__.'/dashboard.php';
