<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatAgentController extends Controller
{
    /**
     * Proxy chat messages to the configured DigitalOcean GenAI Agent.
     */
    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:4000'],
            'conversation_id' => ['nullable', 'string', 'max:255'],
        ]);

        $endpointUrlRaw = SystemSetting::where('key', 'digitalocean_agent_endpoint_url')->value('value');
        $accessKeyRaw = SystemSetting::where('key', 'digitalocean_agent_access_key')->value('value');

        // Decode JSON values if stored as JSON
        $endpointUrl = $endpointUrlRaw;
        if (is_string($endpointUrlRaw) && !empty($endpointUrlRaw)) {
            $decoded = json_decode($endpointUrlRaw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $endpointUrl = $decoded;
            }
        }

        $accessKey = $accessKeyRaw;
        if (is_string($accessKeyRaw) && !empty($accessKeyRaw)) {
            $decoded = json_decode($accessKeyRaw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $accessKey = $decoded;
            }
        }

        if (empty($endpointUrl) || empty($accessKey)) {
            return response()->json([
                'error' => 'Chat assistant is not configured yet.',
            ], 503);
        }

        try {
            // Ensure endpoint URL points to /api/v1/chat/completions
            $finalUrl = rtrim($endpointUrl, '/');
            if (!str_contains($finalUrl, '/api/v1/chat/completions')) {
                $finalUrl = $finalUrl.'/api/v1/chat/completions';
            }

            // Format payload according to DigitalOcean GenAI Agent API spec
            // API expects: {"messages": [{"role": "user", "content": "..."}]}
            $messages = [
                [
                    'role' => 'user',
                    'content' => $data['message'],
                ],
            ];

            // Helper function to get and decode a setting
            $getSetting = function ($key, $default) {
                $raw = SystemSetting::where('key', $key)->value('value');
                if (empty($raw)) {
                    return $default;
                }
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
                if (is_numeric($raw)) {
                    return is_float($default) ? (float) $raw : (int) $raw;
                }
                return $raw;
            };

            // Get all settings with defaults matching DigitalOcean playground
            $maxTokens = $getSetting('digitalocean_agent_max_tokens', 2001);
            $temperature = $getSetting('digitalocean_agent_temperature', 0.7);
            $topP = $getSetting('digitalocean_agent_top_p', 0.9);
            $topK = $getSetting('digitalocean_agent_top_k', 10);
            $retrievalMethod = $getSetting('digitalocean_agent_retrieval_method', 'none');

            // Build payload with all parameters
            $payload = [
                'messages' => $messages,
                'max_tokens' => $maxTokens,
                'temperature' => $temperature,
                'top_p' => $topP,
                'k' => $topK,
                'retrieval_method' => $retrievalMethod,
            ];

            Log::info('DigitalOcean agent chat request', [
                'url' => $finalUrl,
                'message_length' => strlen($data['message']),
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$accessKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($finalUrl, $payload);

            if (!$response->successful()) {
                Log::warning('DigitalOcean agent chat call failed', [
                    'url' => $finalUrl,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'error' => 'The assistant is unavailable at the moment. Please try again shortly.',
                ], 502);
            }

            $responseData = $response->json();
            
            // Extract reply from response structure: choices[0].message.content
            $reply = null;
            if (is_array($responseData) && isset($responseData['choices'][0]['message']['content'])) {
                $reply = $responseData['choices'][0]['message']['content'];
            } elseif (is_array($responseData) && isset($responseData['choices'][0]['content'])) {
                $reply = $responseData['choices'][0]['content'];
            }

            if (empty($reply)) {
                Log::warning('Unexpected response format from DigitalOcean agent', [
                    'response_keys' => is_array($responseData) ? array_keys($responseData) : 'non-array',
                ]);
                $reply = 'I received your message but could not process it properly.';
            }

            // Use the response ID as conversation_id if available
            $conversationId = $responseData['id'] ?? $data['conversation_id'] ?? null;

            return response()->json([
                'reply' => $reply,
                'conversation_id' => $conversationId,
            ]);
        } catch (\Throwable $e) {
            Log::error('DigitalOcean agent chat exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'The assistant is unavailable at the moment. Please try again shortly.',
            ], 502);
        }
    }
}

