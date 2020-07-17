<?php

namespace App\Http\Controllers\API;

use App\Machine;
use App\MachineItem;
use App\User;
use App\Http\Controllers\Controller;

class UtilityController extends Controller
{

    public function reset()
    {
        User::truncate();
        Machine::truncate();
        MachineItem::truncate();
        return response()->noContent();
    }

}
