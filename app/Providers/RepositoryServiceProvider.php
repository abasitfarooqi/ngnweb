<?php

namespace App\Providers;

use App\Repositories\MotorbikeRepository;
use App\Repositories\MotorbikeRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(MotorbikeRepositoryInterface::class, MotorbikeRepository::class);
    }
}
