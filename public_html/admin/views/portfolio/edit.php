<?php
$project = [
    'id' => 0,
    'title' => '',
    'slug' => '',
    'category_id' => 1,
    'client_name' => '',
    'project_date' => '',
    'description' => '',
    'image' => 'assets/images/portfolio/portfolio-1.jpg',
    'services_used' => '',
    'status' => 'published'
];

if ($id > 0) {
    $all = Portfolio::getAll();
    foreach ($all as $item) {
        if ((int)$item['id'] === $id) {
            $project = array_merge($project, $item);
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['id'] = $id;
    if (empty($_POST['slug'])) $_POST['slug'] = slugify($_POST['title']);

    if (Portfolio::save($_POST)) {
        set_flash('success', 'Portfolio project saved.');
        header('Location: ' . ADMIN_URL . '?view=portfolio');
        exit;
    }
}
$categories = Portfolio::getCategories();
?>

<div style="margin-bottom: 24px;">
    <a href="<?= ADMIN_URL ?>?view=portfolio" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to Portfolio</a>
</div>

<div class="admin-table-card" style="padding: 30px; max-width: 850px;">
    <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 24px; border-bottom: 1px solid var(--admin-border); padding-bottom: 12px;">
        <?= $id > 0 ? 'Edit Portfolio Case Study' : 'Add New Portfolio Project' ?>
    </h2>

    <form action="" method="POST">
        <?= csrf_field() ?>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Project Title *</label>
                <input type="text" name="title" value="<?= e($project['title']) ?>" class="form-control" required placeholder="e.g. Aura Skincare Packaging">
            </div>

            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-control">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $project['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Client Name</label>
                <input type="text" name="client_name" value="<?= e($project['client_name']) ?>" class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">Completion Date</label>
                <input type="text" name="project_date" value="<?= e($project['project_date']) ?>" class="form-control" placeholder="e.g. January 2026">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Services Executed</label>
            <input type="text" name="services_used" value="<?= e($project['services_used']) ?>" class="form-control" placeholder="e.g. Rigid Box, Foil Stamping">
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-control" required><?= e($project['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Image Path</label>
            <input type="text" name="image" value="<?= e($project['image']) ?>" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Project</button>
    </form>
</div>
