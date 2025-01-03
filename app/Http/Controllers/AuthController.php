<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    function register(Request $request){
        $data = $request->all();
        $validator= Validator::make($data,[
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email'=> 'required|string|unique:Users,email',
            'password'=> 'required|string|confirmed',
            'phone'=>'string|required|unique:Users,phone',
            'image_url'=> 'required|image|max:2048',
            'address'=>'required|string',
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all(); // Get all error messages as an array
            $errorText = implode(", ", $errorMessages); // Join the error messages into a single string

            return response()->json([
                "message" => $errorText, // Return the errors as a single string
            ], 400);
        }

        if ($request->hasFile('image_url')) {
            $photo_subject_path = $request->file('image_url')->store('images', 'public');
            $photoUrl = asset('storage/' . $photo_subject_path);
        }


        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'phone'=>$data['phone'],
            'address'=>$data['address'],
            'image_url'=>$photoUrl

        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];


        return response()->json($response,201);
    }


    public function login(Request $request){
        $fields = $request->all();
        $validator= Validator::make($fields,[
            'email' => 'required|string',
            'password' => 'required|string'
        ]);


        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all(); // Get all error messages as an array
            $errorText = implode(", ", $errorMessages); // Join the error messages into a single string

            return response()->json([
                "message" => $errorText, // Return the errors as a single string
            ], 400);
        }

        //check email
        $user = user::where('email' , $fields['email'])->first();

        //check password
        if(!$user || !Hash::check($fields['password'] , $user->password)){
            return response([
                'message' => 'login failed'
            ] , 401);

        }
        $token = $user->createToken('myapptoken')->plainTextToken;


        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response , 201);
    }

    public function logout(Request $request){

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged Out'
        ],200);
    }


}
