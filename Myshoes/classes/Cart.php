<?php
/**
 * Cart - session-based shopping cart helpers.
 */
require_once __DIR__ . '/Product.php';

class Cart
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function add($shoeId, $qty = 1)
    {
        $shoeId = (int) $shoeId;
        $qty = max(1, (int) $qty);
        if (isset($_SESSION['cart'][$shoeId])) {
            $_SESSION['cart'][$shoeId] += $qty;
        } else {
            $_SESSION['cart'][$shoeId] = $qty;
        }
    }

    public function remove($shoeId)
    {
        $shoeId = (int) $shoeId;
        if (isset($_SESSION['cart'][$shoeId])) {
            unset($_SESSION['cart'][$shoeId]);
            return true;
        }
        return false;
    }

    public function clear()
    {
        $_SESSION['cart'] = [];
    }

    public function isEmpty()
    {
        return empty($_SESSION['cart']);
    }

    /** @return array shoe_id => quantity */
    public function getItems()
    {
        return $_SESSION['cart'];
    }

    public function countItems()
    {
        return array_sum($_SESSION['cart']);
    }

    /**
     * Cart lines with product details for display.
     * @return array list of [shoe_id, qty, name, price, image_col, line_total]
     */
    public function getDetailedItems()
    {
        $productModel = new Product();
        $lines = [];

        foreach ($this->getItems() as $shoeId => $qty) {
            $product = $productModel->findById($shoeId);
            if (!$product) {
                continue;
            }
            $lines[] = [
                'shoe_id' => $shoeId,
                'qty' => (int) $qty,
                'name' => $product['name'],
                'price' => (float) $product['price'],
                'image_col' => $product['image_col'],
                'brand' => $product['brand'] ?? '',
                'category' => $product['category'] ?? 'shoes',
                'line_total' => (float) $product['price'] * (int) $qty,
            ];
        }

        return $lines;
    }

    public function getTotal()
    {
        $total = 0.0;
        foreach ($this->getDetailedItems() as $line) {
            $total += $line['line_total'];
        }
        return $total;
    }
}
