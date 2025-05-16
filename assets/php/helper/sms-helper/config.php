<?php
// Define your SMS gateway configuration values here directly
$config = [
    'SMS_API_KEY' => '2106gkd160cb8d400dtd1f4cadh8454',
    'SMS_API_URL' => 'https://textit.biz/api/send',
];

// This function can be used to retrieve configuration values
function getConfig($key) {
    global $config;
    if (isset($config[$key])) {
        return $config[$key];
    } else {
        throw new Exception("Configuration key not found: " . $key);
    }
}
