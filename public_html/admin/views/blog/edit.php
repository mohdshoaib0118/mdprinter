<?php
$article = [
    'id' => 0,
    'title' => '',
    'slug' => '',
    'category_id' => 1,
    'excerpt' => '',
    'content' => '',
    'featured_image' => 'assets/images/blog/blog-1.jpg',
    'author' => 'MD Printers Team',
    'is_published' => 1
];

if ($id > 0) {
    $all = Blog::getAll();
    foreach ($all as $item) {
        if ((int)$item['id'] === $id) {
            $article = array_merge($article, $item);
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['id'] = $id;
    if (empty($_POST['slug'])) $_POST['slug'] = slugify($_POST['title']);

    if (Blog::save($_POST)) {
        set_flash('success', 'Blog article saved.');
        header('Location: ' . ADMIN_URL . '?view=blog');
        exit;
    }
}
?>

<div style="margin-bottom: 24px;">
    <a href="<?= ADMIN_URL ?>?view=blog" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to Blog Articles</a>
</div>

<div class="admin-table-card" style="padding: 30px; max-width: 900px;">
    <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 24px; border-bottom: 1px solid var(--admin-border); padding-bottom: 12px;">
        <?= $id > 0 ? 'Edit Article' : 'Write New Article' ?>
    </h2>

    <form action="" method="POST">
        <?= csrf_field() ?>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Article Title *</label>
                <input type="text" name="title" value="<?= e($article['title']) ?>" class="form-control" required placeholder="e.g. How to Choose Paper GSM">
            </div>

            <div class="form-group">
                <label class="form-label">Author Name</label>
                <input type="text" name="author" value="<?= e($article['author']) ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Excerpt / Summary</label>
            <textarea name="excerpt" rows="2" class="form-control" required><?= e($article['excerpt']) ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Full Article Content (HTML Supported)</label>
            <textarea name="content" rows="8" class="form-control"><?= e($article['content']) ?></textarea>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Featured Image Path</label>
                <input type="text" name="featured_image" value="<?= e($article['featured_image']) ?>" class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="is_published" class="form-control">
                    <option value="1" <?= $article['is_published'] ? 'selected' : '' ?>>Published</option>
                    <option value="0" <?= !$article['is_published'] ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Article</button>
    </form>
</div>
