<?php

namespace App\Repositories;

use App\Models\Motorbike;

interface MotorbikeRepositoryInterface
{
    public function create(array $data): Motorbike;
}
