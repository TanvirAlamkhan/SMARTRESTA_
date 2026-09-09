<?php
/**
 * SMARTRESTA API Endpoint: Commission Rules Catalog & Creation
 * GET /api/v1/commission_rules/index.php
 * POST /api/v1/commission_rules/index.php
 */

require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../helpers/response.php';

Auth::requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getConnection();

try {
    if ($method === 'GET') {
        Auth::requirePermission('commissions.view');
        $branchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 1;

        $stmt = $db->prepare("SELECT * FROM commission_rules WHERE branch_id = :bid ORDER BY id ASC");
        $stmt->execute([':bid' => $branchId]);
        $rules = $stmt->fetchAll();

        sendJsonResponse(true, 200, "Commission rules retrieved successfully", $rules);

    } else if ($method === 'POST') {
        Auth::requirePermission('commissions.rules.manage');
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $user = Auth::getCurrentUser();

        $name = trim($data['name'] ?? '');
        $code = trim($data['code'] ?? ('COMM-RULE-' . strtoupper(substr(md5(uniqid()), 0, 6))));
        $branchId = (int)($data['branch_id'] ?? 1);
        $base = strtoupper(trim($data['calculation_base'] ?? 'PERCENTAGE'));
        $rate = (float)($data['rate'] ?? 0.00);
        $fixedAmount = (float)($data['fixed_amount'] ?? 0.00);
        $minSales = (float)($data['minimum_sales'] ?? 0.00);
        $eligible = strtoupper(trim($data['commission_eligible'] ?? 'PAID_ONLY'));

        if (empty($name)) {
            sendJsonResponse(false, 400, "Rule name is required.");
            exit;
        }

        $insStmt = $db->prepare("
            INSERT INTO commission_rules
            (branch_id, name, code, calculation_base, rate, fixed_amount, minimum_sales, commission_eligible, is_active, created_by, created_at)
            VALUES (:bid, :name, :code, :base, :rate, :famt, :msales, :elg, 1, :uid, NOW())
        ");
        $insStmt->execute([
            ':bid' => $branchId,
            ':name' => $name,
            ':code' => $code,
            ':base' => $base,
            ':rate' => $rate,
            ':famt' => $fixedAmount,
            ':msales' => $minSales,
            ':elg' => $eligible,
            ':uid' => $user['id']
        ]);
        $ruleId = (int)$db->lastInsertId();

        sendJsonResponse(true, 201, "Commission rule created successfully", [
            'id' => $ruleId,
            'name' => $name,
            'code' => $code,
            'calculation_base' => $base,
            'rate' => $rate,
            'fixed_amount' => $fixedAmount
        ]);

    } else {
        sendJsonResponse(false, 405, "Method not allowed.");
    }
} catch (Exception $e) {
    sendJsonResponse(false, 400, $e->getMessage());
}
