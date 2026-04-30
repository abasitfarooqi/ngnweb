<?php

namespace App\Livewire\FluxAdmin\Pages\Settings;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\SystemSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('DigitalOcean agent settings — Flux Admin')]
class AgentSettingsForm extends Component
{
    use WithAuthorization;

    public string $endpoint_url = '';

    public string $access_key = '';

    public ?int $max_tokens = 2001;

    public ?float $temperature = 0.7;

    public ?float $top_p = 0.9;

    public ?int $top_k = 10;

    public string $retrieval_method = 'none';

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');

        $settings = SystemSetting::whereIn('key', [
            'digitalocean_agent_endpoint_url',
            'digitalocean_agent_access_key',
            'digitalocean_agent_max_tokens',
            'digitalocean_agent_temperature',
            'digitalocean_agent_top_p',
            'digitalocean_agent_top_k',
            'digitalocean_agent_retrieval_method',
        ])->get()->mapWithKeys(function ($s) {
            $v = $s->value;
            if (is_string($v) && $v !== '') {
                $d = json_decode($v, true);
                if (json_last_error() === JSON_ERROR_NONE) { $v = $d; }
            }

            return [$s->key => $v];
        });

        $this->endpoint_url = (string) ($settings['digitalocean_agent_endpoint_url'] ?? '');
        $this->access_key = (string) ($settings['digitalocean_agent_access_key'] ?? '');
        $this->max_tokens = (int) ($settings['digitalocean_agent_max_tokens'] ?? 2001);
        $this->temperature = (float) ($settings['digitalocean_agent_temperature'] ?? 0.7);
        $this->top_p = (float) ($settings['digitalocean_agent_top_p'] ?? 0.9);
        $this->top_k = (int) ($settings['digitalocean_agent_top_k'] ?? 10);
        $this->retrieval_method = (string) ($settings['digitalocean_agent_retrieval_method'] ?? 'none');
    }

    public function save(): void
    {
        $data = $this->validate([
            'endpoint_url' => ['required', 'url', 'max:500'],
            'access_key' => ['required', 'string', 'max:255'],
            'max_tokens' => ['nullable', 'integer', 'min:128', 'max:16384'],
            'temperature' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'top_p' => ['nullable', 'numeric', 'min:0.1', 'max:1'],
            'top_k' => ['nullable', 'integer', 'min:0', 'max:10'],
            'retrieval_method' => ['nullable', 'string', 'in:none,rewrite,step_back,sub_queries'],
        ]);

        $map = [
            'digitalocean_agent_endpoint_url' => ['DigitalOcean Agent Endpoint URL', $data['endpoint_url']],
            'digitalocean_agent_access_key' => ['DigitalOcean Agent Endpoint Access Key', $data['access_key']],
            'digitalocean_agent_max_tokens' => ['DigitalOcean Agent Max Tokens', $data['max_tokens'] ?? 2001],
            'digitalocean_agent_temperature' => ['DigitalOcean Agent Temperature', $data['temperature'] ?? 0.7],
            'digitalocean_agent_top_p' => ['DigitalOcean Agent Top-p', $data['top_p'] ?? 0.9],
            'digitalocean_agent_top_k' => ['DigitalOcean Agent Top-k', $data['top_k'] ?? 10],
            'digitalocean_agent_retrieval_method' => ['DigitalOcean Agent Retrieval Method', $data['retrieval_method'] ?? 'none'],
        ];

        foreach ($map as $key => [$label, $value]) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['display_name' => $label, 'value' => json_encode($value), 'locked' => false],
            );
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Agent settings saved.');
    }

    public function render()
    {
        return view('flux-admin.pages.settings.agent-settings-form');
    }
}
