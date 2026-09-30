<?php
/**
 * MD Printers Admin - Pricing Rules & Option Modifiers Management
 */
require_once MODELS_PATH . '/Product.php';
require_once MODELS_PATH . '/PricingEngine.php';

PricingEngine::initTables();
$db = Database::getInstance();

$products = Product::getAll(['status' => 'published']);
$selectedProductId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : ($products[0]['id'] ?? 0);

// Handle Admin POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'CSRF verification failed.');
        redirect_to(ADMIN_URL . '?view=pricing&product_id=' . $selectedProductId);
    }

    $act = $_POST['action'] ?? '';

    if ($act === 'add_slab') {
        $qty = (int)$_POST['quantity'];
        $base = (float)$_POST['base_price'];
        $regDisc = (float)($_POST['regular_discount_pct'] ?? 0);
        $silvDisc = (float)($_POST['silver_discount_pct'] ?? 5);
        $goldDisc = (float)($_POST['gold_discount_pct'] ?? 10);
        $platDisc = (float)($_POST['platinum_discount_pct'] ?? 15);

        try {
            $stmt = $db->prepare("INSERT INTO product_pricing_slabs 
                (product_id, quantity, base_price, regular_discount_pct, silver_discount_pct, gold_discount_pct, platinum_discount_pct)
                VALUES (:pid, :qty, :base, :reg, :silv, :gold, :plat)
                ON DUPLICATE KEY UPDATE base_price = :base, regular_discount_pct = :reg, silver_discount_pct = :silv, gold_discount_pct = :gold, platinum_discount_pct = :plat");
            $stmt->execute([
                'pid' => $selectedProductId,
                'qty' => $qty,
                'base' => $base,
                'reg' => $regDisc,
                'silv' => $silvDisc,
                'gold' => $goldDisc,
                'plat' => $platDisc
            ]);
            set_flash('success', 'Quantity slab saved successfully!');
        } catch (PDOException $e) {
            set_flash('danger', 'Error saving quantity slab.');
        }
    } elseif ($act === 'delete_slab') {
        $slabId = (int)$_POST['slab_id'];
        $stmt = $db->prepare("DELETE FROM product_pricing_slabs WHERE id = :id AND product_id = :pid");
        $stmt->execute(['id' => $slabId, 'pid' => $selectedProductId]);
        set_flash('success', 'Quantity slab deleted.');
    } elseif ($act === 'add_option') {
        $optName = trim($_POST['option_name']);
        if (!empty($optName)) {
            $stmt = $db->prepare("INSERT INTO product_options (product_id, option_name) VALUES (:pid, :name)");
            $stmt->execute(['pid' => $selectedProductId, 'name' => $optName]);
            set_flash('success', 'Option group "' . e($optName) . '" created!');
        }
    } elseif ($act === 'add_option_value') {
        $optId = (int)$_POST['option_id'];
        $valName = trim($_POST['value_name']);
        $modType = $_POST['modifier_type'];
        $modPrice = (float)$_POST['price_modifier'];

        if (!empty($valName)) {
            $stmt = $db->prepare("INSERT INTO product_option_values (option_id, value_name, modifier_type, price_modifier) VALUES (:oid, :name, :mtype, :mprice)");
            $stmt->execute(['oid' => $optId, 'name' => $valName, 'mtype' => $modType, 'mprice' => $modPrice]);
            set_flash('success', 'Option value added!');
        }
    } elseif ($act === 'delete_option_value') {
        $valId = (int)$_POST['value_id'];
        $stmt = $db->prepare("DELETE FROM product_option_values WHERE id = :id");
        $stmt->execute(['id' => $valId]);
        set_flash('success', 'Option value removed.');
    }

    redirect_to(ADMIN_URL . '?view=pricing&product_id=' . $selectedProductId);
}

$slabs = PricingEngine::getQuantitySlabs($selectedProductId);
$options = PricingEngine::getProductOptions($selectedProductId);
?>

<div class="admin-content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 900; color: #0f172a; margin: 0;">B2B Pricing Rules & Options Engine</h1>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Configure quantity slabs, option price modifiers (fixed, per-unit, percentage), and customer tier discounts per product.</p>
    </div>
</div>

<?php display_flash(); ?>

