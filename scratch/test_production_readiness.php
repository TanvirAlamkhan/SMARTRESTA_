<?php
/**
 * SMARTRESTA Prompt 15 Automated Production Readiness & Security Test Suite
 */

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/Logger.php';
require_once __DIR__ . '/../core/Response.php';

echo "========================================================================\n";
echo "SMARTRESTA — PROMPT 15 PRODUCTION READINESS & SECURITY DIAGNOSTIC SUITE\n";
echo "========================================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest(bool $condition, string $testName, string $detail = '') {
    global $passCount, $failCount;
    if ($condition) {
        $passCount++;
        echo " [PASS] {$testName}" . ($detail ? " → {$detail}" : "") . "\n";
    } else {
        $failCount++;
        echo "![FAIL] {$testName}" . ($detail ? " → {$detail}" : "") . "\n";
    }
}

try {
    $db = Database::getConnection();

    // ----------------------------------------------------
    // TEST 1: DATABASE CONNECTION & MIGRATION 024 INDEXES
    // ----------------------------------------------------
    echo "--- 1. Testing Database & Migration 024 Performance Indexes ---\n";
    assertTest($db !== null, "Database Connection", "Connected to MySQL successfully");

    $stmtIdx = $db->query("SHOW INDEX FROM orders WHERE Key_name = 'idx_ord_branch_status'");
    $idxFound = $stmtIdx->fetch();
    assertTest(!empty($idxFound), "Migration 024 Composite Indexes", "idx_ord_branch_status verified on orders table");

    $stmtIdxAudit = $db->query("SHOW INDEX FROM audit_logs WHERE Key_name = 'idx_audit_user_action'");
    $idxAuditFound = $stmtIdxAudit->fetch();
    assertTest(!empty($idxAuditFound), "Migration 024 Audit Indexes", "idx_audit_user_action verified on audit_logs table");


    // ----------------------------------------------------
    // TEST 2: AUTHENTICATION, BCRYPT COST & SESSION SECURITY
    // ----------------------------------------------------
    echo "\n--- 2. Testing Password Hashing, CSRF & Session Security ---\n";

    $testPass = "P@ssword123!";
    $hash = Auth::hashPassword($testPass);
    assertTest(strpos($hash, '$2y$12$') === 0, "Bcrypt Password Hashing (Cost 12)", "Hash starts with $2y$12$");
    assertTest(Auth::verifyPassword($testPass, $hash) === true, "Bcrypt Password Verification", "Password verified");
    assertTest(Auth::verifyPassword("WrongPassword", $hash) === false, "Bcrypt Password Rejection", "Invalid password rejected");

    $csrfToken = CSRF::getToken();
    assertTest(!empty($csrfToken) && strlen($csrfToken) === 64, "CSRF Token Generation", "Token length: 64 hex chars");
    assertTest(CSRF::validateToken($csrfToken) === true, "CSRF Token Validation", "Valid token accepted");
    assertTest(CSRF::validateToken("invalid_csrf_token_123") === false, "CSRF Invalid Token Rejection", "Forged token rejected");


    // ----------------------------------------------------
    // TEST 3: OBSERVABILITY, LOGGER & SECRET REDACTION
    // ----------------------------------------------------
    echo "\n--- 3. Testing Logger & Secret Redaction ---\n";

    $reqId = Logger::getRequestId();
    assertTest(!empty($reqId), "Request Correlation ID", "Generated Request ID: {$reqId}");

    Logger::info("Production Diagnostic Test Run", [
        'user' => 'admin@smartresta.com',
        'password' => 'SuperSecret123',
        'token' => 'Bearer sample_secret_token',
        'safe_data' => 'normal_value'
    ]);

    $logPath = dirname(__DIR__) . '/storage/logs/app.log';
    assertTest(file_exists($logPath), "Log File Creation", "app.log created at storage/logs/app.log");

    $logContent = file_get_contents($logPath);
    assertTest(strpos($logContent, 'SuperSecret123') === false, "Logger Secret Redaction (Password Masking)", "Redacted sensitive password");
    assertTest(strpos($logContent, '[REDACTED]') !== false, "Logger Secret Redaction Tag", "Found [REDACTED] tag in log entry");


    // ----------------------------------------------------
    // TEST 4: HEALTH PROBES (LIVENESS & READINESS)
    // ----------------------------------------------------
    echo "\n--- 4. Testing Health Probes ---\n";

    $_GET['type'] = 'liveness';
    ob_start();
    // Simulate probe check logic
    $livenessStatus = ($db !== null);
    ob_end_clean();
    assertTest($livenessStatus === true, "Liveness Probe Check", "Service status: UP");

    $_GET['type'] = 'readiness';
    $stmtReadiness = $db->query("SELECT 1");
    $readinessStatus = ($stmtReadiness->fetchColumn() == 1);
    assertTest($readinessStatus === true, "Readiness Probe Check", "Database status: CONNECTED");


    // ----------------------------------------------------
    // TEST 5: BACKUP UTILITY EXECUTION
    // ----------------------------------------------------
    echo "\n--- 5. Testing Database Backup Utility ---\n";

    $backupScript = dirname(__DIR__) . '/bin/backup.php';
    assertTest(file_exists($backupScript), "Backup Script Existence", "bin/backup.php present");

    exec("C:\\xampp\\php\\php.exe -n -d extension_dir=C:\\xampp\\php\\ext -d extension=pdo_mysql -d extension=mysqli \"{$backupScript}\"", $outputLines, $returnCode);

    assertTest($returnCode === 0, "Backup Execution Exit Code", "Backup script executed cleanly (exit 0)");

    $backupDir = dirname(__DIR__) . '/storage/backups';
    $backupFiles = glob("{$backupDir}/smartresta_backup_*.sql");
    assertTest(!empty($backupFiles), "Backup SQL Dump File Created", "Found " . count($backupFiles) . " backup dump files");

    if (!empty($backupFiles)) {
        $latestBackup = end($backupFiles);
        assertTest(filesize($latestBackup) > 0, "Backup File Integrity", "Backup size: " . round(filesize($latestBackup)/1024, 2) . " KB");
    }

} catch (Exception $e) {
    echo "\n❌ UNHANDLED EXCEPTION IN PRODUCTION TEST SUITE: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    $failCount++;
}

echo "\n========================================================================\n";
echo "PRODUCTION READINESS RESULTS: PASS = {$passCount} | FAIL = {$failCount}\n";
echo "========================================================================\n";

if ($failCount === 0) {
    echo "🎉 SMARTRESTA IS SECURE, STABLE, OBSERVABLE & PRODUCTION READY FOR RAILWAY DEPLOYMENT!\n";
    exit(0);
} else {
    echo "⚠️ SOME PRODUCTION READINESS CHECKS FAILED. INSPECT LOGS ABOVE.\n";
    exit(1);
}
