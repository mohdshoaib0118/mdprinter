<?php
/**
 * MD Printers - B2B Order Journey: Complete Dynamic Product Configurator & Order Form
 */
require_once INCLUDES_PATH . '/header.php';
require_once MODELS_PATH . '/Category.php';
require_once MODELS_PATH . '/ProductGroup.php';
require_once MODELS_PATH . '/Product.php';
require_once MODELS_PATH . '/PricingEngine.php';

if (!isset($product) || empty($product)) {
    if (isset($currentProduct) && !empty($currentProduct)) {
        $product = $currentProduct;
    } else {
        $prodSlug = $matchParams['slug'] ?? ($segments[1] ?? '');
        $product = Product::getBySlug($prodSlug);
    }
}

if (!$product) {
    redirect_to(SITE_URL . '/order');
}

$productId = (int)$product['id'];

// Get Category & Product Group
$category = null;
if (!empty($product['category_id'])) {
    $category = Category::getById((int)$product['category_id']);
}

$group = null;
if (!empty($product['product_group_id'])) {
    $group = ProductGroup::getById((int)$product['product_group_id']);
}

// Sibling products in the same group for Product Switcher Dropdown
$siblingProducts = !empty($product['product_group_id']) ? Product::getProductsByGroup((int)$product['product_group_id']) : [];

// Pricing Slabs & Options
$quantitySlabs = PricingEngine::getQuantitySlabs($productId);
$availableOptions = PricingEngine::getProductOptions($productId);

$minQty = (int)($product['min_quantity'] ?? 100);
$maxQty = !empty($product['max_quantity']) ? (int)$product['max_quantity'] : null;
$qtyStep = !empty($product['quantity_step']) ? (int)$product['quantity_step'] : 1;

$initialQty = !empty($quantitySlabs) ? (int)$quantitySlabs[0]['quantity'] : $minQty;
$customerId = is_customer_logged_in() ? (int)$_SESSION['customer_id'] : null;

// Initial server-side price calculation
$initialCalculation = PricingEngine::calculatePrice($productId, $initialQty, [], $customerId);

// Parse specifications JSON
$specifications = [];
if (!empty($product['specifications_json'])) {
    $decodedSpecs = json_decode($product['specifications_json'], true);
    if (is_array($decodedSpecs)) {
        $specifications = $decodedSpecs;
    }
}

// Gallery images
$galleryImages = [];
if (!empty($product['gallery_json'])) {
    $decodedGallery = json_decode($product['gallery_json'], true);
    if (is_array($decodedGallery)) {
        $galleryImages = $decodedGallery;
    }
}
?>