<!-- Product Selection Bar -->
<div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 24px;">
    <form action="<?= ADMIN_URL ?>" method="GET" style="display: flex; align-items: center; gap: 16px;">
        <input type="hidden" name="view" value="pricing">
        <label style="font-weight: 800; color: #0f172a; margin: 0;">Select Catalogue Product:</label>
        <select name="product_id" class="form-control" style="width: auto; flex-grow: 1; font-weight: 700;" onchange="this.form.submit()">
            <?php foreach ($products as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $p['id'] == $selectedProductId ? 'selected' : '' ?>>
                    <?= e($p['name']) ?> (ID: #<?= $p['id'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">

    <!-- Left Column: Quantity Slabs & Tier Pricing -->
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
        <h3 style="font-size: 1.2rem; font-weight: 900; color: #0f172a; margin-bottom: 16px;">1. Quantity Slabs & Tier Discounts</h3>
        
        <!-- Add Slab Form -->
        <form action="<?= ADMIN_URL ?>?view=pricing&product_id=<?= $selectedProductId ?>" method="POST" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 16px; border-radius: 12px; margin-bottom: 24px;">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
            <input type="hidden" name="action" value="add_slab">
            <h5 style="font-weight: 800; color: #0f172a; margin-bottom: 12px;">Add / Update Quantity Slab</h5>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                <div>
                    <label style="font-size: 0.8rem; font-weight: 700; color: #64748b;">Quantity (e.g., 500)</label>
                    <input type="number" name="quantity" required class="form-control" placeholder="500">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 700; color: #64748b;">Base Price (₹)</label>
                    <input type="number" step="0.01" name="base_price" required class="form-control" placeholder="499.00">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                <div>
                    <label style="font-size: 0.8rem; font-weight: 700; color: #64748b;">Silver Disc (%)</label>
                    <input type="number" step="0.01" name="silver_discount_pct" value="5.00" class="form-control">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 700; color: #64748b;">Gold Disc (%)</label>
                    <input type="number" step="0.01" name="gold_discount_pct" value="10.00" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; font-weight: 800;">
                <i class="fas fa-plus-circle"></i> Save Quantity Slab
            </button>
        </form>

        <!-- Slabs List Table -->
        <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 700;">
                        <th style="padding: 10px 14px;">Qty</th>
                        <th style="padding: 10px 14px;">Base Price</th>
                        <th style="padding: 10px 14px;">Gold Disc</th>
                        <th style="padding: 10px 14px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($slabs)): ?>
                        <tr><td colspan="4" style="padding: 16px; text-align: center; color: #64748b;">No quantity slabs defined.</td></tr>
                    <?php else: ?>
                        <?php foreach ($slabs as $s): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 14px; font-weight: 800; color: #0f172a;"><?= number_format($s['quantity']) ?> units</td>
                                <td style="padding: 12px 14px; font-weight: 800; color: var(--cmyk-cyan);">₹<?= number_format($s['base_price'], 2) ?></td>
                                <td style="padding: 12px 14px; color: #16a34a; font-weight: 700;"><?= $s['gold_discount_pct'] ?>%</td>
                                <td style="padding: 12px 14px; text-align: right;">
                                    <form action="<?= ADMIN_URL ?>?view=pricing&product_id=<?= $selectedProductId ?>" method="POST" style="margin:0;">
                                        <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                                        <input type="hidden" name="action" value="delete_slab">
                                        <input type="hidden" name="slab_id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn btn-sm" style="background: rgba(239,68,68,0.1); color: #ef4444; border: 0; padding: 4px 10px;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Column: Product Option Groups & Modifiers -->
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
        <h3 style="font-size: 1.2rem; font-weight: 900; color: #0f172a; margin-bottom: 16px;">2. Configurable Option Modifiers</h3>

        <!-- Add Option Group Form -->
        <form action="<?= ADMIN_URL ?>?view=pricing&product_id=<?= $selectedProductId ?>" method="POST" style="display: flex; gap: 10px; margin-bottom: 24px;">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
            <input type="hidden" name="action" value="add_option">
            <input type="text" name="option_name" required placeholder="Option Group (e.g. Paper GSM, Finishing)" class="form-control" style="font-size: 0.9rem;">
            <button type="submit" class="btn btn-primary btn-sm" style="white-space: nowrap; font-weight: 800;">
                <i class="fas fa-plus"></i> Create Option Group
            </button>
        </form>

        <!-- Existing Option Groups List -->
        <?php foreach ($options as $opt): ?>
            <div style="border: 1px solid #cbd5e1; border-radius: 12px; padding: 18px; margin-bottom: 20px; background: #ffffff;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 14px;">
                    <h5 style="font-size: 1rem; font-weight: 900; color: #0f172a; margin: 0;">
                        <i class="fas fa-sliders-h" style="color: var(--cmyk-cyan);"></i> <?= e($opt['option_name']) ?>
                    </h5>
                </div>

                <!-- Values List -->
                <ul style="list-style: none; padding: 0; margin: 0 0 16px 0; display: flex; flex-direction: column; gap: 8px;">
                    <?php foreach ($opt['values'] as $v): ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 8px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.88rem;">
                            <div>
                                <strong style="color: #0f172a;"><?= e($v['value_name']) ?></strong>
                                <span style="font-size: 0.78rem; color: #64748b; margin-left: 8px;">
                                    Modifier: <strong><?= e($v['modifier_type']) ?></strong> (<?= $v['modifier_type'] === 'percentage' ? '+' . $v['price_modifier'] . '%' : '+₹' . number_format($v['price_modifier'], 2) ?>)
                                </span>
                            </div>
                            <form action="<?= ADMIN_URL ?>?view=pricing&product_id=<?= $selectedProductId ?>" method="POST" style="margin:0;">
                                <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                                <input type="hidden" name="action" value="delete_option_value">
                                <input type="hidden" name="value_id" value="<?= $v['id'] ?>">
                                <button type="submit" class="btn btn-sm" style="background: transparent; color: #ef4444; border: 0; padding: 2px 6px;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Add Value to Group Form -->
                <form action="<?= ADMIN_URL ?>?view=pricing&product_id=<?= $selectedProductId ?>" method="POST" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 8px; align-items: center;">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                    <input type="hidden" name="action" value="add_option_value">
                    <input type="hidden" name="option_id" value="<?= $opt['id'] ?>">

                    <input type="text" name="value_name" required placeholder="Value (e.g. 350 GSM)" class="form-control" style="font-size: 0.85rem;">
                    <select name="modifier_type" class="form-control" style="font-size: 0.85rem;">
                        <option value="fixed">Fixed (+₹)</option>
                        <option value="per_unit">Per Unit (+₹/unit)</option>
                        <option value="percentage">Percentage (+%)</option>
                    </select>
                    <input type="number" step="0.01" name="price_modifier" value="0.00" class="form-control" style="font-size: 0.85rem;">

                    <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 800;">
                        <i class="fas fa-plus"></i>
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

</div>
