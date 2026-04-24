<?php
/**
 * Automated Deployment Script for GitHub Webhooks
 * Uses proc_open since exec/shell_exec are disabled on this server
 */

// Load DEPLOY_SECRET from .env file
$envFile = __DIR__ . '/.env';
$secret = '';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, 'DEPLOY_SECRET=') === 0) {
            $secret = substr($line, strlen('DEPLOY_SECRET='));
            break;
        }
    }
}

if (empty($secret)) {
    http_response_code(500);
    die('Deploy secret not configured.');
}

// Verify GitHub signature
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
if (!$signature) {
    http_response_code(403);
    die('Signature header not set.');
}

$payload = file_get_contents('php://input');
$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($hash, $signature)) {
    http_response_code(403);
    die('Invalid signature.');
}

// Helper function using proc_open (shell_exec is disabled)
function run_cmd($cmd, $cwd = '/home/ntegty/public_html') {
    $desc = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w']
    ];
    $proc = proc_open($cmd, $desc, $pipes, $cwd);
    if (!is_resource($proc)) {
        return "[ERROR] Failed to run: {$cmd}\n";
    }
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $code = proc_close($proc);
    return trim($stdout . "\n" . $stderr) . " (exit:{$code})";
}

// Commands to execute
$output = "Deploy Time: " . date('Y-m-d H:i:s') . "\n";
$output .= "---\n";

// Pull latest changes
$output .= "git pull origin main:\n";
$output .= run_cmd("git pull origin main") . "\n\n";

// Optional: Clear Laravel caches after deploy
$output .= "php artisan optimize:clear:\n";
$output .= run_cmd("php artisan optimize:clear") . "\n\n";

$output .= "---\n";
$output .= "Deploy completed.\n";
$output .= "=========================\n";

// Save deploy log
$logDir = '/home/ntegty/public_html/storage/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
file_put_contents($logDir . '/deploy.log', $output, FILE_APPEND);

echo "Deployed successfully!";
