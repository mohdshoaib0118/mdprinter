<?php
/**
 * MD Printers - B2B GST Invoice Model & Real PDF Generator
 */
require_once CONFIG_PATH . '/database.php';
require_once MODELS_PATH . '/Setting.php';

class Invoice {

    public static function initTables() {
        $db = Database::getInstance();
        if (!$db) return;

        try {
            $db->exec("CREATE TABLE IF NOT EXISTS `invoices` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
                `order_id` INT NOT NULL UNIQUE,
                `customer_id` INT NOT NULL,
                `invoice_date` DATETIME NOT NULL,
                `seller_snapshot_json` TEXT DEFAULT NULL,
                `buyer_snapshot_json` TEXT DEFAULT NULL,
                `gstin` VARCHAR(30) DEFAULT NULL,
                `place_of_supply` VARCHAR(100) DEFAULT NULL,
                `taxable_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `cgst` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `sgst` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `igst` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `shipping` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `total` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `status` ENUM('issued', 'cancelled') NOT NULL DEFAULT 'issued',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_order` (`order_id`),
                INDEX `idx_cust` (`customer_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        } catch (\Throwable $e) {
            // Ignore if exists
        }
    }

    public static function generateInvoiceNumber(): string {
        return 'INV-' . date('Y') . '-' . str_pad((string)rand(10000, 99999), 6, '0', STR_PAD_LEFT);
    }

    public static function generateForOrder(array $order): ?array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return null;

        // Check if invoice already exists (Idempotent - reuses exact same invoice)
        $existing = self::findByOrderId($order['id']);
        if ($existing) return $existing;

        $sellerSnapshot = [
            'name' => Setting::get('site_name', 'MD PRINTERS'),
            'gstin' => Setting::get('gstin_number', '07AAAAA0000A1Z5'),
            'address' => Setting::get('contact_address', 'Industrial Area Phase II, Commercial Print Zone, New Delhi 110020'),
            'phone' => Setting::get('contact_phone', '+91 81461 41450'),
            'email' => Setting::get('contact_email', 'info@mdprinters.com')
        ];

        $buyerAddress = json_decode($order['billing_address_json'] ?? '{}', true);

        $invoiceNumber = self::generateInvoiceNumber();

        try {
            $stmt = $db->prepare("INSERT INTO invoices (
                invoice_number, order_id, customer_id, invoice_date, seller_snapshot_json,
                buyer_snapshot_json, gstin, place_of_supply, taxable_amount, cgst, sgst, igst, shipping, total, status
            ) VALUES (
                :inv_num, :oid, :cid, NOW(), :seller,
                :buyer, :gstin, :pos, :taxable, :cgst, :sgst, :igst, :ship, :total, 'issued'
            )");

            $stmt->execute([
                'inv_num' => $invoiceNumber,
                'oid' => $order['id'],
                'cid' => $order['customer_id'],
                'seller' => json_encode($sellerSnapshot),
                'buyer' => json_encode($buyerAddress),
                'gstin' => $order['gstin'] ?? ($buyerAddress['gstin'] ?? ''),
                'pos' => $buyerAddress['state'] ?? 'Delhi',
                'taxable' => $order['taxable_amount'],
                'cgst' => $order['cgst'],
                'sgst' => $order['sgst'],
                'igst' => $order['igst'],
                'ship' => $order['shipping'],
                'total' => $order['total']
            ]);

            return self::findByOrderId($order['id']);
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function findByOrderId(int $orderId): ?array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return null;

        try {
            $stmt = $db->prepare("SELECT * FROM invoices WHERE order_id = :oid LIMIT 1");
            $stmt->execute(['oid' => $orderId]);
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Render HTML structure optimized for Dompdf layout
     */
    public static function renderHtmlForPdf(array $order, array $invoice): string {
        $seller = json_decode($invoice['seller_snapshot_json'] ?? '{}', true);
        $buyer = json_decode($invoice['buyer_snapshot_json'] ?? '{}', true);

        $itemsHtml = '';
        foreach ($order['items'] as $it) {
            $cfg = json_decode($it['configuration_json'] ?? '{}', true);
            $cfgStr = '';
            if (is_array($cfg)) {
                foreach ($cfg as $k => $v) {
                    $cfgStr .= htmlspecialchars($k) . ': ' . htmlspecialchars($v) . ' | ';
                }
                $cfgStr = rtrim($cfgStr, ' | ');
            }

            $itemsHtml .= '<tr>
                <td>
                    <strong>' . htmlspecialchars($it['product_name_snapshot']) . '</strong>';
            if ($cfgStr !== '') {
                $itemsHtml .= '<div style="font-size: 9px; color: #64748b; margin-top: 2px;">' . $cfgStr . '</div>';
            }
            $itemsHtml .= '</td>
                <td style="text-align: center;">' . (int)$it['quantity'] . '</td>
                <td style="text-align: right;">₹' . number_format((float)$it['unit_price'], 2) . '</td>
                <td style="text-align: right;">₹' . number_format((float)$it['subtotal'], 2) . '</td>
            </tr>';
        }

        $taxHtml = '';
        if ((float)$invoice['cgst'] > 0) {
            $taxHtml .= '<tr>
                <td style="padding: 3px 0;">CGST (9%):</td>
                <td style="text-align: right; font-weight: bold; padding: 3px 0;">₹' . number_format((float)$invoice['cgst'], 2) . '</td>
            </tr>
            <tr>
                <td style="padding: 3px 0;">SGST (9%):</td>
                <td style="text-align: right; font-weight: bold; padding: 3px 0;">₹' . number_format((float)$invoice['sgst'], 2) . '</td>
            </tr>';
        } else {
            $taxHtml .= '<tr>
                <td style="padding: 3px 0;">IGST (18%):</td>
                <td style="text-align: right; font-weight: bold; padding: 3px 0;">₹' . number_format((float)$invoice['igst'], 2) . '</td>
            </tr>';
        }

        $shippingHtml = '';
        if ((float)$invoice['shipping'] > 0) {
            $shippingHtml = '<tr>
                <td style="padding: 3px 0;">Shipping Fee:</td>
                <td style="text-align: right; font-weight: bold; padding: 3px 0;">₹' . number_format((float)$invoice['shipping'], 2) . '</td>
            </tr>';
        }

        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tax Invoice #' . htmlspecialchars($invoice['invoice_number']) . '</title>
    <style>
        body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 11px; color: #0f172a; margin: 0; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        .header-table { border-bottom: 2px solid #00a8e8; padding-bottom: 12px; margin-bottom: 20px; }
        .title { color: #00a8e8; font-size: 20px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .subtitle { color: #64748b; font-size: 10px; margin-top: 3px; text-transform: uppercase; }
        .seller-name { font-size: 14px; font-weight: bold; text-align: right; color: #0f172a; }
        .seller-gst { font-size: 10px; color: #64748b; text-align: right; margin-top: 3px; }
        
        .address-table { margin-bottom: 20px; }
        .address-box { width: 48%; vertical-align: top; }
        .section-label { color: #64748b; font-size: 9px; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; }
        .customer-company { font-size: 13px; font-weight: bold; color: #0f172a; }
        
        .items-table { margin-bottom: 20px; }
        .items-table th { background: #f8fafc; border: 1px solid #cbd5e1; padding: 8px; font-weight: bold; font-size: 10px; text-align: left; }
        .items-table td { border: 1px solid #e2e8f0; padding: 8px; font-size: 10px; vertical-align: top; }
        
        .summary-table { width: 45%; margin-left: auto; border-collapse: collapse; }
        .total-row { border-top: 2px solid #0f172a; font-size: 13px; font-weight: bold; color: #00a8e8; }
        .footer { margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 12px; text-align: center; font-size: 9px; color: #64748b; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="vertical-align: top;">
                <div class="title">TAX INVOICE</div>
                <div class="subtitle">ORIGINAL FOR RECIPIENT</div>
            </td>
            <td style="vertical-align: top; text-align: right;">
                <div class="seller-name">' . htmlspecialchars($seller['name'] ?? 'MD PRINTERS') . '</div>
                <div class="seller-gst">GSTIN: <strong>' . htmlspecialchars($seller['gstin'] ?? '07AAAAA0000A1Z5') . '</strong></div>
                <div style="font-size: 9px; color: #64748b;">' . htmlspecialchars($seller['address'] ?? 'New Delhi 110020') . '</div>
            </td>
        </tr>
    </table>

    <table class="address-table">
        <tr>
            <td class="address-box">
                <div class="section-label">Billed To (Customer):</div>
                <div class="customer-company">' . htmlspecialchars($order['company_name'] ?: ($order['contact_person'] ?: 'B2B Client')) . '</div>
                <div>' . htmlspecialchars($buyer['address_line1'] ?? '') . ', ' . htmlspecialchars($buyer['city'] ?? '') . ', ' . htmlspecialchars($buyer['state'] ?? '') . ' - ' . htmlspecialchars($buyer['pincode'] ?? '') . '</div>
                <div>Contact: ' . htmlspecialchars($order['contact_person']) . ' (' . htmlspecialchars($order['mobile']) . ')</div>
                <div>GSTIN: <strong>' . htmlspecialchars($invoice['gstin'] ?: 'Unregistered B2B') . '</strong></div>
            </td>
            <td class="address-box" style="text-align: right;">
                <div class="section-label">Invoice & Order Details:</div>
                <div><strong>Invoice No:</strong> #' . htmlspecialchars($invoice['invoice_number']) . '</div>
                <div><strong>Order No:</strong> #' . htmlspecialchars($order['order_number']) . '</div>
                <div><strong>Invoice Date:</strong> ' . date('d M Y', strtotime($invoice['invoice_date'])) . '</div>
                <div><strong>Place of Supply:</strong> ' . htmlspecialchars($invoice['place_of_supply']) . '</div>
                <div><strong>Payment Method:</strong> ' . htmlspecialchars(strtoupper(str_replace('_', ' ', $order['payment_method']))) . '</div>
                <div><strong>Payment Status:</strong> ' . htmlspecialchars(strtoupper($order['payment_status'])) . '</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item Description</th>
                <th style="text-align: center; width: 60px;">Qty</th>
                <th style="text-align: right; width: 90px;">Unit Price</th>
                <th style="text-align: right; width: 100px;">Taxable Value</th>
            </tr>
        </thead>
        <tbody>
            ' . $itemsHtml . '
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td style="padding: 3px 0;">Taxable Amount:</td>
            <td style="text-align: right; font-weight: bold; padding: 3px 0;">₹' . number_format((float)$invoice['taxable_amount'], 2) . '</td>
        </tr>
        ' . $taxHtml . '
        ' . $shippingHtml . '
        <tr>
            <td class="total-row" style="padding-top: 6px;">Grand Total:</td>
            <td class="total-row" style="text-align: right; padding-top: 6px;">₹' . number_format((float)$invoice['total'], 2) . '</td>
        </tr>
    </table>

    <div class="footer">
        This is an official computer-generated B2B Tax Invoice issued by MD Printers Commercial Press.
    </div>
</body>
</html>';
    }

    /**
     * Generate raw binary PDF content using Dompdf
     */
    public static function generatePdfStream(array $order): ?string {
        $invoice = self::generateForOrder($order);
        if (!$invoice) return null;

        $html = self::renderHtmlForPdf($order, $invoice);

        if (class_exists('Dompdf\Dompdf')) {
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Helvetica');

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            return $dompdf->output();
        }

        return null;
    }

    /**
     * Output PDF directly to browser with application/pdf header
     */
    public static function streamPdf(array $order, bool $download = true): void {
        $invoice = self::generateForOrder($order);
        if (!$invoice) {
            die("Error retrieving invoice data.");
        }

        $pdfBinary = self::generatePdfStream($order);

        if (!$pdfBinary) {
            die("PDF rendering engine unavailable.");
        }

        if (ob_get_length()) {
            ob_clean();
        }

        $filename = $invoice['invoice_number'] . '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Length: ' . strlen($pdfBinary));
        header('Content-Disposition: ' . ($download ? 'attachment' : 'inline') . '; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        echo $pdfBinary;
        exit;
    }
}
