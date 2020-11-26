<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller {

    public function me()
    {
        $user = Auth::user();

        return response()->json($user);
    }
}