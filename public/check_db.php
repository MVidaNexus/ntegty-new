<?php
// Secure temporary script
if (($_GET['token'] ?? '') !== 'v3ry-s3cr3t-t0k3n') {
    http_response_code(403);
    die('Unauthorized');
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

header('Content-Type: application/json');

try {
    $examTypes = DB::table('exam_types')->select('id', 'code', 'name_ar')->get();
    $examBranches = DB::table('exam_branches')->select('id', 'exam_type_id', 'code', 'name_ar')->get();
    $resultsCounts = DB::table('results')
        ->groupBy('exam_type_id')
        ->selectRaw('exam_type_id, count(*) as count')
        ->get();
        
    $resultsWithBranches = DB::table('results')
        ->groupBy('exam_type_id', 'branch_id')
        ->selectRaw('exam_type_id, branch_id, count(*) as count')
        ->get();

    echo json_encode([
        'success' => true,
        'exam_types' => $examTypes,
        'exam_branches' => $examBranches,
        'results_counts' => $resultsCounts,
        'results_with_branches' => $resultsWithBranches,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (\Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
}
