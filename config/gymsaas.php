<?php

return [
    'subscription_grace_days' => (int) env('SUBSCRIPTION_GRACE_DAYS', 7),
    'trial_days' => (int) env('TRIAL_DAYS', 14),
];
