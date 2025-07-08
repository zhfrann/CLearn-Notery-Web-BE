<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:6'],
        ]);

        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($validated['username']) . '&background=random';

        $response = Http::get($avatarUrl);
        if (!$response->ok() || !$response->body()) {
            throw new HttpResponseException(response([
                'success' => false,
                'message' => 'Gagal mengambil avatar default dari layanan eksternal.',
            ], 500));
        }

        $avatarContents = $response->body();
        $filename = 'foto_profil/' . $validated['username'] . '.png';
        Storage::disk('public')->put($filename, $avatarContents);

        $user = User::query()->create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'status_akun' => 'aktif',
            'foto_profil' => $filename
        ]);

        return response()->json([
            'success' => true,
            'message' => "Register $user->username berhasil",
            // 'data' => $user->toArray()
            'data' => new UserResource($user->load(['semester', 'major', 'faculty']))
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        $user = User::query()->where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Username atau password salah.'
            ], 401));
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => "Login berhasil",
            'data' => [
                // 'user' => [
                //     'user_id' => $user->user_id,
                //     'username' => $user->username,
                //     'email' => $user->email,
                //     'foto_profil' => $user->foto_profil_url,
                //     'role' => $user->role,
                //     'status_akun' => $user->status_akun,
                //     'semester' => $user->semester,
                //     'major' => $user->major,
                //     'faculty' => $user->faculty,
                //     'rating' => $user->rating,
                //     'matkul_favorit' => $user->matkul_favorit,
                // ],
                'user' => new UserResource($user->load(['semester', 'major', 'faculty'])),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.'
        ]);
    }
}
