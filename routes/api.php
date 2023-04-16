<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\EspecialidadController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


//todo: faltan los middleware

//Doctores------------------------------------------------

Route::get('/doctores',[DoctorController::class, 'getDoctores']);
Route::get('/doctores/{id}',[DoctorController::class ,'getDoctor']);
Route::post('/doctores/crear', [DoctorController::class, 'crear']);
Route::put('/doctores/{id}', [DoctorController::class, 'update']);
Route::delete('/doctores/{id}', [DoctorController::class, 'delete']);
Route::get('/especialidadDoctor/{id}', [DoctorController::class, 'getEspecialidades']);


//Pacientes------------------------------------------------

Route::get('/pacientes',[PacienteController::class, 'getPacientes']);
Route::get('/pacientes/{id}',[PacienteController::class ,'getPaciente']);
Route::post('/pacientes/crear', [PacienteController::class, 'crear']);
Route::put('/pacientes/{id}', [PacienteController::class, 'update']);
Route::delete('/pacientes/{id}', [PacienteController::class, 'delete']);
Route::get('/pacientes/{id}/citas', [PacienteController::class, 'getCitas']);


//Citas------------------------------------------------

Route::get('/citas',[CitaController::class, 'getCitas']);
Route::get('/citas/{id}',[CitaController::class ,'getCita']);
Route::post('/citas/agenda', [CitaController::class, 'agenda']);
Route::put('/citas/{id}', [CitaController::class, 'update']);
Route::delete('/citas/{id}', [CitaController::class, 'delete']);
Route::get('/citas/citasCanceladas', [CitaController::class, 'getCitasCanceladas']);
Route::get ('/citas/citasCanceladas/{id}', [CitaController::class, 'getCitaCancelada']);
Route::post('/citas/cancelarCita/{id}', [CitaController::class, 'cancelarCita']);
Route::get('/citas/citasPendientes', [CitaController::class, 'getCitasPendientes']);
Route::get('/citas/citasDoctor/{id}', [CitaController::class, 'getCitasDoctor']);
Route::get('/citas/citasPaciente/{id}', [CitaController::class, 'getCitasPaciente']);
Route::get('/citas/citasEspecialidad/{id}', [CitaController::class, 'getCitasEspecialidad']);


//Especialidades------------------------------------------------

Route::get('/especialidades',[EspecialidadController::class, 'getEspecialidades']);
Route::get('/especialidades/{id}',[EspecialidadController::class ,'getEspecialidad']);
Route::post('/especialidades/crear', [EspecialidadController::class, 'crear']);
Route::put('/especialidades/{id}', [EspecialidadController::class, 'update']);
Route::delete('/especialidades/{id}', [EspecialidadController::class, 'delete']);


