<?php

namespace App\Livewire\FluxAdmin\Pages\Dev;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Queue monitor — Flux Admin')]
class QueueMonitorIndex extends Component
{
    use WithAuthorization;

    public string $queueName = 'default';

    public function mount(): void { $this->authorizeModule('see-menu-admin'); }

    public function render()
    {
        $jobs    = [];
        $error   = null;
        $prefix  = config('database.redis.options.prefix', '');
        $redisKey = "queues:{$this->queueName}:delayed";

        try {
            $rawJobs = Redis::zrangebyscore($redisKey, '-inf', '+inf', ['withscores' => true]);

            foreach ($rawJobs as $payload => $score) {
                try {
                    $decoded = json_decode($payload, true);
                    $jobs[]  = [
                        'id'        => $decoded['id']           ?? 'n/a',
                        'name'      => $decoded['displayName']  ?? $decoded['job'] ?? 'Unknown',
                        'attempts'  => $decoded['attempts']     ?? 0,
                        'queue'     => $decoded['queue']        ?? $this->queueName,
                        'available' => $score,
                        'available_human' => now()->diffForHumans(\Carbon\Carbon::createFromTimestamp($score), true).' away',
                    ];
                } catch (\Throwable $e) {
                    // skip malformed payload
                }
            }

            usort($jobs, fn ($a, $b) => $a['available'] <=> $b['available']);
        } catch (\Throwable $e) {
            Log::warning('Queue monitor failed', ['error' => $e->getMessage()]);
            $error = 'Could not connect to Redis: '.$e->getMessage();
        }

        return view('flux-admin.pages.dev.queue-monitor', compact('jobs', 'error'));
    }
}
