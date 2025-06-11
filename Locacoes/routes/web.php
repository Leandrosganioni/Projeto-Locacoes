<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\EquipamentoController;

use App\Http\Controllers\UsuarioController;

use App\Http\Controllers\PedidoController;


//Route::get("/login", [AuthController::class, 'showFormLogin'])->name('login');
//Route::post("/login", [AuthController::class, 'login']);


Route::get('/', [AuthController::class, 'showFormLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::middleware("auth")->group(function (){
    Route::resource("clientes", ClienteController::class);
    Route::resource('funcionarios', FuncionarioController::class);
    Route::resource('equipamentos', EquipamentoController::class);
    Route::post("/logout", [AuthController::class, "logout"])->name('logout');
});


Route::get('/register', [AuthController::class, 'showFormRegister'])->name('register');
Route::post('/create-user', [AuthController::class, 'create'])->name('createUser');


/*Route::get('/index', function () {
    return view('index'); // ou outro nome de view, se for diferente
})->name('index');
*/
Route::get('/index', function () {
    return view('index');
})->middleware('auth')->name('index');


Route::resource('usuarios', UsuarioController::class)->middleware('auth');


Route::resource('usuario', UsuarioController::class);
Route::get('/usuario/create', [UsuarioController::class, 'create'])->name('usuario.create');
Route::post('/usuario', [UsuarioController::class, 'store'])->name('usuario.store');


Route::resource('clientes', ClienteController::class);
Route::resource('funcionarios', FuncionarioController::class);
Route::resource('equipamentos', EquipamentoController::class);
Route::resource('pedidos', PedidoController::class); 

