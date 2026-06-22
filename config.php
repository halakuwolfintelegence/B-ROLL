<?php
// config.php - Configuration file
session_start();

// Default API keys (will be overridden by database/file if exists)
$config = [
    'pexels_api_key' => 'hPfLL2XaPl3rVFEHXNaQbZstXrX1vZMSxmuvN9tqrAwbpXSZhdVL3Blm',
    'pixabay_api_key' => '56395196-037a4e0daa26799bb7627b4f3'
];

// Load saved config from file
$configFile = __DIR__ . '/config_data.json';
if (file_exists($configFile)) {
    $savedConfig = json_decode(file_get_contents($configFile), true);
    if ($savedConfig) {
        $config = array_merge($config, $savedConfig);
    }
}

// Function to save config
function saveConfig($pexelsKey, $pixabayKey) {
    global $configFile;
    $data = [
        'pexels_api_key' => $pexelsKey,
        'pixabay_api_key' => $pixabayKey
    ];
    return file_put_contents($configFile, json_encode($data, JSON_PRETTY_PRINT));
}
?>
