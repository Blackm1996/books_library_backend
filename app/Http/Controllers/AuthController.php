<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/*
    SESSION_DRIVER=cookie
CLIENT_URL=http://localhost:3000
SESSION_DOMAIN=localhost
SANCTUM_STATEFUL_DOMAINS=http://localhost:3000
*/
class AuthController extends Controller
{
    public function login(Request $request)
    {

        /*try {*/
            $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ]);
        /*} catch (\Exception $th) {
            return response()->json(["Error Verification"=>$th],422);
        }*/
        $credentials = $request->only('email', 'password');

        $ifuser = Auth::attempt($credentials);
        if (!$ifuser) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $ability = "user";
        switch ($user->role) {
            case 2020:
                $ability = "user";
                break;

            case 2006:
                $ability = "admin";
                break;

            case 1996:
                $ability = "superAdmin";
                break;
        }
        $token = $user->createToken('booksApp',[$ability])->plainTextToken;
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'role' => $ability,
                    'type' => 'bearer',
                ]
            ]);
    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'role' => ['required', Rule::in([2020, 2006, 1996]),],
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
        ],201);

    }

    public function logout()
    {
        try {
            /** @var \App\Models\User $user **/
            $user = Auth::user();
            $user->tokens()->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out',
            ]);
        } catch (\Exception $e) {
            return response()->json($e, 404);
        }


    }

    public function index()
    {
        $users = DB::table('users')->select("id","name","email", "role")
            ->where('role','!=',1996)->get();
        return response()->json($users,200);
    }
}
