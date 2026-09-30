<?php
$service = [
    'id' => 0,
    'name' => '',
    'slug' => '',
    'icon' => 'fas fa-print',
    'short_desc' => '',
    'full_desc' => '',
    'main_image' => 'assets/images/services/offset-printing.jpg',
    'is_featured' => 1,
    'status' => 'published'
];

if ($id > 0) {
    $all = Service::getAll();
    foreach ($all as $item) {
        if ((int)$item['id'] === $id) {
            $service = array_merge($service, $item);
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['id'] = $id;
    if (empty($_POST['slug'])) $_POST['slug'] = slugify($_POST['name']);

    if (Service::save($_POST)) {
        set_flash('success', 'Service saved successfully.');
        header('Location: ' . ADMIN_URL . '?view=services');
        exit;
    }
}
?>

<div style="margin-bottom: 24px;">
    <a href="<?= ADMIN_URL ?>?view=services" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to Services</a>
</div>

<div class="admin-table-card" style="padding: 30px; max-width: 850px;">
    <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 24px; border-bottom: 1px solid var(--admin-border); padding-bottom: 12px;">
        <?= $id > 0 ? 'Edit Service' : 'Add New Service' ?>
    </h2>

    <form action="" method="POST">
        <?= csrf_field() ?>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Service Title *</label>
                <input type="text" name="name" value="<?= e($service['name']) ?>" class="form-control" required placeholder="e.g. Multi-Color Offset Printing">
            </div>

            <div class="form-group">
                <label class="form-label">FontAwesome Icon Class</label>
                <input type="text" name="icon" value="<?= e($service['icon']) ?>" class="form-control" placeholder="fas fa-industry">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Short Description</label>
            <textarea name="short_desc" rows="2" class="form-control" required><?= e($service['short_desc']) ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Full Description (HTML Supported)</label>
            <textarea name="full_desc" rows="6" class="form-control"><?= e($service['full_desc']) ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Image Path</label>
            <input type="text" name="main_image" value="<?= e($service['main_image']) ?>" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Service</button>
    </form>
</div>
