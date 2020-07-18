<?php

namespace App\Http\Controllers\API;

use App\Machine;
use App\User;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function show(User $user, Machine $machine)
    {
        $user->machine = $machine;
        return response()->json($user, 200);
    }

    /**
     * Reset the user's coins
     */
    public function reset(User $user)
    {
        $user->coins = 5;
        $user->save();
        return response()->noContent();
    }

}
