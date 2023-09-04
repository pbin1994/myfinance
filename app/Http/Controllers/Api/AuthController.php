<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($requestData,[
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $requestData['password'] = Hash::make($requestData['password']);

        $user = User::create($requestData);

        return response([ 'status' => true, 'message' => 'Пользователь зарегистрирован' ], 200);
    }

    public function change_pwd(Request $request)
    {


        $requestData = $request->all();
        //dd($requestData);
        $validator = Validator::make($requestData,[
            'old_password' => 'required|max:55',
            'password' => 'required|confirmed|min:6',
        ],
        [
           'old_password.required' => 'Введите текущий пароль',
           'password.required' => 'Введите новый пароль',
           'password.confirmed' => 'Пароли не совпадают',
           'password.min' => 'Пароль не может быть короче 6 символов'
       ]
   );


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }


        $user = $request->user();
        if(!Hash::check($request->get('old_password'), $user->password)){
            return response()->json(['errors' => ['old_password'=>['Неверный текущий пароль']] ], 401);
        }

        $user =  User::find($user->id);
        $user->password =  Hash::make($request->password);
        $user->save();

        return response([ 'status' => true, 'message' => 'Пароль успешно изменен' ], 200);
    }

    public function login(Request $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($requestData,[
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Неверный логин или пароль'
            ], 422);
        }

        if(! auth()->attempt($requestData)){
            return response()->json(['error' => 'Неверный логин или пароль'], 401);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken], 200);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json(['user' => $user], 200);
    }

    public function logout (Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}