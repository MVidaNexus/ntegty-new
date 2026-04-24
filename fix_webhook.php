<?php
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache');

if (($_GET['key'] ?? '') !== 'fixWebhook2026') {
    http_response_code(403);
    die('Forbidden');
}

$token = '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';
$repo = 'MVidaNexus/ntegty';

$envContent = file_get_contents('/home/ntegty/public_html/.env');
preg_match('/DEPLOY_SECRET=(.+)/', $envContent, $m);
$deploySecret = trim($m[1] ?? '');

echo "=== FIX WEBHOOKS === " . date('Y-m-d H:i:s') . " ===\n\n";

// Delete both old webhooks
$ids = [606025518, 610538325];
foreach ($ids as $id) {
    echo "Deleting webhook {$id}...\n";
    $ch = curl_init("https://api.github.com/repos/{$repo}/hooks/{$id}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$token}",
        "Accept: application/vnd.github+json",
        "User-Agent: DeployScript"
    ]);
    curl_exec($ch);
    echo "HTTP: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
    curl_close($ch);
}

// Create fresh webhook with correct secret
echo "\nCreating new webhook with correct secret...\n";
echo "Secret: {$deploySecret}\n\n";

$ch = curl_init("https://api.github.com/repos/{$repo}/hooks");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'name' => 'web',
    'active' => true,
    'events' => ['push'],
    'config' => [
        'url' => 'https://ntegty.com/deploy.php',
        'content_type' => 'json',
        'secret' => $deploySecret,
        'insecure_ssl' => '0'
    ]
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer {$token}",
    "Accept: application/vnd.github+json",
    "Content-Type: application/json",
    "User-Agent: DeployScript"
]);
$result = json_decode(curl_exec($ch), true);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP: {$httpCode}\n";
if ($httpCode === 201) {
    echo "Webhook created successfully!\n";
    echo "ID: {$result['id']}\n";
    echo "URL: {$result['config']['url']}\n";
    echo "Events: " . implode(', ', $result['events']) . "\n";
    echo "Active: " . ($result['active'] ? 'YES' : 'NO') . "\n";
} else {
    echo "Error: " . json_encode($result) . "\n";
}

// Verify
echo "\n--- Verifying ---\n";
$ch = curl_init("https://api.github.com/repos/{$repo}/hooks");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer {$token}",
    "Accept: application/vnd.github+json",
    "User-Agent: DeployScript"
]);
$hooks = json_decode(curl_exec($ch), true);
curl_close($ch);

echo "Current webhooks:\n";
foreach ($hooks as $h) {
    echo "  {$h['id']} -> {$h['config']['url']} (active: " . ($h['active'] ? 'yes' : 'no') . ")\n";
}
