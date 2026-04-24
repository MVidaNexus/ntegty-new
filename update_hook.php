<?php
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache');

if (($_GET['key'] ?? '') !== 'updateHook2026') {
    http_response_code(403);
    die('Forbidden');
}

$token = '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';
$repo = 'MVidaNexus/ntegty';
$hookId = 610538614;

$envContent = file_get_contents('/home/ntegty/public_html/.env');
preg_match('/DEPLOY_SECRET=(.+)/', $envContent, $m);
$deploySecret = trim($m[1] ?? '');

echo "=== UPDATE WEBHOOK === " . date('Y-m-d H:i:s') . " ===\n\n";
echo "Updating webhook {$hookId} with correct secret...\n";
echo "Secret: {$deploySecret}\n\n";

$ch = curl_init("https://api.github.com/repos/{$repo}/hooks/{$hookId}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
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
if ($httpCode === 200) {
    echo "Webhook updated successfully!\n";
    echo "URL: {$result['config']['url']}\n";
    echo "Events: " . implode(', ', $result['events']) . "\n";
    echo "Active: " . ($result['active'] ? 'YES' : 'NO') . "\n";
    echo "Updated: {$result['updated_at']}\n";
} else {
    echo "Error: " . json_encode($result) . "\n";
}

// Test the webhook with a ping
echo "\nSending test ping...\n";
$ch = curl_init("https://api.github.com/repos/{$repo}/hooks/{$hookId}/pings");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer {$token}",
    "Accept: application/vnd.github+json",
    "Content-Type: application/json",
    "User-Agent: DeployScript"
]);
curl_exec($ch);
$pingCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "Ping HTTP: {$pingCode}\n";
if ($pingCode === 204) {
    echo "Ping sent successfully! Check deploy.log for the result.\n";
}
