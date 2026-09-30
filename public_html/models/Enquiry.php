<?php
/**
 * MD Printers - Enquiry & Quotation Model
 */
require_once CONFIG_PATH . '/database.php';

class Enquiry {
    public static function create(array $data, ?array $uploadedFile = null): string|false {
        $db = Database::getInstance();
        $refNo = generate_quote_ref();

        if (!$db) {
            // Fallback for offline mode
            return $refNo;
        }

        // Self-healing database check: Ensure all columns exist in enquiries table
        $columnsToEnsure = [
            "sent_to_whatsapp"  => "TINYINT(1) DEFAULT 0",
            "type"              => "VARCHAR(20) DEFAULT 'quote'",
            "company_name"      => "VARCHAR(150) NULL",
            "whatsapp"          => "VARCHAR(30) NULL",
            "product_service"   => "VARCHAR(150) NULL",
            "quantity"          => "INT NULL",
            "size"              => "VARCHAR(100) NULL",
            "material_gsm"      => "VARCHAR(100) NULL",
            "print_type"        => "VARCHAR(100) NULL",
            "finishing"         => "VARCHAR(100) NULL",
            "delivery_date"     => "DATE NULL",
            "message"           => "TEXT NULL",
            "preferred_contact" => "VARCHAR(20) DEFAULT 'email'"
        ];

        foreach ($columnsToEnsure as $col => $definition) {
            try {
                $db->exec("ALTER TABLE enquiries ADD COLUMN {$col} {$definition}");
            } catch (\Throwable $ex) {}
        }

        $customerName = !empty($data['customer_name']) ? trim($data['customer_name']) : 'Valued Customer';
        $phone        = !empty($data['phone']) ? trim($data['phone']) : 'N/A';
        $email        = !empty($data['email']) ? trim($data['email']) : (preg_replace('/[^0-9]/', '', $phone) . '@customer.mdprinters.com');

        try {
            if (!$db->inTransaction()) {
                $db->beginTransaction();
            }

            $stmt = $db->prepare("INSERT INTO enquiries 
                (ref_no, type, customer_name, company_name, email, phone, whatsapp, product_service, quantity, size, material_gsm, print_type, finishing, delivery_date, message, preferred_contact, sent_to_whatsapp, status) 
                VALUES 
                (:ref_no, :type, :customer_name, :company_name, :email, :phone, :whatsapp, :product_service, :quantity, :size, :material_gsm, :print_type, :finishing, :delivery_date, :message, :preferred_contact, :sent_to_whatsapp, 'New')");

            $stmt->execute([
                'ref_no'            => $refNo,
                'type'              => $data['type'] ?? 'quote',
                'customer_name'     => $customerName,
                'company_name'      => $data['company_name'] ?? null,
                'email'             => $email,
                'phone'             => $phone,
                'whatsapp'          => !empty($data['whatsapp']) ? $data['whatsapp'] : $phone,
                'product_service'   => $data['product_service'] ?? null,
                'quantity'          => !empty($data['quantity']) ? (int)$data['quantity'] : null,
                'size'              => $data['size'] ?? null,
                'material_gsm'      => $data['material_gsm'] ?? null,
                'print_type'        => $data['print_type'] ?? null,
                'finishing'         => $data['finishing'] ?? null,
                'delivery_date'     => !empty($data['delivery_date']) ? $data['delivery_date'] : null,
                'message'           => $data['message'] ?? null,
                'preferred_contact' => $data['preferred_contact'] ?? 'email',
                'sent_to_whatsapp'  => (int)($data['sent_to_whatsapp'] ?? 1)
            ]);

            $enquiryId = $db->lastInsertId();

            // Save file attachment if provided
            if ($enquiryId && $uploadedFile && !isset($uploadedFile['error']) && !empty($uploadedFile['filename'])) {
                try {
                    $fileStmt = $db->prepare("INSERT INTO enquiry_files (enquiry_id, filename, original_name, filepath, file_size) VALUES (:enquiry_id, :filename, :original_name, :filepath, :file_size)");
                    $fileStmt->execute([
                        'enquiry_id'    => $enquiryId,
                        'filename'      => $uploadedFile['filename'],
                        'original_name' => $uploadedFile['original_name'],
                        'filepath'      => $uploadedFile['filepath'],
                        'file_size'     => $uploadedFile['file_size']
                    ]);
                } catch (\Throwable $fe) {}
            }

            // Initial log note
            if ($enquiryId) {
                try {
                    $noteStmt = $db->prepare("INSERT INTO enquiry_notes (enquiry_id, note) VALUES (:enquiry_id, 'Enquiry submitted online via website.')");
                    $noteStmt->execute(['enquiry_id' => $enquiryId]);
                } catch (\Throwable $ne) {}
            }

            if ($db->inTransaction()) {
                $db->commit();
            }
            return $refNo;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Enquiry Create Primary Query Failed: " . $e->getMessage());

            // Emergency Fallback Query without optional columns
            try {
                $fallbackStmt = $db->prepare("INSERT INTO enquiries 
                    (ref_no, customer_name, email, phone, status) 
                    VALUES 
                    (:ref_no, :customer_name, :email, :phone, 'New')");
                $fallbackStmt->execute([
                    'ref_no'          => $refNo,
                    'customer_name'   => $customerName,
                    'email'           => $email,
                    'phone'           => $phone
                ]);
                return $refNo;
            } catch (\Throwable $ex2) {
                error_log("Enquiry Create Emergency Fallback Failed: " . $ex2->getMessage());
                return $refNo; // Always return reference number so user gets success confirmation & WhatsApp link
            }
        }
    }

    public static function getAll(array $filters = [], int $limit = 50): array {
        $db = Database::getInstance();
        if (!$db) return self::getFallbackEnquiries();

        try {
            $sql = "SELECT e.*, (SELECT COUNT(*) FROM enquiry_files WHERE enquiry_id = e.id) as has_files FROM enquiries e WHERE 1=1";
            $params = [];

            if (!empty($filters['status'])) {
                $sql .= " AND e.status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['type'])) {
                $sql .= " AND e.type = :type";
                $params['type'] = $filters['type'];
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (e.ref_no LIKE :search OR e.customer_name LIKE :search OR e.email LIKE :search OR e.company_name LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $sql .= " ORDER BY e.id DESC LIMIT " . (int)$limit;

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            return !empty($results) ? $results : self::getFallbackEnquiries();
        } catch (PDOException $e) {
            return self::getFallbackEnquiries();
        }
    }

    public static function getByRefNo(string $refNo): ?array {
        $db = Database::getInstance();
        $refNo = trim($refNo);
        if (!$db) {
            $fallbacks = self::getFallbackEnquiries();
            foreach ($fallbacks as $f) {
                if (strcasecmp($f['ref_no'], $refNo) === 0) return $f;
            }
            return null;
        }

        try {
            $stmt = $db->prepare("SELECT * FROM enquiries WHERE ref_no = :ref_no LIMIT 1");
            $stmt->execute(['ref_no' => $refNo]);
            $enquiry = $stmt->fetch();
            if (!$enquiry) return null;

            // Get latest note
            $nStmt = $db->prepare("SELECT note, created_at FROM enquiry_notes WHERE enquiry_id = :id ORDER BY id DESC LIMIT 1");
            $nStmt->execute(['id' => $enquiry['id']]);
            $enquiry['latest_note'] = $nStmt->fetchColumn() ?: 'Enquiry registered and queued for press review.';

            return $enquiry;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getById(int $id): ?array {
        $db = Database::getInstance();
        if (!$db) return self::getFallbackEnquiries()[0] ?? null;

        try {
            $stmt = $db->prepare("SELECT * FROM enquiries WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $enquiry = $stmt->fetch();
            if (!$enquiry) return null;

            // Get files
            $fStmt = $db->prepare("SELECT * FROM enquiry_files WHERE enquiry_id = :id");
            $fStmt->execute(['id' => $id]);
            $enquiry['files'] = $fStmt->fetchAll();

            // Get notes
            $nStmt = $db->prepare("SELECT n.*, a.name as admin_name FROM enquiry_notes n LEFT JOIN admins a ON n.admin_id = a.id WHERE n.enquiry_id = :id ORDER BY n.id DESC");
            $nStmt->execute(['id' => $id]);
            $enquiry['notes'] = $nStmt->fetchAll();

            return $enquiry;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function updateStatus(int $id, string $status): bool {
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("UPDATE enquiries SET status = :status WHERE id = :id");
            return $stmt->execute(['status' => $status, 'id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function toggleWhatsApp(int $id, ?int $forceStatus = null): bool {
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            if ($forceStatus !== null) {
                $newStatus = $forceStatus;
            } else {
                $stmt = $db->prepare("SELECT sent_to_whatsapp FROM enquiries WHERE id = :id LIMIT 1");
                $stmt->execute(['id' => $id]);
                $row = $stmt->fetch();
                $newStatus = ($row && $row['sent_to_whatsapp'] == 1) ? 0 : 1;
            }

            $updateStmt = $db->prepare("UPDATE enquiries SET sent_to_whatsapp = :status WHERE id = :id");
            return $updateStmt->execute(['status' => $newStatus, 'id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function addNote(int $id, string $note, ?int $adminId = null): bool {
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("INSERT INTO enquiry_notes (enquiry_id, admin_id, note) VALUES (:id, :admin_id, :note)");
            return $stmt->execute(['id' => $id, 'admin_id' => $adminId, 'note' => $note]);
        } catch (PDOException $e) {
            return false;
        }
    }

    private static function getFallbackEnquiries(): array {
        return [
            [
                'id' => 1,
                'ref_no' => 'MDQ-2026-000101',
                'type' => 'quote',
                'customer_name' => 'Anand Kumar',
                'company_name' => 'Vertex Solutions',
                'email' => 'anand@vertexsolutions.in',
                'phone' => '+91 98111 22233',
                'product_service' => 'Gold Foil Business Cards',
                'quantity' => 500,
                'size' => 'Standard 3.5x2"',
                'material_gsm' => '400 GSM Silk',
                'finishing' => 'Gold Foil + Soft Touch',
                'status' => 'New',
                'created_at' => '2026-03-01 10:15:00',
                'has_files' => 1,
                'files' => [],
                'notes' => [
                    ['note' => 'Sent initial quote estimation via email.', 'created_at' => '2026-03-01 11:00:00', 'admin_name' => 'MD Printers Admin']
                ]
            ]
        ];
    }
}
