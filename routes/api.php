<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\api\admin\LoginController;
use App\Http\Controllers\Api\Admin\SliderController;
use App\Http\Controllers\Api\Admin\InvoiceController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\DashboardController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('admin')->group(function(){
    // Route login
    Route::post('/login', [LoginController::class,'index',['as'=>'admin']]);
    //group route with middleware "auth:api_admin"
    Route::group(['middleware'=>'auth:api_admin'],function(){
        //data user
        Route::get('/user', [LoginController::class, 'getUser', ['as' => 'admin']]);

        //refresh token JWT
        Route::get('/refresh', [LoginController::class, 'refreshToken', ['as' => 'admin']]);

        //logout
        Route::post('/logout', [LoginController::class, 'logout', ['as' => 'admin']]);

        //dashboard
        Route::get('/dashboard', [DashboardController::class, 'index',['as' => 'admin']]);
    
        //categories resources
        Route::apiResource('/categories', CategoryController::class,['except' => ['create','edit'],'as'=>'admin']);

        //products resources
        Route::apiResource('/products', ProductController::class,['except' => ['create','edit'],'as'=>'admin']);

        //invoices resource
        Route::apiResource('/invoices', InvoiceController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'admin']);

        //customer
        Route::get('/customers',[CustomerController::class,'index', ['as' => 'admin']] );

        //sliders resource
        Route::apiResource('/sliders', SliderController::class, ['except' => ['create', 'show', 'edit', 'update'], 'as' => 'admin']);

        //users resource
        Route::apiResource('/users', UserController::class, ['except' => ['create', 'edit'], 'as' => 'admin']);
    });
    
    
});



//group route with prefix "customer"
Route::prefix('customer')->group(function () {

    //route register
    Route::post('/register', [App\Http\Controllers\Api\Customer\RegisterController::class, 'store'], ['as' => 'customer']);

    //route login
    Route::post('/login', [App\Http\Controllers\Api\Customer\LoginController::class, 'index'], ['as' => 'customer']);

    //group route with middleware "auth:api_customer"
    Route::group(['middleware' => 'auth:api_customer'], function() {

        //data user
        Route::get('/user', [App\Http\Controllers\Api\Customer\LoginController::class, 'getUser'], ['as' => 'customer']);

        //refresh token JWT
        Route::get('/refresh', [App\Http\Controllers\Api\Customer\LoginController::class, 'refreshToken'], ['as' => 'customer']);

        //logout
        Route::post('/logout', [App\Http\Controllers\Api\Customer\LoginController::class, 'logout'], ['as' => 'customer']);

        //dashboard
        Route::get('/dashboard', [App\Http\Controllers\Api\Customer\DashboardController::class,'index'], ['as' => 'customer']);

        //invoice resource
        Route::apiResource('/invoices', App\Http\Controllers\Api\Customer\InvoiceController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'customer']);

        Route::post('/reviews',[\App\Http\Controllers\Api\Customer\ReviewController::class,'store'],['as' => 'customer']);
    });

});

//group route with prefix "web"
Route::prefix('web')->group(function () {

    //categories resource
    Route::apiResource('/categories', App\Http\Controllers\Api\Web\CategoryController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'web']);

    //products resource
    Route::apiResource('/products', App\Http\Controllers\Api\Web\ProductController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'web']);

    Route::get('/sliders',[App\Http\Controllers\Api\Web\SliderController::class,'index'],['as' => 'web']);

    //raja ongkir
    Route::get('/rajaongkir/provinces', [App\Http\Controllers\Api\Web\RajaOngkirController::class,'getProvinces'],['as' => 'web']);

    Route::post('/rajaongkir/cities', [App\Http\Controllers\Api\Web\RajaOngkirController::class,'getCities'],['as' => 'web']);

    Route::post('/rajaongkir/checkOngkir', [App\Http\Controllers\Api\Web\RajaOngkirController::class,'checkOngkir'],['as' => 'web']);

    //get cart 
    Route::get('/carts', [App\Http\Controllers\Api\Web\CartController::class, 'index'], ['as' => 'web']);

    //store cart
    Route::post('/carts', [App\Http\Controllers\Api\Web\CartController::class, 'store'], ['as' => 'web']);
        
    //get cart price
    Route::get('/carts/total_price', [App\Http\Controllers\Api\Web\CartController::class, 'getCartPrice'], ['as' => 'web']);

    //get cart weight
    Route::get('/carts/total_weight', [App\Http\Controllers\Api\Web\CartController::class, 'getCartWeight'], ['as' => 'web']);

    //remove cart
    Route::post('/carts/remove', [App\Http\Controllers\Api\Web\CartController::class, 'removeCart'], ['as' => 'web']);

    //checkout route
    Route::post('/checkout', [App\Http\Controllers\Api\Web\CheckoutController::class, 'store'], ['as' => 'web']);

    //notification handler route
    Route::post('/notification', [App\Http\Controllers\Api\Web\NotificationHandlerController::class, 'index'], ['as' => 'web']);

});



