<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

class ProfileController extends Controller
{
    public function getProfileDetail(Request $request) {

        $user = User::query()->where('user_id', $request->user()->user_id)->first();

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => new UserResource($user->load(['semester', 'major', 'faculty']))
        ]);
    }

    public function updateProfile(Request $request) {}

    public function getNotes(Request $request) {}

    // public function updatePhoto(Request $request) {}

    // public function changePassword(Request $request) {}


}

