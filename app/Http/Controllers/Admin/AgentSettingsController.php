<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgentSettingsController extends Controller
{
    public function index(): View
    {
        $settings = SystemSetting::whereIn('key', [
            'digitalocean_agent_endpoint_url',
            'digitalocean_agent_access_key',
            'digitalocean_agent_max_tokens',
            'digitalocean_agent_temperature',
            'digitalocean_agent_top_p',
            'digitalocean_agent_top_k',
            'digitalocean_agent_retrieval_method',
        ])->get()->mapWithKeys(function ($setting) {
            $value = $setting->value;
            // Decode JSON if it's stored as JSON
            if (is_string($value) && !empty($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $decoded;
                }
            }
            return [$setting->key => $value];
        });

        return view('livewire.agreements.migrated.admin.agent_settings', [
            'endpointUrl' => $settings['digitalocean_agent_endpoint_url'] ?? '',
            'accessKey' => $settings['digitalocean_agent_access_key'] ?? '',
            'maxTokens' => $settings['digitalocean_agent_max_tokens'] ?? '2001',
            'temperature' => $settings['digitalocean_agent_temperature'] ?? '0.7',
            'topP' => $settings['digitalocean_agent_top_p'] ?? '0.9',
            'topK' => $settings['digitalocean_agent_top_k'] ?? '10',
            'retrievalMethod' => $settings['digitalocean_agent_retrieval_method'] ?? 'none',
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'endpoint_url' => ['required', 'url', 'max:500'],
            'access_key' => ['required', 'string', 'max:255'],
            'max_tokens' => ['nullable', 'integer', 'min:128', 'max:16384'],
            'temperature' => ['nullable', 'numeric', 'min:0.0', 'max:1.0'],
            'top_p' => ['nullable', 'numeric', 'min:0.1', 'max:1.0'],
            'top_k' => ['nullable', 'integer', 'min:0', 'max:10'],
            'retrieval_method' => ['nullable', 'string', 'in:none,rewrite,step_back,sub_queries'],
        ]);

        // JSON-encode values to satisfy the check constraint
        SystemSetting::updateOrCreate(
            ['key' => 'digitalocean_agent_endpoint_url'],
            [
                'display_name' => 'DigitalOcean Agent Endpoint URL',
                'value' => json_encode($data['endpoint_url']),
                'locked' => false,
            ]
        );

        SystemSetting::updateOrCreate(
            ['key' => 'digitalocean_agent_access_key'],
            [
                'display_name' => 'DigitalOcean Agent Endpoint Access Key',
                'value' => json_encode($data['access_key']),
                'locked' => false,
            ]
        );

        // Save max_tokens (default to 2001 if not provided)
        $maxTokens = $data['max_tokens'] ?? 2001;
        SystemSetting::updateOrCreate(
            ['key' => 'digitalocean_agent_max_tokens'],
            [
                'display_name' => 'DigitalOcean Agent Max Tokens',
                'value' => json_encode($maxTokens),
                'locked' => false,
            ]
        );

        // Save temperature (default to 0.7 if not provided)
        $temperature = $data['temperature'] ?? 0.7;
        SystemSetting::updateOrCreate(
            ['key' => 'digitalocean_agent_temperature'],
            [
                'display_name' => 'DigitalOcean Agent Temperature',
                'value' => json_encode($temperature),
                'locked' => false,
            ]
        );

        // Save top_p (default to 0.9 if not provided)
        $topP = $data['top_p'] ?? 0.9;
        SystemSetting::updateOrCreate(
            ['key' => 'digitalocean_agent_top_p'],
            [
                'display_name' => 'DigitalOcean Agent Top P',
                'value' => json_encode($topP),
                'locked' => false,
            ]
        );

        // Save top_k (default to 10 if not provided)
        $topK = $data['top_k'] ?? 10;
        SystemSetting::updateOrCreate(
            ['key' => 'digitalocean_agent_top_k'],
            [
                'display_name' => 'DigitalOcean Agent Top K',
                'value' => json_encode($topK),
                'locked' => false,
            ]
        );

        // Save retrieval_method (default to 'none' if not provided)
        $retrievalMethod = $data['retrieval_method'] ?? 'none';
        SystemSetting::updateOrCreate(
            ['key' => 'digitalocean_agent_retrieval_method'],
            [
                'display_name' => 'DigitalOcean Agent Retrieval Method',
                'value' => json_encode($retrievalMethod),
                'locked' => false,
            ]
        );

        return redirect()
            ->back()
            ->with('success', 'Agent settings updated.');
    }
}

