<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //creamos una instancia de AuthController
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255', //validamos que el email sea requerido, string, email y máximo de 255 caracteres
            'password' => 'required|string|min:4', //validamos que la contraseña sea requerida, string y mínimo de 4 caracteres
        ]);

        if ($validator->fails()) { //si la validación falla
            return response()->json($validator->errors(), 422); //devuelve los errores de validación
        }

        if (! $token = auth()->attempt($validator->validated())) { //si no se genera el token
            return response()->json(['error' => 'Unauthorized'], 401); //devuelve un error 401
        }

        return $this->createNewToken($token); //si se genera el token, llamamos a la función createNewToken
    }

    public function register (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100', //validamos que el nombre sea requerido, string y entre 2 y 100 caracteres
            'email' => 'required|string|email|max:100|unique:users', //validamos que el email sea requerido, string, email, máximo de 100 caracteres y único en la tabla users
            'password' => 'required|string|min:4', //validamos que la contraseña sea requerida, string, confirmada y mínimo de 4 caracteres
            'dni'=> 'required|string|between:2,10',
            'rol'=> 'required|string|between:2,100',
            'especialidad'=> 'between:0,100',
        ]);


        if($validator->fails()){ //si la validación falla
            return response()->json($validator->errors()->toJson(), 400); //devuelve los errores de validación
        }

        $user = User::create(array_merge( //creamos un usuario con los datos del request
            $validator->validated(), //validamos los datos del request
            ['password' => bcrypt($request->password)]//encriptamos la contraseña

        ));
        if ($request->rol == 'doctor') {
            $user->especialidades()->attach($request->especialidad);
        }

        return response()->json([ //devuelve un json con los datos del usuario y el token
            'message' => 'Registro Correcto',
            'user' => $user,
            'rol' => $user->rol,
            'token' => $this->createNewToken($user->token)
        ], 201);
    }

    public function logout ()
    {
        auth()->logout(); //cerramos la sesión
        return response()->json(['message' => 'Cierre de sesión correcto']); //devuelve un json con un mensaje
    }


    public function  refresh()
    {
        return $this->createNewToken(auth()->refresh()); //llamamos a la función createNewToken
    }


    public function userProfile ()
    {
        return response()->json(auth()->user()); //devolvemos un json con los datos del usuario
    }


    protected function createNewToken ($token)
    {
        return response()->json([ //devuelve un json con el token, el tipo de token y la fecha de expiración
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL()*60,
            'user' => auth()->user()
        ]);
    }


}
