<?php

namespace App\Providers;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class BlockDangerousCommandsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $dangerousCommands = [
            'migrate:fresh',
            'migrate:refresh',
            'migrate:reset',
            'db:wipe',
        ];

        Event::listen(CommandStarting::class, function ($event) use ($dangerousCommands) {
            if (in_array($event->command, $dangerousCommands)) {
                $this->app->make('log')->warning("Blocked attempt to run dangerous command: {$event->command}");
                exit("⛔ This command is blocked for safety reasons.\n".
                    "Please use individual migrations or contact your administrator.\n");
            }
        });
    }
}
