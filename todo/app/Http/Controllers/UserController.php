<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6']
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'email or password uncorrect',
            ], Response::HTTP_FORBIDDEN);
        }

        if ($user && Hash::check($request->password, $user->password)) {
            $userToken = $user->createToken('login');
            return response()->json([
                'message' => "Weclome $user->name",
                'token' => $userToken->plainTextToken
            ]);
        }
    }
}
