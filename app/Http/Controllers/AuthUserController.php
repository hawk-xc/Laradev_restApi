<?php

namespace App\Http\Controllers;

use \App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthUserController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => false,
            'data' => 'unauthorized'
        ], 404);
    }

    public function login(Request $request)
    {
        $data = $request->only(['email', 'password']);
        $ability = [];

        switch (User::where('email', $request->email)->first()->role) {
            case 'admin':
                $ability = ['get-product', 'update-product', 'delete-product', 'create-product'];
                break;
            case 'user':
                $ability = ['get-product'];
                break;
            default:
                $ability = ['get-product'];
        }

        if (Auth::attempt($data)) {
            return response()->json([
                'status' => true,
                'data' => 'user successfull auth',
                'token' => $request->user()->createToken('api-auth', $ability)->plainTextToken
            ]);
        }
    }

    public function register(Request $request)
    {
        $data = $request->only(['name', 'email', 'password']);

        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => true,
                'data' => $validator->errors()
            ]);
        } else {
            $data['password'] = bcrypt($request['password']);
            User::create($data);

            return response()->json([
                'status' => true,
                'data' => 'user successfull saved!'
            ]);
        }
    }
}
