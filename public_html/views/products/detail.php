<?php
/**
 * MD Printers - Product Detail & Interactive B2B Configurator View
 */
if (!isset($product) && isset($currentProduct)) {
    $product = $currentProduct;
}

require_once VIEWS_PATH . '/order/configurator.php';
