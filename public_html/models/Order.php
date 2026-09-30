<?php
/**
 * MD Printers - B2B Order Management Model
 */
require_once CONFIG_PATH . '/database.php';
require_once MODELS_PATH . '/PricingEngine.php';
require_once MODELS_PATH . '/Customer.php';

class Order {

    public static function initTables() {
        $db = Database::getInstance();
        if (!$db) return;

        try {
            // 1. Orders Table
            $db->exec("CREATE TABLE IF NOT EXISTS `orders` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `order_number` VARCHAR(50) NOT NULL UNIQUE,
                `customer_id` INT NOT NULL,
                `quote_id` INT DEFAULT NULL,
                `status` ENUM(
                    'pending', 'payment_pending', 'confirmed', 
                    'artwork_pending', 'artwork_under_review', 'artwork_correction_required', 'artwork_approved',
                    'production_queued', 'production_started', 'printing', 'finishing', 'quality_check',
                    'ready_to_dispatch', 'packed', 'dispatched', 'out_for_delivery', 'delivered',
                    'on_hold', 'cancelled'
                ) NOT NULL DEFAULT 'pending',
                `payment_status` ENUM('unpaid', 'partially_paid', 'paid', 'refunded') NOT NULL DEFAULT 'unpaid',
                `payment_method` VARCHAR(50) NOT NULL DEFAULT 'bank_transfer',
                `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `discount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `taxable_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `tax` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `cgst` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `sgst` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `igst` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `shipping` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `total` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `billing_address_json` TEXT DEFAULT NULL,
                `shipping_address_json` TEXT DEFAULT NULL,
                `courier_name` VARCHAR(100) DEFAULT NULL,
                `transporter_name` VARCHAR(100) DEFAULT NULL,
                `tracking_number` VARCHAR(100) DEFAULT NULL,
                `tracking_url` VARCHAR(255) DEFAULT NULL,
                `dispatch_date` DATETIME DEFAULT NULL,
                `expected_delivery_date` DATETIME DEFAULT NULL,
                `customer_notes` TEXT DEFAULT NULL,
                `admin_notes` TEXT DEFAULT NULL,
                `placed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `confirmed_at` DATETIME DEFAULT NULL,
                `completed_at` DATETIME DEFAULT NULL,
                `cancelled_at` DATETIME DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_cust` (`customer_id`),
                INDEX `idx_status` (`status`),
                INDEX `idx_pay` (`payment_status`),
                INDEX `idx_ord_num` (`order_number`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // 2. Order Line Items Table
            $db->exec("CREATE TABLE IF NOT EXISTS `order_items` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `order_id` INT NOT NULL,
                `product_id` INT NOT NULL,
                `product_name_snapshot` VARCHAR(191) NOT NULL,
                `quantity` INT NOT NULL,
                `configuration_json` TEXT DEFAULT NULL,
                `pricing_breakdown_json` TEXT DEFAULT NULL,
                `unit_price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `tax` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `total` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `artwork_status` ENUM('pending', 'uploaded', 'under_review', 'correction_required', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
                `approved_artwork_id` INT DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // 3. Order Status History Timeline Table
            $db->exec("CREATE TABLE IF NOT EXISTS `order_status_history` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `order_id` INT NOT NULL,
                `old_status` VARCHAR(50) DEFAULT NULL,
                `new_status` VARCHAR(50) NOT NULL,
                `changed_by_type` ENUM('customer', 'admin', 'system') NOT NULL DEFAULT 'system',
                `changed_by_id` INT DEFAULT NULL,
                `comment` TEXT DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        } catch (\Throwable $e) {
            // Table creation error ignored if exists
        }
    }

    public static function generateOrderNumber(): string {
        return 'MDP-' . date('Y') . '-' . str_pad((string)rand(10000, 99999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create an order using a database transaction and server-side authoritative price recalculation
     */
    public static function createOrder(array $data): ?array {
        self::initTables();
        PricingEngine::initTables();
        $db = Database::getInstance();
        if (!$db) return null;

        $customerId = (int)$data['customer_id'];
        $customer = Customer::findById($customerId);
        if (!$customer) return null;

        $db->beginTransaction();

        try {
            $orderNumber = self::generateOrderNumber();
            $quoteId = !empty($data['quote_id']) ? (int)$data['quote_id'] : null;

            // Recalculate all items server-side
            $itemsData = $data['items'] ?? [];
            $processedItems = [];
            $calcSubtotal = 0.00;
            $calcTax = 0.00;
            $calcTotal = 0.00;

            foreach ($itemsData as $item) {
                $productId = (int)$item['product_id'];
                $quantity = (int)$item['quantity'];
                $selectedOptions = $item['options'] ?? [];

                // Fetch product name
                $pStmt = $db->prepare("SELECT name FROM products WHERE id = :pid");
                $pStmt->execute(['pid' => $productId]);
                $pName = $pStmt->fetchColumn() ?: ($item['product_name'] ?? 'Custom Print Product');

                // Authoritative calculation via PricingEngine
                $calc = PricingEngine::calculatePrice($productId, $quantity, $selectedOptions, $customerId);

                // Config snapshot labels
                $availableOptions = PricingEngine::getProductOptions($productId);
                $configSnapshot = ['Quantity' => $quantity . ' units'];
                foreach ($availableOptions as $opt) {
                    $selValId = $selectedOptions[$opt['id']] ?? null;
                    if ($selValId && !empty($opt['values'])) {
                        foreach ($opt['values'] as $v) {
                            if ((int)$v['id'] === (int)$selValId) {
                                $configSnapshot[$opt['option_name']] = $v['value_name'];
                                break;
                            }
                        }
                    }
                }

                $itemSubtotal = $calc['net_subtotal'];
                $itemTax = $calc['tax_amount'];
                $itemTotal = $calc['final_total'];
                $unitPrice = round($itemTotal / max(1, $quantity), 2);

                $calcSubtotal += $itemSubtotal;
                $calcTax += $itemTax;
                $calcTotal += $itemTotal;

                $processedItems[] = [
                    'product_id' => $productId,
                    'product_name_snapshot' => $pName,
                    'quantity' => $quantity,
                    'configuration_json' => json_encode($configSnapshot),
                    'pricing_breakdown_json' => json_encode($calc),
                    'unit_price' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                    'tax' => $itemTax,
                    'total' => $itemTotal
                ];
            }

            // Determine Intra-state vs Inter-state GST (Delhi default)
            $shippingAddress = $data['shipping_address'] ?? [];
            $billingAddress = $data['billing_address'] ?? [];
            $state = strtolower(trim($shippingAddress['state'] ?? 'delhi'));
            $isSameState = ($state === 'delhi');

            $cgst = 0.00;
            $sgst = 0.00;
            $igst = 0.00;

            if ($isSameState) {
                $cgst = round($calcTax / 2.0, 2);
                $sgst = round($calcTax / 2.0, 2);
            } else {
                $igst = $calcTax;
            }

            $shippingFee = (float)($data['shipping'] ?? 0.00);
            $grandTotal = $calcTotal + $shippingFee;

            $initialStatus = $data['status'] ?? 'pending';
            $paymentMethod = $data['payment_method'] ?? 'bank_transfer';
            $paymentStatus = ($paymentMethod === 'approved_credit') ? 'paid' : 'unpaid';

            // Insert Order Record
            $stmt = $db->prepare("INSERT INTO orders (
                order_number, customer_id, quote_id, status, payment_status, payment_method,
                subtotal, discount, taxable_amount, tax, cgst, sgst, igst, shipping, total,
                billing_address_json, shipping_address_json, customer_notes, placed_at
            ) VALUES (
                :ord_num, :cid, :qid, :status, :pay_status, :pay_method,
                :subtotal, :disc, :taxable, :tax, :cgst, :sgst, :igst, :ship, :total,
                :billing, :shipping, :cnotes, NOW()
            )");

            $stmt->execute([
                'ord_num' => $orderNumber,
                'cid' => $customerId,
                'qid' => $quoteId,
                'status' => $initialStatus,
                'pay_status' => $paymentStatus,
                'pay_method' => $paymentMethod,
                'subtotal' => $calcSubtotal,
                'disc' => 0.00,
                'taxable' => $calcSubtotal,
                'tax' => $calcTax,
                'cgst' => $cgst,
                'sgst' => $sgst,
                'igst' => $igst,
                'ship' => $shippingFee,
                'total' => $grandTotal,
                'billing' => json_encode($billingAddress),
                'shipping' => json_encode($shippingAddress),
                'cnotes' => $data['customer_notes'] ?? ''
            ]);

            $orderId = (int)$db->lastInsertId();

            // Insert Order Line Items
            $itemStmt = $db->prepare("INSERT INTO order_items (
                order_id, product_id, product_name_snapshot, quantity,
                configuration_json, pricing_breakdown_json, unit_price, subtotal, tax, total
            ) VALUES (
                :oid, :pid, :pname, :qty, :config, :breakdown, :uprice, :subtotal, :tax, :total
            )");

            foreach ($processedItems as $pi) {
                $itemStmt->execute([
                    'oid' => $orderId,
                    'pid' => $pi['product_id'],
                    'pname' => $pi['product_name_snapshot'],
                    'qty' => $pi['quantity'],
                    'config' => $pi['configuration_json'],
                    'breakdown' => $pi['pricing_breakdown_json'],
                    'uprice' => $pi['unit_price'],
                    'subtotal' => $pi['subtotal'],
                    'tax' => $pi['tax'],
                    'total' => $pi['total']
                ]);
            }

            // Insert Initial Status History
            $historyStmt = $db->prepare("INSERT INTO order_status_history (
                order_id, old_status, new_status, changed_by_type, changed_by_id, comment
            ) VALUES (:oid, NULL, :new_status, 'customer', :cid, :comment)");

            $historyStmt->execute([
                'oid' => $orderId,
                'new_status' => $initialStatus,
                'cid' => $customerId,
                'comment' => 'B2B order placed successfully by customer.'
            ]);

            // Mark Quote converted if converted from formal quote
            if ($quoteId) {
                $qStmt = $db->prepare("UPDATE quotes SET status = 'converted', converted_to_order_id = :oid WHERE id = :qid");
                $qStmt->execute(['oid' => $orderId, 'qid' => $quoteId]);
            }

            $db->commit();

            return [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'total' => $grandTotal
            ];

        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            echo "Order creation exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
            return null;
        }
    }

    public static function getForCustomer(int $customerId): array {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }
        if (!$db) return [];

        try {
            $stmt = $db->prepare("SELECT * FROM orders WHERE customer_id = :cid ORDER BY id DESC");
            $stmt->execute(['cid' => $customerId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function findById(int $orderId, ?int $customerId = null): ?array {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }
        if (!$db) return null;

        try {
            $sql = "SELECT o.*, c.company_name, c.contact_person, c.email, c.mobile, c.gst_number as gstin 
                    FROM orders o 
                    LEFT JOIN customers c ON o.customer_id = c.id 
                    WHERE o.id = :id";
            $params = ['id' => $orderId];

            if ($customerId !== null) {
                $sql .= " AND o.customer_id = :cid";
                $params['cid'] = $customerId;
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $order = $stmt->fetch();
            if (!$order) return null;

            // Fetch Line Items
            $itemStmt = $db->prepare("SELECT * FROM order_items WHERE order_id = :oid");
            $itemStmt->execute(['oid' => $orderId]);
            $order['items'] = $itemStmt->fetchAll();

            // Fetch Status History Timeline
            $histStmt = $db->prepare("SELECT * FROM order_status_history WHERE order_id = :oid ORDER BY id ASC");
            $histStmt->execute(['oid' => $orderId]);
            $order['timeline'] = $histStmt->fetchAll();

            return $order;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function findByOrderNumber(string $orderNumber): ?array {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }
        if (!$db) return null;

        try {
            $stmt = $db->prepare("SELECT o.*, c.company_name, c.contact_person 
                                  FROM orders o 
                                  LEFT JOIN customers c ON o.customer_id = c.id 
                                  WHERE o.order_number = :ord_num LIMIT 1");
            $stmt->execute(['ord_num' => trim($orderNumber)]);
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function updateStatus(int $orderId, string $newStatus, string $changedByType = 'admin', ?int $changedById = null, string $comment = ''): bool {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }
        if (!$db) return false;

        try {
            $stmt = $db->prepare("SELECT status FROM orders WHERE id = :id");
            $stmt->execute(['id' => $orderId]);
            $oldStatus = $stmt->fetchColumn() ?: 'pending';

            if ($oldStatus === $newStatus) return true;

            $uStmt = $db->prepare("UPDATE orders SET status = :status WHERE id = :id");
            $uStmt->execute(['status' => $newStatus, 'id' => $orderId]);

            // Append status history timeline
            $hStmt = $db->prepare("INSERT INTO order_status_history (
                order_id, old_status, new_status, changed_by_type, changed_by_id, comment
            ) VALUES (:oid, :old_st, :new_st, :btype, :bid, :comment)");

            $hStmt->execute([
                'oid' => $orderId,
                'old_st' => $oldStatus,
                'new_st' => $newStatus,
                'btype' => $changedByType,
                'bid' => $changedById,
                'comment' => $comment ?: ('Order status changed to ' . ucwords(str_replace('_', ' ', $newStatus)))
            ]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function updateShipping(int $orderId, array $data): bool {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }
        if (!$db) return false;

        try {
            $stmt = $db->prepare("UPDATE orders SET 
                courier_name = :cname,
                transporter_name = :tname,
                tracking_number = :tnum,
                tracking_url = :turl,
                dispatch_date = :ddate,
                expected_delivery_date = :edate
                WHERE id = :id");

            return $stmt->execute([
                'cname' => $data['courier_name'] ?? null,
                'tname' => $data['transporter_name'] ?? null,
                'tnum' => $data['tracking_number'] ?? null,
                'turl' => $data['tracking_url'] ?? null,
                'ddate' => !empty($data['dispatch_date']) ? $data['dispatch_date'] : null,
                'edate' => !empty($data['expected_delivery_date']) ? $data['expected_delivery_date'] : null,
                'id' => $orderId
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getAll(array $filters = []): array {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }
        if (!$db) return [];

        try {
            $sql = "SELECT o.*, c.company_name, c.contact_person, c.email, c.mobile 
                    FROM orders o 
                    LEFT JOIN customers c ON o.customer_id = c.id 
                    WHERE 1=1";
            $params = [];

            if (!empty($filters['status'])) {
                $sql .= " AND o.status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['payment_status'])) {
                $sql .= " AND o.payment_status = :pay_status";
                $params['pay_status'] = $filters['payment_status'];
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (o.order_number LIKE :search OR c.company_name LIKE :search OR c.contact_person LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $sql .= " ORDER BY o.id DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * One-Click Reorder: Load past order items and recalculate CURRENT server-side prices into cart
     */
    public static function reorderToCart(int $orderId, int $customerId): array {
        require_once MODELS_PATH . '/Cart.php';
        $order = self::findById($orderId, $customerId);
        if (!$order) {
            return ['success' => false, 'message' => 'Order not found or unauthorized access.'];
        }

        $db = Database::getInstance();
        $addedCount = 0;
        $unavailableCount = 0;

        foreach ($order['items'] as $item) {
            $pid = (int)$item['product_id'];
            $qty = (int)$item['quantity'];

            // Check if product exists and is active
            $pStmt = $db->prepare("SELECT id, name, status FROM products WHERE id = :pid AND status = 'published' LIMIT 1");
            $pStmt->execute(['pid' => $pid]);
            $product = $pStmt->fetch();

            if (!$product) {
                $unavailableCount++;
                continue;
            }

            // Map configuration options back to option values if stored
            $availableOptions = PricingEngine::getProductOptions($pid);
            $selectedOptions = [];
            $cfg = json_decode($item['configuration_json'] ?? '{}', true);

            foreach ($availableOptions as $opt) {
                $optName = $opt['option_name'];
                $valName = $cfg[$optName] ?? null;

                $matchedValId = null;
                if ($valName && !empty($opt['values'])) {
                    foreach ($opt['values'] as $v) {
                        if (strtolower(trim($v['value_name'])) === strtolower(trim($valName))) {
                            $matchedValId = $v['id'];
                            break;
                        }
                    }
                }
                if (!$matchedValId && !empty($opt['values'])) {
                    $matchedValId = $opt['values'][0]['id'];
                }
                if ($matchedValId) {
                    $selectedOptions[$opt['id']] = $matchedValId;
                }
            }

            // Recalculate using CURRENT pricing engine rules
            $recalculated = PricingEngine::calculatePrice($pid, $qty, $selectedOptions, $customerId);

            if (Cart::add($pid, $product['name'], $qty, $cfg, $recalculated, $customerId)) {
                $addedCount++;
            }
        }

        if ($addedCount > 0) {
            $msg = $addedCount . ' item(s) from Order #' . $order['order_number'] . ' added to your B2B cart at current server prices!';
            if ($unavailableCount > 0) {
                $msg .= ' (' . $unavailableCount . ' item(s) no longer available).';
            }
            return ['success' => true, 'message' => $msg];
        } else {
            return ['success' => false, 'message' => 'None of the items from this past order are currently available for reorder.'];
        }
    }
}
