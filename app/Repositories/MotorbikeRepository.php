<?php

namespace App\Repositories;

use App\Models\Motorbike;

class MotorbikeRepository implements MotorbikeRepositoryInterface
{
    public function create(array $data): Motorbike
    {
        return Motorbike::create($data);
    }
}
