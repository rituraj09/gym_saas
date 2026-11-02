<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use Illuminate\Database\Seeder;

class PlatformSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'platform_name', 'value' => 'GymSaaS', 'label' => 'Platform Name', 'type' => 'text', 'group' => 'general'],
            ['key' => 'support_email', 'value' => 'support@gymsaas.com', 'label' => 'Support Email', 'type' => 'email', 'group' => 'general'],
            ['key' => 'support_phone', 'value' => '', 'label' => 'Support Phone', 'type' => 'text', 'group' => 'general'],
            ['key' => 'maintenance_mode', 'value' => '0', 'label' => 'Maintenance Mode', 'type' => 'boolean', 'group' => 'general'],

            // Subscription
            ['key' => 'default_trial_days', 'value' => '14', 'label' => 'Default Trial Days', 'type' => 'number', 'group' => 'subscription'],
            ['key' => 'grace_period_days', 'value' => '7', 'label' => 'Grace Period After Expiry (days)', 'type' => 'number', 'group' => 'subscription'],
            ['key' => 'auto_expire_subscriptions', 'value' => '1', 'label' => 'Auto-Expire Subscriptions', 'type' => 'boolean', 'group' => 'subscription'],

            // Notifications
            ['key' => 'notify_before_expiry_days', 'value' => '7', 'label' => 'Notify Before Expiry (days)', 'type' => 'number', 'group' => 'notifications'],
            ['key' => 'enable_email_notifications', 'value' => '0', 'label' => 'Enable Email Notifications', 'type' => 'boolean', 'group' => 'notifications'],
        ];

        foreach ($settings as $setting) {
            PlatformSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
