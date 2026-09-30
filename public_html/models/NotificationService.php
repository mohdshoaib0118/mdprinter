<?php
/**
 * MD Printers - Automated Email & WhatsApp Notification Service
 */
require_once CONFIG_PATH . '/database.php';
require_once MODELS_PATH . '/Setting.php';

class NotificationService {

    public static function initTables() {
        $db = Database::getInstance();
        if (!$db) return;

        try {
            $db->exec("CREATE TABLE IF NOT EXISTS `notification_logs` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `customer_id` INT DEFAULT NULL,
                `order_id` INT DEFAULT NULL,
                `type` VARCHAR(100) NOT NULL,
                `channel` ENUM('email', 'whatsapp', 'system') NOT NULL DEFAULT 'email',
                `recipient` VARCHAR(191) NOT NULL,
                `subject` VARCHAR(255) DEFAULT NULL,
                `message` TEXT DEFAULT NULL,
                `provider_reference` VARCHAR(100) DEFAULT NULL,
                `status` ENUM('queued', 'sent', 'delivered', 'failed', 'skipped', 'disabled', 'pending_configuration') NOT NULL DEFAULT 'pending_configuration',
                `error_message` TEXT DEFAULT NULL,
                `sent_at` DATETIME DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_cust` (`customer_id`),
                INDEX `idx_order` (`order_id`),
                INDEX `idx_channel` (`channel`),
                INDEX `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        } catch (\Throwable $e) {
            // Ignore if exists
        }
    }

    public static function isSmtpConfigured(): bool {
        return defined('MAIL_HOST') && !empty(MAIL_HOST) && defined('MAIL_USERNAME') && !empty(MAIL_USERNAME);
    }

    public static function isWhatsAppConfigured(): bool {
        return defined('WHATSAPP_API_KEY') && !empty(WHATSAPP_API_KEY);
    }

    /**
     * Send Branded HTML Email and Log Status
     */
    public static function sendEmail(string $recipientEmail, string $subject, string $htmlMessage, ?int $customerId = null, ?int $orderId = null, string $eventType = 'general'): bool {
        self::initTables();
        $db = Database::getInstance();

        $smtpActive = self::isSmtpConfigured();
        $logStatus = $smtpActive ? 'sent' : 'disabled';
        $errorMsg = $smtpActive ? null : 'SMTP Server credentials pending configuration in environment.';

        if ($smtpActive) {
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= "From: " . Setting::get('site_name', 'MD PRINTERS') . " <" . (defined('MAIL_FROM_ADDRESS') ? MAIL_FROM_ADDRESS : 'info@mdprinters.com') . ">\r\n";
            @mail($recipientEmail, $subject, $htmlMessage, $headers);
        }

        if ($db) {
            try {
                $stmt = $db->prepare("INSERT INTO notification_logs (
                    customer_id, order_id, type, channel, recipient, subject, message, status, error_message, sent_at
                ) VALUES (
                    :cid, :oid, :type, 'email', :rec, :subj, :msg, :st, :err, :sdate
                )");

                $stmt->execute([
                    'cid' => $customerId,
                    'oid' => $orderId,
                    'type' => $eventType,
                    'rec' => $recipientEmail,
                    'subj' => $subject,
                    'msg' => substr(strip_tags($htmlMessage), 0, 500),
                    'st' => $logStatus,
                    'err' => $errorMsg,
                    'sdate' => $smtpActive ? date('Y-m-d H:i:s') : null
                ]);
            } catch (\Throwable $ex) {}
        }

        return $smtpActive;
    }

    /**
     * Log WhatsApp Notification State
     */
    public static function sendWhatsApp(string $recipientPhone, string $messageText, ?int $customerId = null, ?int $orderId = null, string $eventType = 'general'): bool {
        self::initTables();
        $db = Database::getInstance();

        $waActive = self::isWhatsAppConfigured();
        $logStatus = $waActive ? 'sent' : 'pending_configuration';
        $errorMsg = $waActive ? null : 'WhatsApp Business API credentials pending configuration.';

        if ($db) {
            try {
                $stmt = $db->prepare("INSERT INTO notification_logs (
                    customer_id, order_id, type, channel, recipient, subject, message, status, error_message
                ) VALUES (
                    :cid, :oid, :type, 'whatsapp', :rec, :subj, :msg, :st, :err
                )");

                $stmt->execute([
                    'cid' => $customerId,
                    'oid' => $orderId,
                    'type' => $eventType,
                    'rec' => $recipientPhone,
                    'subj' => 'WhatsApp Notification: ' . $eventType,
                    'msg' => $messageText,
                    'st' => $logStatus,
                    'err' => $errorMsg
                ]);
            } catch (\Throwable $ex) {}
        }

        return $waActive;
    }

    /**
     * Event Trigger Dispatcher
     */
    public static function triggerEvent(string $eventType, array $data) {
        $email = $data['email'] ?? '';
        $phone = $data['mobile'] ?? ($data['phone'] ?? '');
        $cid = $data['customer_id'] ?? null;
        $oid = $data['order_id'] ?? null;
        $orderNum = $data['order_number'] ?? '';

        if ($eventType === 'order_placed') {
            $subject = "Order #" . $orderNum . " Placed Successfully - MD Printers";
            $msg = "<h2>Thank you for your B2B Order #{$orderNum}!</h2><p>Your commercial printing order has been registered in our press system. Please upload your high-resolution artwork files via your customer dashboard.</p>";
            if ($email) self::sendEmail($email, $subject, $msg, $cid, $oid, $eventType);
            if ($phone) self::sendWhatsApp($phone, "Hello! Your B2B Order #{$orderNum} has been placed with MD Printers. Upload your artwork at " . SITE_URL . "/dashboard/orders", $cid, $oid, $eventType);
        } elseif ($eventType === 'payment_received') {
            $subject = "Payment Verified for Order #" . $orderNum;
            $msg = "<h2>Payment Received & Verified</h2><p>We have verified your payment for Order #{$orderNum}. Your press run will commence shortly.</p>";
            if ($email) self::sendEmail($email, $subject, $msg, $cid, $oid, $eventType);
        } elseif ($eventType === 'dispatched') {
            $subject = "Order #" . $orderNum . " Dispatched!";
            $msg = "<h2>Your Print Order is on its way!</h2><p>Order #{$orderNum} has been packed and dispatched via courier. Tracking AWB: " . e($data['tracking_number'] ?? '') . "</p>";
            if ($email) self::sendEmail($email, $subject, $msg, $cid, $oid, $eventType);
        }
    }

    public static function getLogs(array $filters = []): array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return [];

        try {
            $sql = "SELECT n.*, c.company_name, c.contact_person 
                    FROM notification_logs n 
                    LEFT JOIN customers c ON n.customer_id = c.id 
                    WHERE 1=1";
            $params = [];

            if (!empty($filters['channel'])) {
                $sql .= " AND n.channel = :chan";
                $params['chan'] = $filters['channel'];
            }

            if (!empty($filters['status'])) {
                $sql .= " AND n.status = :st";
                $params['st'] = $filters['status'];
            }

            $sql .= " ORDER BY n.id DESC LIMIT 100";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
