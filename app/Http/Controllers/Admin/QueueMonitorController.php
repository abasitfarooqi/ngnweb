<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class QueueMonitorController
{
    public function index(Request $request)
    {
        $queueName = $request->get('queue', 'default');
        $prefix = config('database.redis.options.prefix', '');

        // Laravel Redis facade automatically applies prefix, so we use key WITHOUT prefix
        // The actual key in Redis will be: {prefix}queues:{queue}:delayed
        $redisKeyWithoutPrefix = "queues:{$queueName}:delayed";
        $redisKeyWithPrefix = "{$prefix}queues:{$queueName}:delayed";

        $jobs = [];
        $debugInfo = [
            'queue_name' => $queueName,
            'prefix' => $prefix,
            'key_without_prefix' => $redisKeyWithoutPrefix,
            'key_with_prefix' => $redisKeyWithPrefix,
            'redis_connected' => false,
            'key_exists' => false,
            'raw_jobs_count' => 0,
            'error' => null,
            'alternative_keys_tried' => [],
        ];

        try {
            // Test Redis connection
            try {
                Redis::ping();
                $debugInfo['redis_connected'] = true;
            } catch (\Exception $e) {
                $debugInfo['error'] = 'Redis connection failed: ' . $e->getMessage();
                Log::error('Queue monitor Redis connection error', $debugInfo);
            }

            // Try different key formats (Laravel adds prefix automatically)
            $alternativeKeys = [
                $redisKeyWithoutPrefix, // Let Laravel add prefix automatically
                "queues:{$queueName}:delayed",
            ];

            $rawJobs = [];
            $usedKey = null;

            // Use Queue's Redis connection - this is what Laravel uses for queues
            // Predis requires ['withscores' => true] as an array option, not a string
            try {
                $queueRedis = Queue::getRedis();
                $rawJobs = $queueRedis->zrange($redisKeyWithoutPrefix, 0, -1, ['withscores' => true]);
                $usedKey = $redisKeyWithoutPrefix;
                $debugInfo['method_used'] = 'queue_redis_with_array_option';
                $debugInfo['actual_key_used'] = $usedKey;
            } catch (\Exception $e) {
                // Fallback: try with string parameter (phpredis format)
                try {
                    $rawJobs = Redis::zrange($redisKeyWithoutPrefix, 0, -1, ['withscores' => true]);
                    $usedKey = $redisKeyWithoutPrefix;
                    $debugInfo['method_used'] = 'redis_facade_array_option';
                    $debugInfo['actual_key_used'] = $usedKey;
                } catch (\Exception $e2) {
                    $debugInfo['error'] = 'Failed to fetch jobs: ' . $e2->getMessage();
                    Log::error('Queue monitor fetch error', ['error' => $e2->getMessage()]);
                }
            }

            // Check if key exists (using key without prefix, Laravel will add it)
            if ($usedKey) {
                try {
                    $keyExists = Redis::exists($usedKey);
                    $debugInfo['key_exists'] = (bool) $keyExists;
                } catch (\Exception $e) {
                    // Ignore
                }
            }

            $debugInfo['raw_jobs_count'] = count($rawJobs);
            $debugInfo['actual_key_used'] = $usedKey ?? $redisKeyWithoutPrefix;

            // Debug: Check structure of raw data
            if (!empty($rawJobs)) {
                $keys = array_keys($rawJobs);
                $isNumericKeys = $keys === range(0, count($rawJobs) - 1);
                $debugInfo['array_is_associative'] = !$isNumericKeys;
                $debugInfo['first_key'] = $keys[0] ?? null;
                $debugInfo['first_key_type'] = isset($keys[0]) ? gettype($keys[0]) : null;
                $debugInfo['first_value_type'] = gettype(reset($rawJobs));
                $debugInfo['first_value_sample'] = is_string(reset($rawJobs)) ? substr(reset($rawJobs), 0, 100) : reset($rawJobs);

                // Check if values are numeric (scores) - if so, it's associative [job => score]
                $firstValue = reset($rawJobs);
                $secondValue = next($rawJobs);
                $debugInfo['first_value_is_numeric'] = is_numeric($firstValue);
                $debugInfo['second_value_is_numeric'] = is_numeric($secondValue);

                // Sample first few key-value pairs
                $sample = [];
                $count = 0;
                foreach ($rawJobs as $key => $value) {
                    if ($count >= 3) break;
                    $sample[] = [
                        'key' => is_string($key) ? substr($key, 0, 50) : $key,
                        'key_type' => gettype($key),
                        'value' => is_numeric($value) ? $value : (is_string($value) ? substr($value, 0, 50) : $value),
                        'value_type' => gettype($value),
                        'value_is_numeric' => is_numeric($value),
                    ];
                    $count++;
                }
                $debugInfo['first_3_key_value_pairs'] = $sample;
            }

            // Parse jobs - handle both formats:
            // 1. Associative array: [job => score, job => score, ...] - Predis WITHSCORES format
            // 2. Flat array: [job1, score1, job2, score2, ...] - Raw Redis format
            $keys = array_keys($rawJobs);
            $isAssociative = !empty($rawJobs) && $keys !== range(0, count($rawJobs) - 1);

            // Also check if values are numeric (indicating associative format where values are scores)
            $hasNumericValues = !empty($rawJobs) && is_numeric(reset($rawJobs));

            if (!empty($rawJobs) && ($isAssociative || $hasNumericValues)) {
                // Associative array format (job => score)
                foreach ($rawJobs as $jobPayload => $scoreValue) {
                    $score = is_numeric($scoreValue) ? (float) $scoreValue : 0;

                    try {
                        $decoded = json_decode($jobPayload, true);

                        // Extract judopayMitQueueId from the serialized command data
                        $judopayMitQueueId = null;
                        if (isset($decoded['data']['command'])) {
                            // Parse the serialized PHP object to extract judopayMitQueueId
                            if (preg_match('/s:17:"judopayMitQueueId";i:(\d+);/', $decoded['data']['command'], $matches)) {
                                $judopayMitQueueId = (int) $matches[1];
                            }
                        }

                        $jobs[] = [
                            'payload' => $jobPayload,
                            'decoded' => $decoded,
                            'score' => $score,
                            'score_raw' => $scoreValue,
                            'scheduled_at' => $score > 0 ? date('Y-m-d H:i:s', (int) $score) : 'Invalid timestamp',
                            'scheduled_at_timestamp' => (int) $score,
                            'time_until' => $score > 0 ? $this->getTimeUntil($score) : 'Invalid',
                            'job_class' => $decoded['displayName'] ?? $decoded['job'] ?? 'Unknown',
                            'uuid' => $decoded['uuid'] ?? null,
                            'data' => $decoded['data'] ?? null,
                            'judopay_mit_queue_id' => $judopayMitQueueId,
                        ];
                    } catch (\Exception $e) {
                        $jobs[] = [
                            'payload' => $jobPayload,
                            'decoded' => null,
                            'score' => $score,
                            'score_raw' => $scoreValue,
                            'scheduled_at' => $score > 0 ? date('Y-m-d H:i:s', (int) $score) : 'Invalid timestamp',
                            'scheduled_at_timestamp' => (int) $score,
                            'time_until' => $score > 0 ? $this->getTimeUntil($score) : 'Invalid',
                            'job_class' => 'Parse Error',
                            'uuid' => null,
                            'data' => null,
                            'judopay_mit_queue_id' => null,
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            } else {
                // Flat array format [job1, score1, job2, score2, ...]
                // But Laravel might return it differently - let's check
                $debugInfo['parsing_method'] = 'flat_array';
                $debugInfo['array_length'] = count($rawJobs);

                for ($i = 0; $i < count($rawJobs) - 1; $i += 2) {
                    if (!isset($rawJobs[$i]) || !isset($rawJobs[$i + 1])) {
                        $debugInfo['skipped_pairs'][] = $i;
                        continue;
                    }

                    $jobPayload = $rawJobs[$i];
                    $scoreValue = $rawJobs[$i + 1];

                    // Debug first score parsing
                    if ($i === 0) {
                        $debugInfo['first_score_raw'] = $scoreValue;
                        $debugInfo['first_score_type'] = gettype($scoreValue);
                        $debugInfo['first_score_is_numeric'] = is_numeric($scoreValue);
                    }

                    $score = is_numeric($scoreValue) ? (float) $scoreValue : 0;

                    if ($score == 0 && $i === 0) {
                        $debugInfo['first_score_parsed_as_zero'] = true;
                        $debugInfo['first_score_original'] = var_export($scoreValue, true);
                    }

                    try {
                        $decoded = json_decode($jobPayload, true);

                        // Extract judopayMitQueueId from the serialized command data
                        $judopayMitQueueId = null;
                        if (isset($decoded['data']['command'])) {
                            // Parse the serialized PHP object to extract judopayMitQueueId
                            if (preg_match('/s:17:"judopayMitQueueId";i:(\d+);/', $decoded['data']['command'], $matches)) {
                                $judopayMitQueueId = (int) $matches[1];
                            }
                        }

                        $jobs[] = [
                            'payload' => $jobPayload,
                            'decoded' => $decoded,
                            'score' => $score,
                            'score_raw' => $scoreValue,
                            'scheduled_at' => $score > 0 ? date('Y-m-d H:i:s', (int) $score) : 'Invalid timestamp',
                            'scheduled_at_timestamp' => (int) $score,
                            'time_until' => $score > 0 ? $this->getTimeUntil($score) : 'Invalid',
                            'job_class' => $decoded['displayName'] ?? $decoded['job'] ?? 'Unknown',
                            'uuid' => $decoded['uuid'] ?? null,
                            'data' => $decoded['data'] ?? null,
                            'judopay_mit_queue_id' => $judopayMitQueueId,
                        ];
                    } catch (\Exception $e) {
                        $jobs[] = [
                            'payload' => $jobPayload,
                            'decoded' => null,
                            'score' => $score,
                            'score_raw' => $scoreValue,
                            'scheduled_at' => $score > 0 ? date('Y-m-d H:i:s', (int) $score) : 'Invalid timestamp',
                            'scheduled_at_timestamp' => (int) $score,
                            'time_until' => $score > 0 ? $this->getTimeUntil($score) : 'Invalid',
                            'job_class' => 'Parse Error',
                            'uuid' => null,
                            'data' => null,
                            'judopay_mit_queue_id' => null,
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            }

            // Sort by score (scheduled time)
            usort($jobs, function ($a, $b) {
                return $a['score'] <=> $b['score'];
            });

        } catch (\Exception $e) {
            $debugInfo['error'] = $e->getMessage();
            $debugInfo['trace'] = $e->getTraceAsString();
            Log::error('Queue monitor error', $debugInfo);
        }

        // Log debug info for troubleshooting
        Log::info('Queue monitor debug', $debugInfo);

        return view('admin.queue-monitor', [
            'jobs' => $jobs,
            'queueName' => $queueName,
            'redisKey' => $debugInfo['key_with_prefix'] ?? $redisKeyWithPrefix,
            'totalJobs' => count($jobs),
            'debugInfo' => $debugInfo, // Add debug info to view
        ]);
    }

    private function getTimeUntil($timestamp): string
    {
        $now = time();
        $diff = (int) $timestamp - $now;

        if ($diff < 0) {
            return 'Overdue by ' . $this->formatDuration(abs($diff));
        }

        return 'In ' . $this->formatDuration($diff);
    }

    private function formatDuration($seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' seconds';
        }

        $minutes = floor($seconds / 60);
        if ($minutes < 60) {
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours < 24) {
            return $hours . ' hour' . ($hours > 1 ? 's' : '') .
                   ($remainingMinutes > 0 ? ' ' . $remainingMinutes . ' minute' . ($remainingMinutes > 1 ? 's' : '') : '');
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        return $days . ' day' . ($days > 1 ? 's' : '') .
               ($remainingHours > 0 ? ' ' . $remainingHours . ' hour' . ($remainingHours > 1 ? 's' : '') : '');
    }
}

