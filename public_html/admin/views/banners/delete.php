<?php
/**
 * MD Printers Admin - Delete Hero Banner Slide View
 */

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    if (Banner::delete($id)) {
        set_flash('success', 'Banner slide removed successfully.');
    } else {
        set_flash('error', 'Failed to delete banner slide.');
    }
}

redirect_to(ADMIN_URL . '?view=banners');