<div style="background: #f8fafc; min-height: 85vh; padding: 36px 0 60px 0;">
    <div class="container">

        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" style="margin-bottom: 24px;">
            <ol style="display: flex; gap: 8px; list-style: none; padding: 0; margin: 0; font-size: 0.88rem; font-weight: 700; color: #64748b; flex-wrap: wrap; align-items: center;">
                <li><a href="<?= SITE_URL ?>/dashboard" style="color: #64748b; text-decoration: none;"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><span style="color: #cbd5e1;">/</span></li>
                <li><a href="<?= SITE_URL ?>/order" style="color: #64748b; text-decoration: none;">New Order</a></li>
                <?php if ($category): ?>
                    <li><span style="color: #cbd5e1;">/</span></li>
                    <li><a href="<?= SITE_URL ?>/order/category/<?= e($category['slug']) ?>" style="color: #64748b; text-decoration: none;"><?= e($category['name']) ?></a></li>
                <?php endif; ?>
                <?php if ($group): ?>
                    <li><span style="color: #cbd5e1;">/</span></li>
                    <li><a href="<?= SITE_URL ?>/order/group/<?= e($group['slug']) ?>" style="color: #64748b; text-decoration: none;"><?= e($group['name']) ?></a></li>
                <?php endif; ?>
                <li><span style="color: #cbd5e1;">/</span></li>
                <li style="color: var(--cmyk-cyan);"><?= e($product['name']) ?></li>
            </ol>
        </nav>

        <!-- Two Column Main Grid -->
        <div style="display: grid; grid-template-columns: 1.25fr 1fr; gap: 36px; align-items: start;">

            <!-- ========================================================= -->
            <!-- LEFT COLUMN: COMPLETE ORDER / CONFIGURATION FORM         -->
            <!-- ========================================================= -->
            <div>
                <div style="background: #ffffff; padding: 36px; border-radius: 24px; border: 1px solid #e2e8f0; box-shadow: 0 15px 40px rgba(0,0,0,0.06);">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 2px solid #f1f5f9; padding-bottom: 18px; flex-wrap: wrap; gap: 12px;">
                        <div>
                            <span style="font-size: 0.78rem; text-transform: uppercase; font-weight: 800; color: var(--cmyk-cyan); letter-spacing: 0.5px; display: block;">
                                Order Configuration Form
                            </span>
                            <h2 style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 4px 0 0 0;">
                                Configure Print Specification
                            </h2>
                        </div>
                        <?php if (is_customer_logged_in()): ?>
                            <span style="background: rgba(0,174,239,0.1); color: var(--cmyk-cyan); border: 1px solid rgba(0,174,239,0.3); font-size: 0.8rem; font-weight: 800; padding: 6px 14px; border-radius: 20px; text-transform: uppercase;">
                                <i class="fas fa-crown"></i> <?= e($_SESSION['customer_tier'] ?? $_SESSION['customer_type'] ?? 'B2B Account') ?> Tier
                            </span>
                        <?php endif; ?>
                    </div>

                    <form id="b2bOrderForm" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf() ?>">
                        <input type="hidden" name="product_id" id="formProductId" value="<?= $productId ?>">

                        <!-- SECTION 1: ORDER NAME / JOB NAME -->
                        <div style="margin-bottom: 24px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px;">
                            <label style="font-weight: 800; color: #0f172a; display: block; margin-bottom: 8px; font-size: 0.95rem;">
                                1. Order / Job Reference Name <span style="color: #ef4444;">*</span>
                            </label>
                            <input type="text" name="order_name" id="order_name_input" class="form-control" required placeholder="Enter Job Name (e.g. Acme Corp Marketing Cards Batch 1)" style="font-weight: 700; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.95rem; background: #ffffff;">
                            <small style="color: #64748b; font-size: 0.8rem; margin-top: 6px; display: block;">
                                Specify customer/job identifier for print queue tracking.
                            </small>
                        </div>

                        <!-- SECTION 2: SELECT PRODUCT (SWITCHER WITHIN PRODUCT GROUP) -->
                        <div style="margin-bottom: 24px;">
                            <label style="font-weight: 800; color: #0f172a; display: block; margin-bottom: 8px; font-size: 0.95rem;">
                                2. Selected Product
                            </label>
                            <?php if (count($siblingProducts) > 1): ?>
                                <select onchange="if(this.value) window.location.href='<?= SITE_URL ?>/order/product/' + this.value;" class="form-control" style="font-weight: 800; border-radius: 10px; border: 2px solid var(--cmyk-cyan); background: rgba(0,174,239,0.03); color: #0f172a; font-size: 0.95rem;">
                                    <?php foreach ($siblingProducts as $sp): ?>
                                        <option value="<?= e($sp['slug']) ?>" <?= (int)$sp['id'] === $productId ? 'selected' : '' ?>>
                                            <?= e($sp['name']) ?> (Min Qty: <?= number_format($sp['min_quantity']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small style="color: #64748b; font-size: 0.78rem; margin-top: 4px; display: block;">
                                    Switch product within group "<?= e($group['name'] ?? 'Current Group') ?>"
                                </small>
                            <?php else: ?>
                                <div style="background: #f1f5f9; padding: 12px 16px; border-radius: 10px; font-weight: 800; color: #0f172a; font-size: 0.95rem; border: 1px solid #cbd5e1;">
                                    <i class="fas fa-box" style="color: var(--cmyk-cyan); margin-right: 8px;"></i> <?= e($product['name']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- SECTION 3: QUANTITY SELECTION -->
                        <div style="margin-bottom: 24px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px;">
                            <label style="font-weight: 800; color: #0f172a; display: block; margin-bottom: 12px; font-size: 0.95rem;">
                                3. Select Print Quantity (Min: <?= number_format($minQty) ?><?= $maxQty ? ', Max: ' . number_format($maxQty) : '' ?>):
                            </label>
                            
                            <?php if (!empty($quantitySlabs)): ?>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 10px; margin-bottom: 14px;">
                                    <?php foreach ($quantitySlabs as $idx => $s): ?>
                                        <label style="cursor: pointer; text-align: center; border: 2px solid <?= $idx === 0 ? 'var(--cmyk-cyan)' : '#e2e8f0' ?>; background: <?= $idx === 0 ? 'rgba(0,174,239,0.06)' : '#ffffff' ?>; border-radius: 12px; padding: 10px 6px; transition: all 0.2s; user-select: none;" class="qty-pill">
                                            <input type="radio" name="quantity" value="<?= $s['quantity'] ?>" <?= $idx === 0 ? 'checked' : '' ?> style="display: none;" onchange="updateB2BPrice()">
                                            <div style="font-weight: 900; font-size: 1rem; color: #0f172a;"><?= number_format($s['quantity']) ?></div>
                                            <div style="font-size: 0.72rem; color: #64748b; font-weight: 700;">units</div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <input type="number" name="quantity" value="<?= $minQty ?>" min="<?= $minQty ?>" <?= $maxQty ? 'max="' . $maxQty . '"' : '' ?> step="<?= $qtyStep ?>" class="form-control" style="font-weight: 800; border-radius: 10px; font-size: 1rem;" onchange="updateB2BPrice()" onkeyup="updateB2BPrice()">
                            <?php endif; ?>
                        </div>

                        <!-- SECTION 4 & 5: DYNAMIC PRODUCT OPTIONS & BOOLEAN CHOICE SLABS -->
                        <?php if (!empty($availableOptions)): ?>
                            <div style="margin-bottom: 24px;">
                                <h4 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-sliders-h" style="color: var(--cmyk-cyan);"></i> Product Specific Detail Options
                                </h4>

                                <?php foreach ($availableOptions as $opt): ?>
                                    <div style="margin-bottom: 18px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <label style="font-weight: 800; color: #0f172a; font-size: 0.92rem; margin: 0;">
                                                <?= e($opt['option_name']) ?>
                                                <?php if (!empty($opt['is_required'])): ?>
                                                    <span style="color: #ef4444;">*</span>
                                                <?php endif; ?>
                                            </label>
                                            <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; background: #e2e8f0; padding: 2px 8px; border-radius: 10px;">
                                                <?= !empty($opt['is_required']) ? 'Required' : 'Optional' ?>
                                            </span>
                                        </div>

                                        <?php if ($opt['option_type'] === 'radio' || strtolower($opt['option_name']) === 'privacy packing' || count($opt['values']) <= 3): ?>
                                            <!-- Render styled Radio Choice Cards -->
                                            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 8px;">
                                                <?php foreach ($opt['values'] as $vIdx => $v): ?>
                                                    <label style="cursor: pointer; border: 2px solid <?= !empty($v['is_default']) ? 'var(--cmyk-cyan)' : '#cbd5e1' ?>; background: <?= !empty($v['is_default']) ? 'rgba(0,174,239,0.06)' : '#ffffff' ?>; border-radius: 10px; padding: 8px 14px; font-size: 0.88rem; font-weight: 700; color: #0f172a; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;" class="opt-radio-pill">
                                                        <input type="radio" name="options[<?= $opt['id'] ?>]" value="<?= $v['id'] ?>" <?= !empty($v['is_default']) ? 'checked' : '' ?> onchange="updateB2BPrice(); highlightRadioPill(this);">
                                                        <span><?= e($v['value_name']) ?></span>
                                                        <?php
                                                            if ($v['price_modifier'] > 0) {
                                                                if ($v['modifier_type'] === 'fixed') echo '<small style="color:#0284c7;">(+₹' . number_format($v['price_modifier'], 2) . ')</small>';
                                                                elseif ($v['modifier_type'] === 'per_unit') echo '<small style="color:#0284c7;">(+₹' . number_format($v['price_modifier'], 2) . '/unit)</small>';
                                                                elseif ($v['modifier_type'] === 'percentage') echo '<small style="color:#0284c7;">(+' . $v['price_modifier'] . '%)</small>';
                                                            }
                                                        ?>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <!-- Render Standard Dropdown Select -->
                                            <select name="options[<?= $opt['id'] ?>]" class="form-control config-option-select" style="font-weight: 700; border-radius: 10px; border: 1px solid #cbd5e1; background: #ffffff;" onchange="updateB2BPrice()">
                                                <?php foreach ($opt['values'] as $v): ?>
                                                    <option value="<?= $v['id'] ?>" <?= !empty($v['is_default']) ? 'selected' : '' ?>>
                                                        <?= e($v['value_name']) ?>
                                                        <?php
                                                            if ($v['price_modifier'] > 0) {
                                                                if ($v['modifier_type'] === 'fixed') echo ' (+₹' . number_format($v['price_modifier'], 2) . ')';
                                                                elseif ($v['modifier_type'] === 'per_unit') echo ' (+₹' . number_format($v['price_modifier'], 2) . '/unit)';
                                                                elseif ($v['modifier_type'] === 'percentage') echo ' (+' . $v['price_modifier'] . '%)';
                                                            }
                                                        ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="margin-bottom: 24px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 14px; padding: 18px; color: #0369a1;">
                                <h5 style="font-weight: 800; margin: 0 0 6px 0; font-size: 0.95rem; color: #0369a1;">
                                    <i class="fas fa-info-circle"></i> Standard Product Specifications
                                </h5>
                                <p style="font-size: 0.88rem; margin: 0; line-height: 1.5; color: #334155;">
                                    This product is supplied with standard press specifications. Specify target quantity, job reference, artwork, and remarks to receive your B2B estimate.
                                </p>
                            </div>
                        <?php endif; ?>

                        <!-- SECTION 6: PRESSLINE PRINTING TEXT (IF CONFIGURED) -->
                        <?php if (!empty($product['allow_pressline'])): ?>
                            <div style="margin-bottom: 24px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px;">
                                <label style="font-weight: 800; color: #0f172a; display: block; margin-bottom: 8px; font-size: 0.92rem;">
                                    <i class="fas fa-pen-nib" style="color: var(--cmyk-cyan);"></i> Pressline / Custom Printing Margin Text (Optional):
                                </label>
                                <input type="text" name="pressline" class="form-control" placeholder="Enter text to print on pressline margin or flap..." style="font-weight: 600; border-radius: 10px; font-size: 0.88rem; background: #ffffff;">
                            </div>
                        <?php endif; ?>

                        <!-- SECTION 7: DELIVERY RULE BADGE -->
                        <div style="margin-bottom: 24px;">
                            <?php 
                                $isFreeDelivery = false;
                                if (!empty($product['free_delivery_min_qty']) && $initialQty >= (int)$product['free_delivery_min_qty']) {
                                    $isFreeDelivery = true;
                                } elseif (!empty($product['free_delivery_min_amount']) && $initialCalculation['net_subtotal'] >= (float)$product['free_delivery_min_amount']) {
                                    $isFreeDelivery = true;
                                }
                            ?>
                            <div id="deliveryRuleContainer" style="background: <?= $isFreeDelivery ? '#f0fdf4' : '#f8fafc' ?>; border: 1px solid <?= $isFreeDelivery ? '#bbf7d0' : '#e2e8f0' ?>; border-radius: 12px; padding: 14px 18px; display: flex; align-items: center; gap: 12px;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: <?= $isFreeDelivery ? '#16a34a' : '#64748b' ?>; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                                    <i class="fas fa-truck-fast"></i>
                                </div>
                                <div>
                                    <strong id="deliveryBadgeText" style="color: <?= $isFreeDelivery ? '#15803d' : '#0f172a' ?>; font-size: 0.92rem; display: block;">
                                        <?= $isFreeDelivery ? 'Eligible for Free Delivery' : 'Standard Shipping Applicable at Checkout' ?>
                                    </strong>
                                    <span style="font-size: 0.78rem; color: #64748b;">Dispatched safely with press tracking identifier</span>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 8: ARTWORK SELECTION & SECURE FILE UPLOAD -->
                        <div style="margin-bottom: 24px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 16px; padding: 20px;">
                            <label style="font-weight: 800; color: #0f172a; display: block; margin-bottom: 12px; font-size: 0.95rem;">
                                <i class="fas fa-cloud-upload-alt" style="color: var(--cmyk-cyan);"></i> Artwork & Production Media:
                            </label>
                            
                            <select name="artwork_option" id="artworkOptionSelect" class="form-control" style="font-weight: 700; border-radius: 10px; margin-bottom: 14px; background: #ffffff; border: 1px solid #cbd5e1;" onchange="toggleArtworkFileInput()">
                                <option value="upload_now">Upload Artwork File Online Now</option>
                                <option value="upload_later">Upload Artwork Later via Customer Dashboard</option>
                                <option value="email">Send Artwork File via Email (print@mdprinters.com)</option>
                            </select>

                            <div id="artworkFileInputContainer" style="background: #ffffff; padding: 14px; border-radius: 10px; border: 1px solid #e2e8f0;">
                                <input type="file" name="artwork_file" class="form-control" style="font-size: 0.88rem; border-radius: 8px;">
                                <small style="color: #64748b; display: block; margin-top: 8px; font-size: 0.78rem;">
                                    Supported extensions: <strong>PDF, CDR, AI, PSD, ZIP, PNG, JPG</strong> (Max size: 15MB). High-resolution vector files recommended.
                                </small>
                            </div>
                        </div>

                        <!-- SECTION 9: SPECIAL REMARK / PRODUCTION INSTRUCTIONS -->
                        <div style="margin-bottom: 24px;">
                            <label style="font-weight: 800; color: #0f172a; display: block; margin-bottom: 8px; font-size: 0.92rem;">
                                Special Remarks / Production Instructions:
                            </label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Specific PMS Pantone shade codes, exact trimming requests, special packaging instructions..." style="border-radius: 10px; font-size: 0.88rem; border: 1px solid #cbd5e1;"></textarea>
                        </div>

                        <!-- SECTION 11 & 12: LIVE PRICE BREAKDOWN PANEL & UNPRICED SAFEGUARD -->
                        <div id="pricingCardWrapper" style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 18px; padding: 24px; margin-bottom: 24px;">
                            
                            <!-- Unpriced Safeguard Banner -->
                            <div id="unpricedWarningBanner" style="display: <?= $initialCalculation['is_priced'] ? 'none' : 'block' ?>; background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 16px; margin-bottom: 16px; color: #92400e; font-weight: 700; font-size: 0.92rem;">
                                <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 1.1rem; color: #d97706;"></i>
                                <span id="unpricedText"><?= e($initialCalculation['price_status_message'] ?? 'Pricing not configured for this specification.') ?></span>
                            </div>

                            <!-- Priced Calculation Panel -->
                            <div id="pricedDetailsContainer" style="display: <?= $initialCalculation['is_priced'] ? 'block' : 'none' ?>;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; color: #64748b;">
                                    <span>Base Product Price:</span>
                                    <strong id="lblBasePrice" style="color: #0f172a;">₹<?= number_format($initialCalculation['base_price'], 2) ?></strong>
                                </div>

                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; color: #64748b;">
                                    <span>Option Charges:</span>
                                    <strong id="lblOptionsTotal" style="color: #0f172a;">+₹<?= number_format($initialCalculation['options_total'], 2) ?></strong>
                                </div>

                                <?php if ($initialCalculation['tier_discount_amount'] > 0): ?>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; color: #16a34a; font-weight: 700;">
                                        <span><?= e($initialCalculation['tier_name']) ?> Tier Savings (<?= $initialCalculation['tier_discount_pct'] ?>%):</span>
                                        <strong id="lblTierDiscount">-₹<?= number_format($initialCalculation['tier_discount_amount'], 2) ?></strong>
                                    </div>
                                <?php endif; ?>

                                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.9rem; color: #64748b;">
                                    <span>GST Tax (18%):</span>
                                    <strong id="lblTaxAmount" style="color: #0f172a;">₹<?= number_format($initialCalculation['tax_amount'], 2) ?></strong>
                                </div>

                                <div style="border-top: 2px dashed #cbd5e1; padding-top: 14px; display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <span style="font-size: 0.78rem; color: #64748b; text-transform: uppercase; font-weight: 800; display: block; letter-spacing: 0.5px;">Grand Total (Inc. GST)</span>
                                        <div id="lblFinalTotal" style="font-size: 2.1rem; font-weight: 900; color: var(--cmyk-cyan); line-height: 1.1;">
                                            ₹<?= number_format($initialCalculation['final_total'], 2) ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right; font-size: 0.85rem; color: #64748b; font-weight: 700; background: #ffffff; padding: 8px 14px; border-radius: 10px; border: 1px solid #e2e8f0;">
                                        Per Unit: <strong id="lblPerUnitPrice" style="color: #0f172a; font-size: 0.95rem;">₹<?= number_format($initialCalculation['final_total'] / max(1, $initialQty), 2) ?></strong>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- SECTION 13: ACTION BUTTONS (ADD TO CART / REQUEST QUOTE) -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <button type="button" id="btnAddToCart" onclick="submitToB2BCart()" class="btn" style="background: #0f172a; color: #ffffff; font-weight: 800; border: 0; padding: 16px; border-radius: 12px; font-size: 1rem; cursor: pointer; opacity: <?= $initialCalculation['is_priced'] ? '1' : '0.4' ?>;" <?= $initialCalculation['is_priced'] ? '' : 'disabled' ?>>
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                            <button type="button" id="btnRequestQuote" onclick="submitToFormalQuote()" class="btn btn-primary" style="font-weight: 800; padding: 16px; border-radius: 12px; font-size: 1rem; cursor: pointer;">
                                <i class="fas fa-file-invoice-dollar"></i> Request Quote
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- ========================================================= -->
            <!-- RIGHT COLUMN: PRODUCT IMAGE GALLERY, SPECS & METADATA    -->
            <!-- ========================================================= -->
            <div>
                <div style="background: #ffffff; border-radius: 24px; border: 1px solid #e2e8f0; overflow: hidden; padding: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); position: sticky; top: 30px;">
                    
                    <!-- 1. PRODUCT IMAGE GALLERY -->
                    <div style="margin-bottom: 24px;">
                        <?php if (!empty($product['main_image'])): ?>
                            <div style="height: 320px; border-radius: 18px; background: #f1f5f9; overflow: hidden; position: relative; margin-bottom: 12px; border: 1px solid #e2e8f0;">
                                <img id="mainProductPreviewImage" src="<?= media_url($product['main_image']) ?>" alt="<?= e($product['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>

                            <?php if (!empty($galleryImages)): ?>
                                <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 6px;">
                                    <img src="<?= media_url($product['main_image']) ?>" alt="<?= e($product['name']) ?> Main Preview" onclick="switchPreviewImage(this.src)" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover; cursor: pointer; border: 2px solid var(--cmyk-cyan);" class="gallery-thumb">
                                    <?php foreach ($galleryImages as $gImg): ?>
                                        <img src="<?= media_url($gImg) ?>" alt="<?= e($product['name']) ?> Gallery Spec Image" onclick="switchPreviewImage(this.src)" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover; cursor: pointer; border: 1px solid #cbd5e1;" class="gallery-thumb">
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div style="height: 220px; border-radius: 18px; background: #f8fafc; border: 2px dashed #cbd5e1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; text-align: center; padding: 20px;">
                                <i class="fas fa-image" style="font-size: 2.5rem; margin-bottom: 10px; color: #cbd5e1;"></i>
                                <span style="font-weight: 700; font-size: 0.9rem;">No product images available</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 2. PRODUCT NAME & DESCRIPTION -->
                    <h1 style="font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0 0 10px 0; line-height: 1.2;">
                        <?= e($product['name']) ?>
                    </h1>

                    <div style="color: #475569; font-size: 0.92rem; line-height: 1.6; margin-bottom: 24px;">
                        <?= !empty($product['full_desc']) ? $product['full_desc'] : '<p>' . e($product['short_desc']) . '</p>' ?>
                    </div>

                    <!-- 3. ESTIMATED DISPATCH & PRODUCTION TIME -->
                    <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 14px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 14px;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--cmyk-cyan); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <span style="font-size: 0.78rem; text-transform: uppercase; font-weight: 800; color: #0369a1; display: block;">Estimated Dispatch & Production</span>
                            <strong style="color: #0f172a; font-size: 0.95rem;"><?= e($product['production_time'] ?? '2-3 Business Days') ?></strong>
                        </div>
                    </div>

                    <!-- 4. PRODUCT METADATA & TECHNICAL SPECIFICATIONS TABLE -->
                    <h4 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                        Technical Specifications
                    </h4>

                    <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem; margin-bottom: 24px;">
                        <?php if (!empty($product['sku'])): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 8px 0; color: #64748b; font-weight: 700;">Product Reference / SKU:</td>
                                <td style="padding: 8px 0; color: #0f172a; font-weight: 800; text-align: right;"><?= e($product['sku']) ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (!empty($product['product_code'])): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 8px 0; color: #64748b; font-weight: 700;">Product Code:</td>
                                <td style="padding: 8px 0; color: #0f172a; font-weight: 800; text-align: right;"><?= e($product['product_code']) ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (!empty($product['product_class'])): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 8px 0; color: #64748b; font-weight: 700;">Product Class:</td>
                                <td style="padding: 8px 0; color: #0f172a; font-weight: 800; text-align: right;"><?= e($product['product_class']) ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if (!empty($product['product_core'])): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 8px 0; color: #64748b; font-weight: 700;">Product Core:</td>
                                <td style="padding: 8px 0; color: #0f172a; font-weight: 800; text-align: right;"><?= e($product['product_core']) ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($specifications as $specKey => $specVal): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 8px 0; color: #64748b; font-weight: 700; width: 45%;"><?= e($specKey) ?>:</td>
                                <td style="padding: 8px 0; color: #0f172a; font-weight: 700; text-align: right;"><?= e($specVal) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <!-- 5. PRODUCT SPECIALIZATION (IF CONFIGURED) -->
                    <?php if (!empty($product['specialization'])): ?>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px; margin-bottom: 24px;">
                            <h5 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0 0 8px 0; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-certificate" style="color: var(--cmyk-magenta);"></i> Product Specialization
                            </h5>
                            <p style="color: #475569; font-size: 0.88rem; line-height: 1.5; margin: 0;">
                                <?= e($product['specialization']) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- 6. POINTS TO BE NOTED (IF CONFIGURED) -->
                    <?php if (!empty($product['points_to_note'])): ?>
                        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 14px; padding: 18px; margin-bottom: 24px;">
                            <h5 style="font-size: 0.95rem; font-weight: 800; color: #92400e; margin: 0 0 8px 0; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-lightbulb" style="color: #d97706;"></i> Points to Note
                            </h5>
                            <p style="color: #78350f; font-size: 0.88rem; line-height: 1.5; margin: 0;">
                                <?= e($product['points_to_note']) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- WHATSAPP DIRECT ESTIMATOR CONTACT -->
                    <a href="https://wa.me/<?= e(Setting::get('whatsapp_number', '918146141450')) ?>?text=<?= urlencode('Hello MD Printers, I need assistance with configuring: ' . $product['name']) ?>" target="_blank" class="btn btn-magenta" style="width: 100%; font-weight: 800; justify-content: center; border-radius: 12px; padding: 12px; font-size: 0.92rem;">
                        <i class="fab fa-whatsapp"></i> Chat with Press Estimator
                    </a>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
function switchPreviewImage(src) {
    var img = document.getElementById('mainProductPreviewImage');
    if (img) img.src = src;
}

function toggleArtworkFileInput() {
    var val = document.getElementById('artworkOptionSelect').value;
    var container = document.getElementById('artworkFileInputContainer');
    if (val === 'upload_now') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
}

function highlightRadioPill(radioInput) {
    var parentContainer = radioInput.closest('div');
    if (parentContainer) {
        parentContainer.querySelectorAll('.opt-radio-pill').forEach(function(pill) {
            pill.style.borderColor = '#cbd5e1';
            pill.style.background = '#ffffff';
        });
    }
    var currentPill = radioInput.closest('.opt-radio-pill');
    if (currentPill) {
        currentPill.style.borderColor = 'var(--cmyk-cyan)';
        currentPill.style.background = 'rgba(0,174,239,0.06)';
    }
}

function updateB2BPrice() {
    var form = document.getElementById('b2bOrderForm');
    var formData = new FormData(form);

    fetch('<?= SITE_URL ?>/api/b2b/calculate-price', {
        method: 'POST',
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            var calc = data.data;
            var isPriced = (calc.is_priced && parseFloat(calc.final_total) > 0);
            
            var warnBanner = document.getElementById('unpricedWarningBanner');
            var pricedContainer = document.getElementById('pricedDetailsContainer');
            var btnCart = document.getElementById('btnAddToCart');

            if (!isPriced) {
                warnBanner.style.display = 'block';
                document.getElementById('unpricedText').innerText = calc.price_status_message || 'Pricing not configured for this specification.';
                pricedContainer.style.display = 'none';
                btnCart.disabled = true;
                btnCart.style.opacity = '0.4';
                btnCart.style.cursor = 'not-allowed';
            } else {
                warnBanner.style.display = 'none';
                pricedContainer.style.display = 'block';
                btnCart.disabled = false;
                btnCart.style.opacity = '1';
                btnCart.style.cursor = 'pointer';

                document.getElementById('lblBasePrice').innerText = '₹' + parseFloat(calc.base_price).toFixed(2);
                document.getElementById('lblOptionsTotal').innerText = '+₹' + parseFloat(calc.options_total).toFixed(2);
                if (document.getElementById('lblTierDiscount')) {
                    document.getElementById('lblTierDiscount').innerText = '-₹' + parseFloat(calc.tier_discount_amount).toFixed(2);
                }
                document.getElementById('lblTaxAmount').innerText = '₹' + parseFloat(calc.tax_amount).toFixed(2);
                document.getElementById('lblFinalTotal').innerText = '₹' + parseFloat(calc.final_total).toFixed(2);
                
                var unitPrice = (parseFloat(calc.final_total) / Math.max(1, parseInt(calc.quantity))).toFixed(2);
                document.getElementById('lblPerUnitPrice').innerText = '₹' + unitPrice;
            }
        }
    })
    .catch(function(err) { console.error('Price calculation error:', err); });
}

function submitToB2BCart() {
    var orderName = document.getElementById('order_name_input').value.trim();
    if (!orderName) {
        alert('Please enter an Order / Job Reference Name before adding to cart.');
        document.getElementById('order_name_input').focus();
        return;
    }

    var form = document.getElementById('b2bOrderForm');
    var formData = new FormData(form);

    fetch('<?= SITE_URL ?>/api/b2b/cart-add', {
        method: 'POST',
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        alert(data.message);
        if (data.success) {
            window.location.href = '<?= SITE_URL ?>/dashboard/cart';
        }
    })
    .catch(function(err) {
        alert('Failed to add order to cart. Please check your inputs and try again.');
    });
}

function submitToFormalQuote() {
    var form = document.getElementById('b2bOrderForm');
    var formData = new FormData(form);

    fetch('<?= SITE_URL ?>/api/b2b/quote-request', {
        method: 'POST',
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        alert(data.message);
        if (data.success) {
            window.location.href = '<?= SITE_URL ?>/dashboard/quotes';
        }
    })
    .catch(function(err) {
        alert('Failed to submit quotation request. Please try again.');
    });
}

// Visual highlight for radio quantity pills
document.addEventListener('change', function(e) {
    if (e.target.name === 'quantity') {
        document.querySelectorAll('.qty-pill').forEach(function(el) {
            el.style.borderColor = '#e2e8f0';
            el.style.background = '#ffffff';
        });
        var label = e.target.closest('.qty-pill');
        if (label) {
            label.style.borderColor = 'var(--cmyk-cyan)';
            label.style.background = 'rgba(0,174,239,0.06)';
        }
    }
});
</script>

<?php include INCLUDES_PATH . '/footer.php'; ?>
