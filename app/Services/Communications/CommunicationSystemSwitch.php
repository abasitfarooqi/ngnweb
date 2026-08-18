<?php

namespace App\Services\Communications;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;

class CommunicationSystemSwitch
{
    public function enabled(): bool
    {
        if ((bool) config('communications.emergency_bypass', false)) {
            return false;
        }

        if (! Schema::hasTable('system_settings')) {
            return (bool) config('communications.default_enabled', false);
        }

        $key = (string) config('communications.admin_enabled_setting_key', 'communication_system_enabled');
        $setting = SystemSetting::query()->where('key', $key)->first();

        if (! $setting) {
            return (bool) config('communications.default_enabled', false);
        }

        return filter_var($setting->value, FILTER_VALIDATE_BOOL);
    }
}
