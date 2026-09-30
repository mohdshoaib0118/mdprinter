<?php
require_once __DIR__ . '/../public_html/config/config.php';
require_once __DIR__ . '/../public_html/config/database.php';

$db = Database::getInstance();
if (!$db) {
    echo "DB Connection failed\n";
    exit;
}

$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "TABLES:\n";
print_r($tables);

foreach ($tables as $table) {
    echo "\n--- COLUMNS FOR $table ---\n";
    $cols = $db->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo "{$col['Field']} ({$col['Type']}) NULL={$col['Null']} DEFAULT={$col['Default']}\n";
    }
}
