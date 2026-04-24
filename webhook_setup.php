<?php
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache');

if (($_GET['key'] ?? '') !== 'webhookSetup2026') {
    http_response_code(403);
    die('Forbidden');
}

$step = $_GET['step'] ?? 'info';
$token = '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';
$repo = 'MVidaNexus/ntegty';

// Read DEPLOY_SECRET from .env
$envContent = file_get_contents('/home/ntegty/public_html/.env');
preg_match('/DEPLOY_SECRET=(.+)/', $envContent, $m);
$deploySecret = trim($m[1] ?? '');

function run_cmd($cmd, $cwd = '/home/ntegty/public_html') {
    $desc = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    $proc = proc_open($cmd, $desc, $pipes, $cwd);
    if (!is_resource($proc)) return "[ERROR] {$cmd}\n";
    fclose($pipes[0]);
    $out = stream_get_contents($pipes[1]);
    $err = stream_get_contents($pipes[2]);
    fclose($pipes[1]); fclose($pipes[2]);
    $c = proc_close($proc);
    return "$ {$cmd}\n" . trim($out . "\n" . $err) . " [exit:{$c}]\n\n";
}

echo "=== WEBHOOK SETUP === " . date('Y-m-d H:i:s') . " ===\n\n";

if ($step === 'create') {
    echo "[1] Creating GitHub Webhook...\n";
    echo "DEPLOY_SECRET: {$deploySecret}\n";
    echo "Webhook URL: https://ntegty.com/deploy.php\n\n";
    
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
        echo "Webhook ID: {$result['id']}\n";
        echo "Events: " . implode(', ', $result['events']) . "\n";
        echo "Active: " . ($result['active'] ? 'YES' : 'NO') . "\n";
    } else {
        echo "Error: " . json_encode($result) . "\n";
    }
}

if ($step === 'list') {
    $ch = curl_init("https://api.github.com/repos/{$repo}/hooks");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$token}",
        "Accept: application/vnd.github+json",
        "User-Agent: DeployScript"
    ]);
    $result = json_decode(curl_exec($ch), true);
    curl_close($ch);
    
    if (is_array($result)) {
        foreach ($result as $hook) {
            echo "ID: {$hook['id']}\n";
            echo "URL: {$hook['config']['url']}\n";
            echo "Events: " . implode(', ', $hook['events']) . "\n";
            echo "Active: " . ($hook['active'] ? 'YES' : 'NO') . "\n";
            echo "---\n";
        }
        if (empty($result)) echo "No webhooks configured.\n";
    }
}

if ($step === 'cleanup') {
    echo "Cleaning up temp scripts...\n";
    $files = ['fix_repo.php', 'do_sync.php', 'run_sync.php', 'sync2.php', 
              'check_exec.php', 'test_ping.php', 'gh_keys.php', 'push_fix.php',
              'storage/sync_repo.sh'];
    $bp = '/home/ntegty/public_html';
    foreach ($files as $f) {
        $path = "{$bp}/{$f}";
        if (file_exists($path)) {
            @unlink($path);
            echo "  Deleted: {$f}\n";
        }
    }
    
    // Commit cleanup (remove any tracked temp files)
    echo "\nCommitting cleanup...\n";
    echo run_cmd("git add -A");
    echo run_cmd("git status");
    
    // Check if there's anything to commit
    $desc = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    $p = proc_open('git status --porcelain', $desc, $pipes, $bp);
    fclose($pipes[0]);
    $dirty = trim(stream_get_contents($pipes[1]));
    fclose($pipes[1]); fclose($pipes[2]); proc_close($p);
    
    if ($dirty) {
        echo run_cmd("git commit -m 'chore: cleanup temp deployment scripts'");
        echo run_cmd("git push origin main");
    } else {
        echo "Nothing to commit.\n";
    }
    
    echo "\nFinal status:\n";
    echo run_cmd("git log --oneline -5");
    echo run_cmd("git branch -vv");
    
    // Self-delete last
    @unlink(__FILE__);
    echo "\nAll cleanup scripts removed!\n";
}
