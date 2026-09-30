<?php
/**
 * MD Printers - B2B Payment Service, Credit Ledger & Gateway Abstraction
 */
require_once CONFIG_PATH . '/database.php';
require_once MODELS_PATH . '/Customer.php';

interface PaymentGatewayInterface {
    public function isEnabled(): bool;
    public function createPaymentOrder(float $amount, string $currency, string $receiptId): array;
    public function verifySignature(array $payload): bool;
}

class RazorpayGateway implements PaymentGatewayInterface {
    public function isEnabled(): bool {
        return defined('PAYMENT_GATEWAY_ENABLED') && PAYMENT_GATEWAY_ENABLED && defined('RAZORPAY_KEY_ID') && !empty(RAZORPAY_KEY_ID);
    }

    public function createPaymentOrder(float $amount, string $currency, string $receiptId): array {
        if (!$this->isEnabled()) {
            return ['success' => false, 'message' => 'Online Payment Gateway is currently disabled / configuration pending.'];
        }
        return ['success' => false, 'message' => 'Razorpay API keys pending configuration.'];
    }

    public function verifySignature(array $payload): bool {
        return false;
    }
}

class PaymentService {

    public static function initTables() {
        $db = Database::getInstance();
        if (!$db) return;

        try {
            // 1. Payment Transactions Table
            $db->exec("CREATE TABLE IF NOT EXISTS `payments` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `order_id` INT NOT NULL,
                `customer_id` INT NOT NULL,
                `payment_reference` VARCHAR(100) NOT NULL UNIQUE,
                `gateway` VARCHAR(50) NOT NULL DEFAULT 'bank_transfer',
                `gateway_order_id` VARCHAR(100) DEFAULT NULL,
                `gateway_payment_id` VARCHAR(100) DEFAULT NULL,
                `gateway_signature` VARCHAR(255) DEFAULT NULL,
                `payment_method` VARCHAR(50) NOT NULL DEFAULT 'bank_transfer',
                `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `currency` VARCHAR(10) NOT NULL DEFAULT 'INR',
                `status` ENUM('created', 'pending', 'authorized', 'paid', 'failed', 'cancelled', 'refunded', 'partially_refunded') NOT NULL DEFAULT 'pending',
                `failure_reason` TEXT DEFAULT NULL,
                `gateway_response_json` TEXT DEFAULT NULL,
                `paid_at` DATETIME DEFAULT NULL,
                `refunded_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_order` (`order_id`),
                INDEX `idx_cust` (`customer_id`),
                INDEX `idx_ref` (`payment_reference`),
                INDEX `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // 2. Customer B2B Credit Ledger Table
            $db->exec("CREATE TABLE IF NOT EXISTS `customer_credit_ledger` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `customer_id` INT NOT NULL,
                `order_id` INT DEFAULT NULL,
                `payment_id` INT DEFAULT NULL,
                `transaction_type` ENUM('order_credit', 'payment_received', 'credit_adjustment', 'refund', 'manual_debit', 'manual_credit') NOT NULL,
                `reference` VARCHAR(100) NOT NULL,
                `debit` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `credit` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `balance_after` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `description` TEXT DEFAULT NULL,
                `created_by_type` ENUM('customer', 'admin', 'system') NOT NULL DEFAULT 'system',
                `created_by_id` INT DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_cust` (`customer_id`),
                INDEX `idx_order` (`order_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // 3. Refunds Table
            $db->exec("CREATE TABLE IF NOT EXISTS `refunds` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `payment_id` INT DEFAULT NULL,
                `order_id` INT NOT NULL,
                `refund_reference` VARCHAR(100) NOT NULL UNIQUE,
                `gateway_refund_id` VARCHAR(100) DEFAULT NULL,
                `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `reason` TEXT DEFAULT NULL,
                `status` ENUM('requested', 'processing', 'processed', 'failed', 'cancelled') NOT NULL DEFAULT 'requested',
                `created_by_type` ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
                `created_by_id` INT NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_order` (`order_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        } catch (\Throwable $e) {
            // Ignore if exists
        }
    }

    public static function isGatewayEnabled(): bool {
        return defined('PAYMENT_GATEWAY_ENABLED') && PAYMENT_GATEWAY_ENABLED && defined('RAZORPAY_KEY_ID') && !empty(RAZORPAY_KEY_ID);
    }

    public static function getSupportedMethods(): array {
        $gatewayActive = self::isGatewayEnabled();

        return [
            'bank_transfer' => [
                'name' => 'NEFT / RTGS / Bank Transfer',
                'icon' => 'fas fa-university',
                'description' => 'Pay via direct bank transfer or IMPS. Orders confirmed upon press ledger verification.'
            ],
            'approved_credit' => [
                'name' => 'B2B Account Credit Facility',
                'icon' => 'fas fa-hand-holding-usd',
                'description' => 'Charge to pre-approved customer B2B credit limit.'
            ],
            'online' => [
                'name' => 'Online Gateway (UPI, Cards, NetBanking)',
                'icon' => 'fas fa-credit-card',
                'description' => $gatewayActive ? 'Secure instant payment through Razorpay / PayU Gateway.' : 'Online Payment — Coming Soon (Configuration Pending)'
            ]
        ];
    }

    /**
     * Validate credit limit server-side against customer wallet balance
     */
    public static function validateCreditLimit(int $customerId, float $orderTotal): array {
        self::initTables();
        $customer = Customer::findById($customerId);
        if (!$customer) {
            return ['eligible' => false, 'message' => 'Customer profile not found.'];
        }

        $limit = (float)($customer['credit_limit'] ?? 0.00);
        $used = (float)($customer['used_credit'] ?? $customer['wallet_balance'] ?? 0.00);
        $available = max(0.00, $limit - $used);

        if ($limit <= 0) {
            return [
                'eligible' => false,
                'message' => 'Your account does not currently have an active credit line. Please contact administration.'
            ];
        }

        if ($orderTotal > $available) {
            return [
                'eligible' => false,
                'message' => 'Order total (₹' . number_format($orderTotal, 2) . ') exceeds your available B2B credit line (₹' . number_format($available, 2) . ').'
            ];
        }

        return [
            'eligible' => true,
            'available_credit' => $available,
            'message' => 'B2B Credit verified! Available credit line: ₹' . number_format($available, 2)
        ];
    }

    /**
     * Record B2B Credit Ledger Transaction (Debit or Credit)
     */
    public static function recordCreditTransaction(int $customerId, ?int $orderId, ?int $paymentId, string $type, string $reference, float $debit, float $credit, string $description, string $createdByType = 'system', ?int $createdById = null): bool {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            // Fetch current balance
            $customer = Customer::findById($customerId);
            $currentUsed = (float)($customer['used_credit'] ?? $customer['wallet_balance'] ?? 0.00);

            $newBalance = round($currentUsed + $debit - $credit, 2);

            $stmt = $db->prepare("INSERT INTO customer_credit_ledger (
                customer_id, order_id, payment_id, transaction_type, reference,
                debit, credit, balance_after, description, created_by_type, created_by_id
            ) VALUES (
                :cid, :oid, :pid, :type, :ref,
                :debit, :credit, :bal, :desc, :btype, :bid
            )");

            $stmt->execute([
                'cid' => $customerId,
                'oid' => $orderId,
                'pid' => $paymentId,
                'type' => $type,
                'ref' => $reference,
                'debit' => $debit,
                'credit' => $credit,
                'bal' => $newBalance,
                'desc' => $description,
                'btype' => $createdByType,
                'bid' => $createdById
            ]);

            // Update customer used credit balance in customers table
            try {
                $uStmt = $db->prepare("UPDATE customers SET used_credit = :bal, wallet_balance = :bal WHERE id = :cid");
                $uStmt->execute(['bal' => $newBalance, 'cid' => $customerId]);
            } catch (\Throwable $ex) {}

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Verify & Record Bank Transfer or Manual Payment Entry by Admin
     */
    public static function recordPaymentVerification(int $orderId, float $amount, string $paymentReference, string $paymentMethod = 'bank_transfer', string $notes = '', ?int $adminId = null): array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return ['success' => false, 'message' => 'Database error.'];

        $db->beginTransaction();

        try {
            // Check if payment reference already recorded (Prevent duplicate processing)
            $checkStmt = $db->prepare("SELECT id FROM payments WHERE payment_reference = :ref LIMIT 1");
            $checkStmt->execute(['ref' => trim($paymentReference)]);
            if ($checkStmt->fetch()) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Payment reference "' . e($paymentReference) . '" has already been recorded.'];
            }

            // Fetch order
            $oStmt = $db->prepare("SELECT customer_id, total, payment_status FROM orders WHERE id = :oid");
            $oStmt->execute(['oid' => $orderId]);
            $order = $oStmt->fetch();
            if (!$order) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Order not found.'];
            }

            $customerId = (int)$order['customer_id'];
            $orderTotal = (float)$order['total'];

            // Create Payment Record
            $pStmt = $db->prepare("INSERT INTO payments (
                order_id, customer_id, payment_reference, gateway, payment_method,
                amount, currency, status, paid_at
            ) VALUES (
                :oid, :cid, :pref, :gw, :pmethod,
                :amount, 'INR', 'paid', NOW()
            )");

            $pStmt->execute([
                'oid' => $orderId,
                'cid' => $customerId,
                'pref' => trim($paymentReference),
                'gw' => $paymentMethod,
                'pmethod' => $paymentMethod,
                'amount' => $amount
            ]);

            $paymentId = (int)$db->lastInsertId();

            // Calculate total paid for order
            $sumStmt = $db->prepare("SELECT SUM(amount) FROM payments WHERE order_id = :oid AND status = 'paid'");
            $sumStmt->execute(['oid' => $orderId]);
            $totalPaid = (float)$sumStmt->fetchColumn();

            $newPayStatus = ($totalPaid >= $orderTotal) ? 'paid' : 'partially_paid';

            // Update order payment status
            $uOrder = $db->prepare("UPDATE orders SET payment_status = :pstatus WHERE id = :oid");
            $uOrder->execute(['pstatus' => $newPayStatus, 'oid' => $orderId]);

            // Record Ledger Entry
            self::recordCreditTransaction(
                $customerId, $orderId, $paymentId, 'payment_received',
                $paymentReference, 0.00, $amount,
                'Payment received via ' . $paymentMethod . ' (Ref: ' . $paymentReference . '). Notes: ' . $notes,
                'admin', $adminId
            );

            // Log timeline history in Order
            $histStmt = $db->prepare("INSERT INTO order_status_history (
                order_id, old_status, new_status, changed_by_type, changed_by_id, comment
            ) VALUES (:oid, NULL, 'payment_received', 'admin', :aid, :comment)");
            $histStmt->execute([
                'oid' => $orderId,
                'aid' => $adminId,
                'comment' => 'Payment of ₹' . number_format($amount, 2) . ' verified (Ref: ' . $paymentReference . '). Order payment status: ' . strtoupper($newPayStatus)
            ]);

            $db->commit();

            return [
                'success' => true,
                'message' => 'Payment verified & recorded successfully! Order payment status updated to ' . strtoupper($newPayStatus) . '.',
                'payment_id' => $paymentId
            ];

        } catch (\Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            return ['success' => false, 'message' => 'Error recording payment: ' . $e->getMessage()];
        }
    }

    public static function getAllPayments(array $filters = []): array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return [];

        try {
            $sql = "SELECT p.*, o.order_number, c.company_name, c.contact_person 
                    FROM payments p 
                    LEFT JOIN orders o ON p.order_id = o.id 
                    LEFT JOIN customers c ON p.customer_id = c.id 
                    WHERE 1=1";
            $params = [];

            if (!empty($filters['status'])) {
                $sql .= " AND p.status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['customer_id'])) {
                $sql .= " AND p.customer_id = :cid";
                $params['cid'] = $filters['customer_id'];
            }

            $sql .= " ORDER BY p.id DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function getCreditLedger(int $customerId): array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return [];

        try {
            $stmt = $db->prepare("SELECT * FROM customer_credit_ledger WHERE customer_id = :cid ORDER BY id DESC");
            $stmt->execute(['cid' => $customerId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
