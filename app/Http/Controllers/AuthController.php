<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // App/Http/Controllers/Api/AuthController.php
$request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users',
    'password' => 'required|string|min:4|confirmed', // Change ici de 8 à 4
]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => false, // Par défaut pas admin
        ]);

        Auth::login($user);

        return response()->json([
            'user' => $user,
            'message' => 'Inscription réussie'
        ], 201);
    }
}
