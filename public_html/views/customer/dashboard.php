<?php
require_customer_login();
include INCLUDES_PATH . '/header.php';

$customer = current_customer();
$addresses = Customer::getAddresses((int)$customer['id']);
$flash = get_flash();

$heroBadge    = 'CUSTOMER MEMBER PORTAL';
$heroTitle    = 'Welcome, ' . e($customer['contact_person']);
$heroSubtitle = e($customer['company_name']) . ' • ' . e($customer['customer_type']) . ' Tier Member';
$heroIcon     = 'fas fa-user-circle';
$breadcrumb   = ['Customer Portal' => ''];
include INCLUDES_PATH . '/inner-hero-banner.php';
?>

<section style="padding: 60px 0; background: #f8fafc;">
    <div class="container">
        
        <?php if ($flash): ?>
            <div class="form-alert alert-<?= e($flash['type']) ?>" style="display: block; padding: 16px; border-radius: 14px; margin-bottom: 30px; font-weight: 700;">
                <i class="fas fa-check-circle"></i> <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Customer Portal Top Navigation Tabs -->
        <div style="display: flex; gap: 10px; margin-bottom: 35px; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; flex-wrap: wrap; align-items: center;">
            <a href="<?= SITE_URL ?>/dashboard" class="btn btn-primary btn-sm" style="font-weight: 800;"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="<?= SITE_URL ?>/dashboard/orders" class="btn btn-outline btn-sm" style="font-weight: 800;"><i class="fas fa-box"></i> My Orders</a>
            <a href="<?= SITE_URL ?>/dashboard/quotes" class="btn btn-outline btn-sm" style="font-weight: 800;"><i class="fas fa-file-invoice-dollar"></i> My Quotes</a>
            <a href="<?= SITE_URL ?>/dashboard/cart" class="btn btn-outline btn-sm" style="font-weight: 800;"><i class="fas fa-shopping-cart"></i> My Cart</a>
            <a href="<?= SITE_URL ?>/dashboard/profile" class="btn btn-outline btn-sm" style="font-weight: 800;"><i class="fas fa-building"></i> Profile</a>

            <div style="margin-left: auto; display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="<?= SITE_URL ?>/order" class="btn btn-primary btn-sm" style="font-weight: 800; background: var(--cmyk-cyan); border-color: var(--cmyk-cyan); color: #ffffff;">
                    <i class="fas fa-shopping-cart"></i> START NEW ORDER
                </a>
                <a href="<?= SITE_URL ?>/request-quote" class="btn btn-magenta btn-sm" style="font-weight: 800;">
                    <i class="fas fa-edit"></i> REQUEST A QUOTE
                </a>
            </div>
        </div>

        <!-- Account Overview Metrics Bar -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 24px; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 16px;">
                <div style="width: 50px; height: 50px; border-radius: 14px; background: rgba(0, 168, 232, 0.12); color: var(--cmyk-cyan); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Account Status</span>
                    <strong style="font-size: 1.15rem; color: #10B981; text-transform: uppercase; font-weight: 900;"><?= e($customer['account_status']) ?></strong>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 24px; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 16px;">
                <div style="width: 50px; height: 50px; border-radius: 14px; background: rgba(230, 0, 103, 0.12); color: var(--cmyk-magenta); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                    <i class="fas fa-crown"></i>
                </div>
                <div>
                    <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Membership Tier</span>
                    <strong style="font-size: 1.15rem; color: #0f172a; font-weight: 900;"><?= e($customer['customer_type']) ?></strong>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 24px; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 16px;">
                <div style="width: 50px; height: 50px; border-radius: 14px; background: rgba(212, 175, 55, 0.15); color: var(--cmyk-gold); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Approved B2B Credit</span>
                    <strong style="font-size: 1.15rem; color: #0f172a; font-weight: 900;">₹<?= number_format((float)$customer['credit_limit'], 2) ?></strong>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 24px; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 16px;">
                <div style="width: 50px; height: 50px; border-radius: 14px; background: rgba(16, 185, 129, 0.12); color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <span style="display: block; font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">GSTIN Registered</span>
                    <strong style="font-size: 1rem; color: #0f172a; font-weight: 900;"><?= e($customer['gst_number'] ?: 'Not Added') ?></strong>
                </div>
            </div>
        </div>

        <!-- Dashboard Main Grid -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            
            <div>
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 30px; box-shadow: var(--shadow-sm); margin-bottom: 30px;">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px;">
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0;">B2B Customer Order & Quotation Hub</h3>
                        <div style="display: flex; gap: 8px;">
                            <a href="<?= SITE_URL ?>/order" class="btn btn-primary btn-sm" style="font-weight: 800;"><i class="fas fa-plus-circle"></i> Start Order</a>
                            <a href="<?= SITE_URL ?>/request-quote" class="btn btn-outline btn-sm" style="font-weight: 800;"><i class="fas fa-file-invoice"></i> Request Quote</a>
                        </div>
                    </div>

                    <!-- Two Distinct Action Cards Section -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                        
                        <!-- Action 1: Start New B2B Order -->
                        <div style="background: #f0f9ff; border: 1.5px solid #bae6fd; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--cmyk-cyan); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 14px;">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <h4 style="font-size: 1.15rem; font-weight: 900; color: #0f172a; margin: 0 0 8px 0;">NEW B2B CATALOGUE ORDER</h4>
                                <p style="color: #475569; font-size: 0.88rem; line-height: 1.5; margin-bottom: 16px;">
                                    Browse printing categories, select GSM paper weights, configure trade specs, and receive live server-calculated prices to place your order directly.
                                </p>
                            </div>
                            <a href="<?= SITE_URL ?>/order" class="btn btn-primary" style="font-weight: 800; border-radius: 10px; width: 100%; text-align: center; justify-content: center;">
                                🛒 START NEW ORDER <i class="fas fa-arrow-right" style="margin-left: 6px;"></i>
                            </a>
                        </div>

                        <!-- Action 2: Request Custom Quotation -->
                        <div style="background: #fdf2f8; border: 1.5px solid #fbcfe8; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--cmyk-magenta); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 14px;">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <h4 style="font-size: 1.15rem; font-weight: 900; color: #0f172a; margin: 0 0 8px 0;">CUSTOM PRINT REQUIREMENT</h4>
                                <p style="color: #475569; font-size: 0.88rem; line-height: 1.5; margin-bottom: 16px;">
                                    Don't see what you need in the standard catalogue? Submit custom specifications, pantone color codes, or artwork directly to our press estimators.
                                </p>
                            </div>
                            <a href="<?= SITE_URL ?>/request-quote" class="btn btn-magenta" style="font-weight: 800; border-radius: 10px; width: 100%; text-align: center; justify-content: center;">
                                📝 REQUEST A QUOTE <i class="fas fa-paper-plane" style="margin-left: 6px;"></i>
                            </a>
                        </div>

                    </div>

                    <!-- Dynamic Recent Orders Section -->
                    <div style="margin-bottom: 24px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <h4 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0;">
                                <i class="fas fa-box" style="color: var(--cmyk-cyan);"></i> Recent Orders
                            </h4>
                            <a href="<?= SITE_URL ?>/dashboard/orders" style="font-size: 0.82rem; font-weight: 700; color: var(--cmyk-cyan); text-decoration: none;">View All Orders <i class="fas fa-arrow-right"></i></a>
                        </div>

                        <?php 
                        require_once MODELS_PATH . '/Order.php';
                        $recentOrders = Order::getForCustomer((int)$customer['id']);
                        if (!empty($recentOrders)): 
                        ?>
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 0.78rem; text-transform: uppercase;">
                                            <th style="padding: 10px 12px;">Order #</th>
                                            <th style="padding: 10px 12px;">Date</th>
                                            <th style="padding: 10px 12px;">Total</th>
                                            <th style="padding: 10px 12px;">Payment</th>
                                            <th style="padding: 10px 12px;">Status</th>
                                            <th style="padding: 10px 12px; text-align: right;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($recentOrders, 0, 5) as $ord): ?>
                                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                                <td style="padding: 10px 12px; font-weight: 800; color: #0f172a;"><?= e($ord['order_number']) ?></td>
                                                <td style="padding: 10px 12px; color: #64748b;"><?= date('d M Y', strtotime($ord['created_at'])) ?></td>
                                                <td style="padding: 10px 12px; font-weight: 800; color: var(--cmyk-cyan);">₹<?= number_format((float)$ord['total'], 2) ?></td>
                                                <td style="padding: 10px 12px;">
                                                    <span style="background: <?= $ord['payment_status'] === 'paid' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $ord['payment_status'] === 'paid' ? '#15803d' : '#991b1b' ?>; font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 8px; text-transform: uppercase;">
                                                        <?= e($ord['payment_status']) ?>
                                                    </span>
                                                </td>
                                                <td style="padding: 10px 12px;">
                                                    <span style="background: #f1f5f9; color: #334155; font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 8px; text-transform: uppercase;">
                                                        <?= e(str_replace('_', ' ', $ord['status'])) ?>
                                                    </span>
                                                </td>
                                                <td style="padding: 10px 12px; text-align: right;">
                                                    <a href="<?= SITE_URL ?>/dashboard/orders?id=<?= $ord['id'] ?>" class="btn btn-sm btn-outline" style="font-weight: 700; font-size: 0.78rem; padding: 4px 10px; border-radius: 6px;">Details</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 24px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                                <i class="fas fa-box-open" style="font-size: 1.8rem; color: #94a3b8; margin-bottom: 8px; display: block;"></i>
                                <h5 style="font-weight: 800; color: #334155; margin: 0 0 4px 0;">No orders placed yet</h5>
                                <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 12px;">Start a new printing order directly from our product catalogue.</p>
                                <a href="<?= SITE_URL ?>/order" class="btn btn-primary btn-sm" style="font-weight: 800;">🛒 START NEW ORDER</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Dynamic Recent Quotes Section -->
                    <div style="margin-bottom: 24px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <h4 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0;">
                                <i class="fas fa-file-signature" style="color: var(--cmyk-magenta);"></i> Recent Quotes
                            </h4>
                            <a href="<?= SITE_URL ?>/dashboard/quotes" style="font-size: 0.82rem; font-weight: 700; color: var(--cmyk-magenta); text-decoration: none;">View All Quotes <i class="fas fa-arrow-right"></i></a>
                        </div>

                        <?php 
                        require_once MODELS_PATH . '/Quote.php';
                        $recentQuotes = Quote::getForCustomer((int)$customer['id']);
                        if (!empty($recentQuotes)): 
                        ?>
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; text-align: left;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 0.78rem; text-transform: uppercase;">
                                            <th style="padding: 10px 12px;">Quote #</th>
                                            <th style="padding: 10px 12px;">Date</th>
                                            <th style="padding: 10px 12px;">Amount</th>
                                            <th style="padding: 10px 12px;">Status</th>
                                            <th style="padding: 10px 12px; text-align: right;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($recentQuotes, 0, 5) as $q): ?>
                                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                                <td style="padding: 10px 12px; font-weight: 800; color: #0f172a;"><?= e($q['quote_number']) ?></td>
                                                <td style="padding: 10px 12px; color: #64748b;"><?= date('d M Y', strtotime($q['created_at'])) ?></td>
                                                <td style="padding: 10px 12px; font-weight: 800; color: var(--cmyk-magenta);">₹<?= number_format((float)$q['total'], 2) ?></td>
                                                <td style="padding: 10px 12px;">
                                                    <span style="background: #f1f5f9; color: #334155; font-size: 0.72rem; font-weight: 800; padding: 3px 8px; border-radius: 8px; text-transform: uppercase;">
                                                        <?= e($q['status']) ?>
                                                    </span>
                                                </td>
                                                <td style="padding: 10px 12px; text-align: right;">
                                                    <a href="<?= SITE_URL ?>/dashboard/quotes?id=<?= $q['id'] ?>" class="btn btn-sm btn-outline" style="font-weight: 700; font-size: 0.78rem; padding: 4px 10px; border-radius: 6px;">Details</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 24px; background: #fdf2f8; border-radius: 12px; border: 1px dashed #fbcfe8;">
                                <i class="fas fa-file-invoice" style="font-size: 1.8rem; color: #94a3b8; margin-bottom: 8px; display: block;"></i>
                                <h5 style="font-weight: 800; color: #334155; margin: 0 0 4px 0;">No custom quotes requested yet</h5>
                                <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 12px;">Submit custom specifications directly to our press estimators.</p>
                                <a href="<?= SITE_URL ?>/request-quote" class="btn btn-magenta btn-sm" style="font-weight: 800;">📝 REQUEST A QUOTE</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Quick Navigation Bar -->
                    <div style="display: flex; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 20px; flex-wrap: wrap;">
                        <a href="<?= SITE_URL ?>/dashboard/orders" class="btn btn-outline btn-sm" style="font-weight: 700;"><i class="fas fa-box"></i> VIEW MY ORDERS</a>
                        <a href="<?= SITE_URL ?>/dashboard/quotes" class="btn btn-outline btn-sm" style="font-weight: 700;"><i class="fas fa-file-signature"></i> VIEW MY QUOTES</a>
                        <a href="<?= SITE_URL ?>/dashboard/cart" class="btn btn-outline btn-sm" style="font-weight: 700;"><i class="fas fa-shopping-cart"></i> VIEW MY CART</a>
                    </div>

                </div>
            </div>

            <div>
                <!-- Member Account Quick Info -->
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 28px; box-shadow: var(--shadow-sm);">
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                        <i class="fas fa-building" style="color: var(--cmyk-cyan);"></i> Company Specs
                    </h3>
                    
                    <ul style="list-style: none; padding: 0; margin: 0 0 20px 0; display: flex; flex-direction: column; gap: 12px; font-size: 0.9rem;">
                        <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #f1f5f9; padding-bottom: 8px;">
                            <span style="color: #64748b;">Company:</span>
                            <strong style="color: #0f172a;"><?= e($customer['company_name']) ?></strong>
                        </li>
                        <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #f1f5f9; padding-bottom: 8px;">
                            <span style="color: #64748b;">Contact:</span>
                            <strong style="color: #0f172a;"><?= e($customer['contact_person']) ?></strong>
                        </li>
                        <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #f1f5f9; padding-bottom: 8px;">
                            <span style="color: #64748b;">Email:</span>
                            <strong style="color: #0f172a;"><?= e($customer['email']) ?></strong>
                        </li>
                        <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #f1f5f9; padding-bottom: 8px;">
                            <span style="color: #64748b;">Mobile:</span>
                            <strong style="color: #0f172a;"><?= e($customer['mobile']) ?></strong>
                        </li>
                        <li style="display: flex; justify-content: space-between; border-bottom: 1px dashed #f1f5f9; padding-bottom: 8px;">
                            <span style="color: #64748b;">Business Type:</span>
                            <strong style="color: #0f172a;"><?= e($customer['business_type'] ?: 'N/A') ?></strong>
                        </li>
                    </ul>

                    <div style="display: flex; gap: 10px;">
                        <a href="<?= SITE_URL ?>/dashboard/profile" class="btn btn-outline btn-sm" style="flex: 1;"><i class="fas fa-edit"></i> Edit Profile</a>
                        <a href="<?= SITE_URL ?>/logout" class="btn btn-outline btn-sm" style="color: #ef4444; border-color: #fca5a5;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
