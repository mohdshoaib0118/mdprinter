<?php
/**
 * MD Printers - B2B Pricing Engine & Options Management
 */
require_once CONFIG_PATH . '/database.php';
require_once MODELS_PATH . '/Customer.php';

class PricingEngine {

    public static function initTables() {
        $db = Database::getInstance();
        if (!$db) return;

        try {
            // 1. Product Option Groups
            $db->exec("CREATE TABLE IF NOT EXISTS `product_options` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `product_id` INT NOT NULL,
                `option_name` VARCHAR(191) NOT NULL,
                `option_type` ENUM('select', 'radio') NOT NULL DEFAULT 'select',
                `is_required` TINYINT(1) NOT NULL DEFAULT 1,
                `sort_order` INT NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_product` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // 2. Product Option Values with Modifiers
            $db->exec("CREATE TABLE IF NOT EXISTS `product_option_values` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `option_id` INT NOT NULL,
                `value_name` VARCHAR(191) NOT NULL,
                `modifier_type` ENUM('fixed', 'per_unit', 'percentage') NOT NULL DEFAULT 'fixed',
                `price_modifier` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `is_default` TINYINT(1) NOT NULL DEFAULT 0,
                `sort_order` INT NOT NULL DEFAULT 0,
                FOREIGN KEY (`option_id`) REFERENCES `product_options`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // 3. Product Quantity Slabs
            $db->exec("CREATE TABLE IF NOT EXISTS `product_pricing_slabs` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `product_id` INT NOT NULL,
                `quantity` INT NOT NULL,
                `base_price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `regular_discount_pct` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                `silver_discount_pct` DECIMAL(5,2) NOT NULL DEFAULT 5.00,
                `gold_discount_pct` DECIMAL(5,2) NOT NULL DEFAULT 10.00,
                `platinum_discount_pct` DECIMAL(5,2) NOT NULL DEFAULT 15.00,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY `uniq_prod_qty` (`product_id`, `quantity`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // Seed default options and slabs for Product 1, 2, 3 if table is empty
            self::seedDefaultsIfEmpty($db);
        } catch (\Throwable $e) {
            // Ignore if tables exist
        }
    }

    private static function seedDefaultsIfEmpty($db) {
        $stmt = $db->query("SELECT COUNT(*) FROM product_options");
        if ((int)$stmt->fetchColumn() > 0) return;

        // Seed Product 1 (Executive Gold Foil Business Cards - ID 1)
        $db->exec("INSERT INTO product_pricing_slabs (product_id, quantity, base_price) VALUES 
            (1, 100, 199.00), (1, 250, 299.00), (1, 500, 499.00), 
            (1, 1000, 799.00), (1, 2500, 1499.00), (1, 5000, 2499.00), (1, 10000, 4499.00)");

        // Options for Product 1
        $db->exec("INSERT INTO product_options (id, product_id, option_name, option_type, sort_order) VALUES
            (1, 1, 'Paper Stock', 'select', 1),
            (2, 1, 'Printing Sides', 'select', 2),
            (3, 1, 'Lamination', 'select', 3),
            (4, 1, 'Special Finish', 'select', 4),
            (5, 1, 'Corner Cut', 'select', 5)");

        $db->exec("INSERT INTO product_option_values (option_id, value_name, modifier_type, price_modifier, is_default) VALUES
            (1, '300 GSM Art Card', 'fixed', 0.00, 1),
            (1, '350 GSM Heavy Board', 'fixed', 50.00, 0),
            (1, '400 GSM Velvet Core Board', 'fixed', 120.00, 0),
            (2, 'Single Side Print', 'fixed', 0.00, 1),
            (2, 'Both Side Print', 'fixed', 60.00, 0),
            (3, 'None', 'fixed', 0.00, 0),
            (3, 'Matte Lamination', 'fixed', 80.00, 1),
            (3, 'Gloss Lamination', 'fixed', 80.00, 0),
            (4, 'None', 'fixed', 0.00, 1),
            (4, 'Gold Foil Stamping', 'per_unit', 0.50, 0),
            (4, 'Spot UV 3D', 'fixed', 150.00, 0),
            (4, 'Embossed Logo', 'fixed', 200.00, 0),
            (5, 'Square Corners', 'fixed', 0.00, 1),
            (5, 'Round Corners', 'fixed', 40.00, 0)");

        // Seed Product 2 (Luxury Corporate Tri-Fold Brochure - ID 2)
        $db->exec("INSERT INTO product_pricing_slabs (product_id, quantity, base_price) VALUES 
            (2, 250, 899.00), (2, 500, 1499.00), (2, 1000, 2499.00), (2, 2500, 4999.00)");

        $db->exec("INSERT INTO product_options (id, product_id, option_name, option_type, sort_order) VALUES
            (6, 2, 'Paper Type', 'select', 1),
            (7, 2, 'Lamination', 'select', 2)");

        $db->exec("INSERT INTO product_option_values (option_id, value_name, modifier_type, price_modifier, is_default) VALUES
            (6, '170 GSM Gloss Art', 'fixed', 0.00, 1),
            (6, '250 GSM Matte Board', 'fixed', 150.00, 0),
            (7, 'None', 'fixed', 0.00, 1),
            (7, 'Thermal Matte Lamination', 'fixed', 120.00, 0)");

        // Seed Product 3 (Custom Rigid Magnetic Gift Box - ID 3)
        $db->exec("INSERT INTO product_pricing_slabs (product_id, quantity, base_price) VALUES 
            (3, 50, 2499.00), (3, 100, 4499.00), (3, 250, 9999.00)");

        $db->exec("INSERT INTO product_options (id, product_id, option_name, option_type, sort_order) VALUES
            (8, 3, 'Rigid Board Thickness', 'select', 1),
            (9, 3, 'Branding Stamping', 'select', 2)");

        $db->exec("INSERT INTO product_option_values (option_id, value_name, modifier_type, price_modifier, is_default) VALUES
            (8, '1.5mm Heavy Greyboard', 'fixed', 0.00, 1),
            (8, '2.5mm Ultra Rigid Board', 'fixed', 300.00, 0),
            (9, 'Debossed Logo', 'fixed', 0.00, 1),
            (9, 'Rose Gold Metallic Foil', 'per_unit', 5.00, 0)");
    }

    /**
     * Get all options and values for a product
     */
    public static function getProductOptions(int $productId): array {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }
        if (!$db) return [];

        try {
            $stmt = $db->prepare("SELECT * FROM product_options WHERE product_id = :pid ORDER BY sort_order ASC, id ASC");
            $stmt->execute(['pid' => $productId]);
            $options = $stmt->fetchAll();

            foreach ($options as &$opt) {
                $vStmt = $db->prepare("SELECT * FROM product_option_values WHERE option_id = :oid ORDER BY sort_order ASC, id ASC");
                $vStmt->execute(['oid' => $opt['id']]);
                $opt['values'] = $vStmt->fetchAll();
            }

            if (empty($options)) {
                // Fallback to JSON options stored in products table if product_options rows don't exist
                $pStmt = $db->prepare("SELECT paper_options_json, finishing_options_json FROM products WHERE id = :pid LIMIT 1");
                $pStmt->execute(['pid' => $productId]);
                $pRow = $pStmt->fetch();

                if ($pRow) {
                    $virtualId = 9000;
                    if (!empty($pRow['paper_options_json'])) {
                        $papers = json_decode($pRow['paper_options_json'], true);
                        if (is_array($papers) && !empty($papers)) {
                            $vals = [];
                            foreach ($papers as $idx => $paperName) {
                                $vals[] = [
                                    'id' => $virtualId + $idx + 1,
                                    'option_id' => $virtualId + 1,
                                    'value_name' => $paperName,
                                    'modifier_type' => 'fixed',
                                    'price_modifier' => 0.00,
                                    'is_default' => ($idx === 0 ? 1 : 0),
                                    'sort_order' => $idx + 1
                                ];
                            }
                            $options[] = [
                                'id' => $virtualId + 1,
                                'product_id' => $productId,
                                'option_name' => 'Paper Stock',
                                'option_type' => 'select',
                                'is_required' => 1,
                                'sort_order' => 1,
                                'values' => $vals
                            ];
                        }
                    }

                    if (!empty($pRow['finishing_options_json'])) {
                        $finishes = json_decode($pRow['finishing_options_json'], true);
                        if (is_array($finishes) && !empty($finishes)) {
                            $vals = [];
                            foreach ($finishes as $idx => $finishName) {
                                $vals[] = [
                                    'id' => $virtualId + 100 + $idx + 1,
                                    'option_id' => $virtualId + 2,
                                    'value_name' => $finishName,
                                    'modifier_type' => 'fixed',
                                    'price_modifier' => 0.00,
                                    'is_default' => ($idx === 0 ? 1 : 0),
                                    'sort_order' => $idx + 1
                                ];
                            }
                            $options[] = [
                                'id' => $virtualId + 2,
                                'product_id' => $productId,
                                'option_name' => 'Lamination / Finishing',
                                'option_type' => 'select',
                                'is_required' => 0,
                                'sort_order' => 2,
                                'values' => $vals
                            ];
                        }
                    }
                }
            }

            return $options;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get quantity slabs for a product
     */
    public static function getQuantitySlabs(int $productId): array {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }
        if (!$db) return [];

        try {
            $stmt = $db->prepare("SELECT * FROM product_pricing_slabs WHERE product_id = :pid ORDER BY quantity ASC");
            $stmt->execute(['pid' => $productId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Server-side calculation engine
     */
    public static function calculatePrice(int $productId, int $quantity, array $selectedOptionValues, ?int $customerId = null): array {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }

        // 1. Fetch Customer Tier
        $tier = 'regular';
        $customerObj = null;
        if ($customerId) {
            $customerObj = Customer::findById($customerId);
            if ($customerObj && !empty($customerObj['tier'])) {
                $tier = strtolower($customerObj['tier']);
            }
        }

        // Tier discount mapping
        $tierDiscounts = [
            'regular' => 0.0,
            'silver' => 5.0,
            'gold' => 10.0,
            'platinum' => 15.0
        ];
        $tierDiscountPct = $tierDiscounts[$tier] ?? 0.0;

        // 2. Fetch Product & Quantity Slab
        $slabs = self::getQuantitySlabs($productId);
        $basePrice = 0.00;
        $matchedSlab = null;
        $isPriced = !empty($slabs);

        if (!empty($slabs)) {
            foreach ($slabs as $s) {
                if ((int)$s['quantity'] === (int)$quantity) {
                    $matchedSlab = $s;
                    break;
                }
            }
            if (!$matchedSlab) {
                // If quantity not exactly matching, pick closest slab or calculate proportionally
                $matchedSlab = $slabs[0];
                foreach ($slabs as $s) {
                    if ((int)$s['quantity'] <= (int)$quantity) {
                        $matchedSlab = $s;
                    }
                }
            }
            $basePrice = (float)$matchedSlab['base_price'];

            // Override tier discount if defined on slab
            $tierCol = $tier . '_discount_pct';
            if (isset($matchedSlab[$tierCol]) && is_numeric($matchedSlab[$tierCol])) {
                $tierDiscountPct = (float)$matchedSlab[$tierCol];
            }
        } else {
            // No pricing slab exists for this product
            $basePrice = 0.00;
            $isPriced = false;
        }

        // 3. Process Option Modifiers
        $optionsBreakdown = [];
        $optionsTotal = 0.00;

        $availableOptions = self::getProductOptions($productId);

        foreach ($availableOptions as $opt) {
            $optId = $opt['id'];
            $selectedValId = $selectedOptionValues[$optId] ?? null;

            $matchedVal = null;
            if ($selectedValId && !empty($opt['values'])) {
                foreach ($opt['values'] as $val) {
                    if ((int)$val['id'] === (int)$selectedValId) {
                        $matchedVal = $val;
                        break;
                    }
                }
            }
            if (!$matchedVal && !empty($opt['values'])) {
                // Default to first option value if not selected
                $matchedVal = $opt['values'][0];
                foreach ($opt['values'] as $v) {
                    if (!empty($v['is_default'])) {
                        $matchedVal = $v;
                        break;
                    }
                }
            }

            if ($matchedVal) {
                $modType = $matchedVal['modifier_type'];
                $modVal = (float)$matchedVal['price_modifier'];
                $addedAmount = 0.00;

                if ($modType === 'fixed') {
                    $addedAmount = $modVal;
                } elseif ($modType === 'per_unit') {
                    $addedAmount = $modVal * (int)$quantity;
                } elseif ($modType === 'percentage') {
                    $addedAmount = ($basePrice * $modVal) / 100.00;
                }

                $optionsTotal += $addedAmount;

                $optionsBreakdown[] = [
                    'option_id' => $opt['id'],
                    'option_name' => $opt['option_name'],
                    'value_id' => $matchedVal['id'],
                    'value_name' => $matchedVal['value_name'],
                    'modifier_type' => $modType,
                    'modifier_val' => $modVal,
                    'added_amount' => round($addedAmount, 2)
                ];
            }
        }

        // 4. Calculate Subtotals & Discounts
        $grossSubtotal = $basePrice + $optionsTotal;
        $tierDiscountAmount = round(($grossSubtotal * $tierDiscountPct) / 100.00, 2);
        $netSubtotal = round($grossSubtotal - $tierDiscountAmount, 2);

        // 5. Tax (18% GST)
        $taxPct = 18.00;
        $taxAmount = round(($netSubtotal * $taxPct) / 100.00, 2);

        // 6. Final Total
        $finalTotal = round($netSubtotal + $taxAmount, 2);

        return [
            'product_id' => $productId,
            'quantity' => (int)$quantity,
            'is_priced' => ($isPriced && $finalTotal > 0),
            'price_status_message' => ($isPriced && $finalTotal > 0) ? 'OK' : 'Pricing not configured — Request Quote',
            'base_price' => round($basePrice, 2),
            'options_breakdown' => $optionsBreakdown,
            'options_total' => round($optionsTotal, 2),
            'gross_subtotal' => round($grossSubtotal, 2),
            'tier_name' => strtoupper($tier),
            'tier_discount_pct' => round($tierDiscountPct, 2),
            'tier_discount_amount' => round($tierDiscountAmount, 2),
            'net_subtotal' => round($netSubtotal, 2),
            'tax_pct' => round($taxPct, 2),
            'tax_amount' => round($taxAmount, 2),
            'final_total' => round($finalTotal, 2)
        ];
    }
}
