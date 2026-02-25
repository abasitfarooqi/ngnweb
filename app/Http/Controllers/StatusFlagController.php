<?php

namespace App\Http\Controllers;

use App\Models\StatusFlag;

class StatusFlagController extends Controller
{
    public function getColor($status)
    {
        return StatusFlag::getColor($status);
    }
}
