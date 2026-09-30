<?php
/**
 * MD Printers - B2B Customer & Member Management Model
 */
require_once CONFIG_PATH . '/database.php';

class Customer {

    /**
     * Self-Healing Database Table Initialization
     */
    public static function initTables(): void {
        $db = Database::getInstance();
        if (!$db) return;

        try {
            // 1. Customers Table
            $db->exec("CREATE TABLE IF NOT EXISTS `customers` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `company_name` VARCHAR(150) NOT NULL,
                `contact_person` VARCHAR(120) NOT NULL,
                `mobile` VARCHAR(30) NOT NULL UNIQUE,
                `whatsapp` VARCHAR(30) NULL,
                `email` VARCHAR(150) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `gst_number` VARCHAR(30) NULL,
                `business_type` VARCHAR(100) NULL,
                `address` TEXT NULL,
                `city` VARCHAR(100) NULL,
                `state` VARCHAR(100) NULL,
                `pincode` VARCHAR(20) NULL,
                `website` VARCHAR(255) NULL,
                `interested_products` TEXT NULL,
                `notes` TEXT NULL,
                `customer_type` ENUM('Retail', 'Business', 'Printer', 'Dealer', 'Distributor', 'Premium Member') DEFAULT 'Business',
                `account_status` ENUM('pending', 'approved', 'rejected', 'suspended', 'blocked') DEFAULT 'pending',
                `credit_limit` DECIMAL(12,2) DEFAULT 0.00,
                `used_credit` DECIMAL(12,2) DEFAULT 0.00,
                `admin_notes` TEXT NULL,
                `last_login` DATETIME NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (`account_status`),
                INDEX (`customer_type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // 2. Customer Addresses Table
            $db->exec("CREATE TABLE IF NOT EXISTS `customer_addresses` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `customer_id` INT UNSIGNED NOT NULL,
                `address_type` ENUM('billing', 'shipping', 'office', 'warehouse') DEFAULT 'shipping',
                `title` VARCHAR(100) DEFAULT 'Main Office',
                `company_name` VARCHAR(150) NULL,
                `contact_person` VARCHAR(120) NULL,
                `phone` VARCHAR(30) NULL,
                `address_line1` TEXT NOT NULL,
                `address_line2` TEXT NULL,
                `city` VARCHAR(100) NOT NULL,
                `state` VARCHAR(100) NOT NULL,
                `pincode` VARCHAR(20) NOT NULL,
                `is_default` TINYINT(1) DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // 3. Password Resets Table
            $db->exec("CREATE TABLE IF NOT EXISTS `password_resets` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `email` VARCHAR(150) NOT NULL,
                `token` VARCHAR(100) NOT NULL UNIQUE,
                `expires_at` DATETIME NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (`email`),
                INDEX (`token`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // Ensure all columns exist on customers table for backward compatibility
            $colsToEnsure = [
                'whatsapp'           => "VARCHAR(30) NULL",
                'gst_number'         => "VARCHAR(30) NULL",
                'business_type'      => "VARCHAR(100) NULL",
                'address'            => "TEXT NULL",
                'city'               => "VARCHAR(100) NULL",
                'state'              => "VARCHAR(100) NULL",
                'pincode'            => "VARCHAR(20) NULL",
                'website'            => "VARCHAR(255) NULL",
                'interested_products'=> "TEXT NULL",
                'notes'              => "TEXT NULL",
                'customer_type'      => "VARCHAR(50) DEFAULT 'Business'",
                'account_status'     => "VARCHAR(50) DEFAULT 'pending'",
                'credit_limit'       => "DECIMAL(12,2) DEFAULT 0.00",
                'used_credit'        => "DECIMAL(12,2) DEFAULT 0.00",
                'wallet_balance'     => "DECIMAL(12,2) DEFAULT 0.00",
                'admin_notes'        => "TEXT NULL",
                'last_login'         => "DATETIME NULL"
            ];

            foreach ($colsToEnsure as $col => $def) {
                try {
                    $db->exec("ALTER TABLE `customers` ADD COLUMN `{$col}` {$def}");
                } catch (\Throwable $ex) {}
            }

        } catch (\Throwable $e) {
            error_log("Customer Table Init Error: " . $e->getMessage());
        }
    }

    /**
     * Register New B2B Customer (Account created with status = 'pending')
     */
    public static function register(array $data): array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return ['success' => false, 'message' => 'Database connection failed.'];

        $email = strtolower(trim($data['email'] ?? ''));
        $mobile = trim($data['mobile'] ?? '');

        if (empty($email) || empty($mobile) || empty($data['company_name']) || empty($data['contact_person']) || empty($data['password'])) {
            return ['success' => false, 'message' => 'Please fill in all mandatory fields (*).'];
        }

        // Check if email or mobile already exists
        $checkStmt = $db->prepare("SELECT id, email, mobile FROM customers WHERE email = :email OR mobile = :mobile LIMIT 1");
        $checkStmt->execute(['email' => $email, 'mobile' => $mobile]);
        $existing = $checkStmt->fetch();

        if ($existing) {
            if (strtolower($existing['email']) === $email) {
                return ['success' => false, 'message' => 'A customer account with this email address already exists.'];
            }
            if ($existing['mobile'] === $mobile) {
                return ['success' => false, 'message' => 'A customer account with this mobile number already exists.'];
            }
        }

        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
        $interestedProds = is_array($data['interested_products'] ?? null) ? implode(', ', $data['interested_products']) : ($data['interested_products'] ?? '');

        try {
            $stmt = $db->prepare("INSERT INTO customers 
                (company_name, contact_person, mobile, whatsapp, email, password, gst_number, business_type, address, city, state, pincode, website, interested_products, notes, account_status)
                VALUES 
                (:company_name, :contact_person, :mobile, :whatsapp, :email, :password, :gst_number, :business_type, :address, :city, :state, :pincode, :website, :interested_products, :notes, 'pending')");

            $stmt->execute([
                'company_name'       => trim($data['company_name']),
                'contact_person'     => trim($data['contact_person']),
                'mobile'             => $mobile,
                'whatsapp'           => !empty($data['whatsapp']) ? trim($data['whatsapp']) : $mobile,
                'email'              => $email,
                'password'           => $passwordHash,
                'gst_number'         => !empty($data['gst_number']) ? strtoupper(trim($data['gst_number'])) : null,
                'business_type'      => $data['business_type'] ?? 'Print Reseller / Business',
                'address'            => $data['address'] ?? null,
                'city'               => $data['city'] ?? null,
                'state'              => $data['state'] ?? null,
                'pincode'            => $data['pincode'] ?? null,
                'website'            => $data['website'] ?? null,
                'interested_products'=> $interestedProds,
                'notes'              => $data['notes'] ?? null
            ]);

            $customerId = (int)$db->lastInsertId();

            // Save default shipping address if provided
            if ($customerId && !empty($data['address']) && !empty($data['city']) && !empty($data['state']) && !empty($data['pincode'])) {
                self::saveAddress([
                    'customer_id'    => $customerId,
                    'address_type'   => 'shipping',
                    'title'          => 'Main Business Location',
                    'company_name'   => trim($data['company_name']),
                    'contact_person' => trim($data['contact_person']),
                    'phone'          => $mobile,
                    'address_line1'  => trim($data['address']),
                    'city'           => trim($data['city']),
                    'state'          => trim($data['state']),
                    'pincode'        => trim($data['pincode']),
                    'is_default'     => 1
                ]);
            }

            return [
                'success' => true,
                'message' => 'Your B2B customer account has been registered successfully! Your account is currently PENDING approval from MD Printers administration.',
                'customer_id' => $customerId
            ];
        } catch (\Throwable $e) {
            error_log("Customer Registration Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed due to a database error. Please try again.'];
        }
    }

    /**
     * Login Customer (Verifies credentials & checks account_status)
     */
    public static function login(string $emailOrMobile, string $password): array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return ['success' => false, 'message' => 'Database connection unavailable.'];

        $input = trim($emailOrMobile);
        if (empty($input) || empty($password)) {
            return ['success' => false, 'message' => 'Please enter your email/mobile and password.'];
        }

        try {
            $stmt = $db->prepare("SELECT * FROM customers WHERE email = :email OR mobile = :mobile LIMIT 1");
            $stmt->execute(['email' => $input, 'mobile' => $input]);
            $customer = $stmt->fetch();

            if (!$customer || !password_verify($password, $customer['password'])) {
                return ['success' => false, 'message' => 'Invalid login credentials. Please check your email/mobile and password.'];
            }

            // Check Account Status
            if ($customer['account_status'] === 'pending') {
                return [
                    'success' => false,
                    'status_code' => 'pending',
                    'message' => 'Your account registration is currently PENDING review by MD Printers administration. You will be notified once approved.'
                ];
            }

            if ($customer['account_status'] === 'rejected') {
                return [
                    'success' => false,
                    'status_code' => 'rejected',
                    'message' => 'Your account registration was not approved. Please contact MD Printers hotline support.'
                ];
            }

            if ($customer['account_status'] === 'suspended' || $customer['account_status'] === 'blocked') {
                return [
                    'success' => false,
                    'status_code' => $customer['account_status'],
                    'message' => 'Your account has been ' . strtoupper($customer['account_status']) . '. Please contact support.'
                ];
            }

            // Update last login timestamp safely
            try {
                $upd = $db->prepare("UPDATE customers SET last_login = NOW() WHERE id = :id");
                $upd->execute(['id' => $customer['id']]);
            } catch (\Throwable $ex) {}

            return [
                'success' => true,
                'customer' => $customer
            ];
        } catch (\Throwable $e) {
            error_log("Customer Login Exception: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
        }
    }

    /**
     * Get Customer By ID
     */
    public static function getById(int $id): ?array {
        $db = Database::getInstance();
        if ($db && !$db->inTransaction()) {
            self::initTables();
        }
        if (!$db) return null;

        try {
            $stmt = $db->prepare("SELECT * FROM customers WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch() ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function findById(int $id): ?array {
        return self::getById($id);
    }

    /**
     * Get Customer By Email
     */
    public static function getByEmail(string $email): ?array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return null;

        try {
            $stmt = $db->prepare("SELECT * FROM customers WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => strtolower(trim($email))]);
            return $stmt->fetch() ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Fetch List of Customers for Admin Panel
     */
    public static function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return [];

        try {
            $sql = "SELECT * FROM customers WHERE 1=1";
            $params = [];

            if (!empty($filters['status'])) {
                $sql .= " AND account_status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['customer_type'])) {
                $sql .= " AND customer_type = :type";
                $params['type'] = $filters['customer_type'];
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (company_name LIKE :search OR contact_person LIKE :search OR email LIKE :search OR mobile LIKE :search OR gst_number LIKE :search)";
                $params['search'] = '%' . trim($filters['search']) . '%';
            }

            $sql .= " ORDER BY id DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Count Total Customers for Admin Pagination & Badges
     */
    public static function countAll(array $filters = []): int {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return 0;

        try {
            $sql = "SELECT COUNT(*) FROM customers WHERE 1=1";
            $params = [];

            if (!empty($filters['status'])) {
                $sql .= " AND account_status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['search'])) {
                $sql .= " AND (company_name LIKE :search OR contact_person LIKE :search OR email LIKE :search OR mobile LIKE :search)";
                $params['search'] = '%' . trim($filters['search']) . '%';
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Update Account Status (Approve, Reject, Suspend, Activate, Block)
     */
    public static function updateStatus(int $id, string $status, ?string $adminNotes = null, ?string $customerType = null, ?float $creditLimit = null): bool {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        $validStatuses = ['pending', 'approved', 'rejected', 'suspended', 'blocked'];
        if (!in_array($status, $validStatuses)) return false;

        try {
            $fields = ["account_status = :status"];
            $params = ['status' => $status, 'id' => $id];

            if ($adminNotes !== null) {
                $fields[] = "admin_notes = :admin_notes";
                $params['admin_notes'] = $adminNotes;
            }

            if ($customerType !== null) {
                $fields[] = "customer_type = :customer_type";
                $params['customer_type'] = $customerType;
            }

            if ($creditLimit !== null) {
                $fields[] = "credit_limit = :credit_limit";
                $params['credit_limit'] = $creditLimit;
            }

            $sql = "UPDATE customers SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $db->prepare($sql);
            return $stmt->execute($params);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Update Customer Profile Information
     */
    public static function updateProfile(int $id, array $data): bool {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("UPDATE customers SET 
                company_name = :company_name,
                contact_person = :contact_person,
                whatsapp = :whatsapp,
                gst_number = :gst_number,
                business_type = :business_type,
                address = :address,
                city = :city,
                state = :state,
                pincode = :pincode,
                website = :website
                WHERE id = :id");

            return $stmt->execute([
                'company_name'   => trim($data['company_name']),
                'contact_person' => trim($data['contact_person']),
                'whatsapp'       => !empty($data['whatsapp']) ? trim($data['whatsapp']) : null,
                'gst_number'     => !empty($data['gst_number']) ? strtoupper(trim($data['gst_number'])) : null,
                'business_type'  => $data['business_type'] ?? null,
                'address'        => $data['address'] ?? null,
                'city'           => $data['city'] ?? null,
                'state'          => $data['state'] ?? null,
                'pincode'        => $data['pincode'] ?? null,
                'website'        => $data['website'] ?? null,
                'id'             => $id
            ]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Change Customer Password
     */
    public static function updatePassword(int $id, string $newPassword): bool {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $db->prepare("UPDATE customers SET password = :hash WHERE id = :id");
            return $stmt->execute(['hash' => $hash, 'id' => $id]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Address Book: Fetch Customer Addresses
     */
    public static function getAddresses(int $customerId): array {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return [];

        try {
            $stmt = $db->prepare("SELECT * FROM customer_addresses WHERE customer_id = :cid ORDER BY is_default DESC, id DESC");
            $stmt->execute(['cid' => $customerId]);
            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Address Book: Save or Update Address
     */
    public static function saveAddress(array $data): bool {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        $customerId = (int)($data['customer_id'] ?? 0);
        if (!$customerId) return false;

        try {
            if (!empty($data['is_default'])) {
                $reset = $db->prepare("UPDATE customer_addresses SET is_default = 0 WHERE customer_id = :cid AND address_type = :type");
                $reset->execute(['cid' => $customerId, 'type' => $data['address_type'] ?? 'shipping']);
            }

            if (!empty($data['id'])) {
                $stmt = $db->prepare("UPDATE customer_addresses SET 
                    address_type = :address_type, title = :title, company_name = :company_name,
                    contact_person = :contact_person, phone = :phone, address_line1 = :address_line1,
                    address_line2 = :address_line2, city = :city, state = :state, pincode = :pincode, is_default = :is_default
                    WHERE id = :id AND customer_id = :cid");

                return $stmt->execute([
                    'address_type'   => $data['address_type'] ?? 'shipping',
                    'title'          => $data['title'] ?? 'Office Address',
                    'company_name'   => $data['company_name'] ?? null,
                    'contact_person' => $data['contact_person'] ?? null,
                    'phone'          => $data['phone'] ?? null,
                    'address_line1'  => trim($data['address_line1']),
                    'address_line2'  => $data['address_line2'] ?? null,
                    'city'           => trim($data['city']),
                    'state'          => trim($data['state']),
                    'pincode'        => trim($data['pincode']),
                    'is_default'     => !empty($data['is_default']) ? 1 : 0,
                    'id'             => (int)$data['id'],
                    'cid'            => $customerId
                ]);
            } else {
                $stmt = $db->prepare("INSERT INTO customer_addresses 
                    (customer_id, address_type, title, company_name, contact_person, phone, address_line1, address_line2, city, state, pincode, is_default)
                    VALUES (:cid, :address_type, :title, :company_name, :contact_person, :phone, :address_line1, :address_line2, :city, :state, :pincode, :is_default)");

                return $stmt->execute([
                    'cid'            => $customerId,
                    'address_type'   => $data['address_type'] ?? 'shipping',
                    'title'          => $data['title'] ?? 'Office Address',
                    'company_name'   => $data['company_name'] ?? null,
                    'contact_person' => $data['contact_person'] ?? null,
                    'phone'          => $data['phone'] ?? null,
                    'address_line1'  => trim($data['address_line1']),
                    'address_line2'  => $data['address_line2'] ?? null,
                    'city'           => trim($data['city']),
                    'state'          => trim($data['state']),
                    'pincode'        => trim($data['pincode']),
                    'is_default'     => !empty($data['is_default']) ? 1 : 0
                ]);
            }
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Address Book: Delete Address
     */
    public static function deleteAddress(int $addressId, int $customerId): bool {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("DELETE FROM customer_addresses WHERE id = :id AND customer_id = :cid");
            return $stmt->execute(['id' => $addressId, 'cid' => $customerId]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Create Password Reset Token (1 hour validity)
     */
    public static function createPasswordResetToken(string $email): string|false {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        $email = strtolower(trim($email));
        $customer = self::getByEmail($email);
        if (!$customer) return false;

        try {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour

            $db->prepare("DELETE FROM password_resets WHERE email = :email")->execute(['email' => $email]);
            $stmt = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)");
            $stmt->execute(['email' => $email, 'token' => $token, 'expires_at' => $expiresAt]);

            return $token;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Verify Password Reset Token
     */
    public static function verifyResetToken(string $token): string|false {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("SELECT email FROM password_resets WHERE token = :token AND expires_at > NOW() LIMIT 1");
            $stmt->execute(['token' => trim($token)]);
            return $stmt->fetchColumn() ?: false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Reset Password using Valid Token
     */
    public static function resetPasswordWithToken(string $token, string $newPassword): bool {
        self::initTables();
        $db = Database::getInstance();
        if (!$db) return false;

        $email = self::verifyResetToken($token);
        if (!$email) return false;

        $customer = self::getByEmail($email);
        if (!$customer) return false;

        if (self::updatePassword((int)$customer['id'], $newPassword)) {
            $db->prepare("DELETE FROM password_resets WHERE email = :email")->execute(['email' => $email]);
            return true;
        }

        return false;
    }
}
