<?php

class Cart
{
    private $db;
    private $session_id;
    private $user_id;
    private static $expiredCartCleanupRan = false;

    public function __construct($db, $user_id = null)
    {
        $this->db = $db;
        $this->session_id = session_id();
        $this->user_id = $_SESSION["user_id"] ?? $user_id;
    }

    public function getCartId()
    {
        $identifier = !empty($this->user_id) ? 'user_id' : 'session_id';
        $value = !empty($this->user_id) ? $this->user_id : $this->session_id;

        $rows = $this->db->getRows("cart", [
            "where" => [$identifier => $value],
            "order_by" => "cart_id ASC",
            "return_type" => "all"
        ]);

        if (!empty($rows)) {
            $primaryId = (int)$rows[0]['cart_id'];
            if (count($rows) > 1) {
                $this->mergeDuplicateCarts($primaryId, array_slice($rows, 1));
            }
            return $primaryId;
        }

        $cartData = [
            "session_id" => $this->session_id,
            "created_at" => date("Y-m-d H:i:s")
        ];

        if (!empty($this->user_id)) {
            $cartData["user_id"] = $this->user_id;
        }

        $inserted = $this->db->insert("cart", $cartData);
        if ($inserted) {
            return $this->db->lastInsertId();
        }

        throw new Exception("Failed to create cart.");
    }

    private function mergeDuplicateCarts($primaryId, array $duplicateRows)
    {
        foreach ($duplicateRows as $row) {
            $duplicateId = (int)$row['cart_id'];
            if ($duplicateId <= 0 || $duplicateId === (int)$primaryId) {
                continue;
            }

            $productItems = $this->db->getRows("cart_items", [
                "where" => ["cart_id" => $duplicateId],
                "return_type" => "all"
            ]);
            foreach ($productItems as $item) {
                $existing = $this->db->getRows("cart_items", [
                    "where" => [
                        "cart_id" => $primaryId,
                        "product_id" => $item['product_id']
                    ],
                    "return_type" => "single"
                ]);

                if ($existing) {
                    $this->db->update(
                        "cart_items",
                        ["quantity" => (int)$existing['quantity'] + (int)$item['quantity']],
                        ["cart_item_id" => $existing['cart_item_id']]
                    );
                    $this->db->delete("cart_items", ["cart_item_id" => $item['cart_item_id']]);
                } else {
                    $this->db->update("cart_items", ["cart_id" => $primaryId], ["cart_item_id" => $item['cart_item_id']]);
                }
            }

            $serviceItems = $this->db->getRows("service_cart_items", [
                "where" => ["cart_id" => $duplicateId],
                "return_type" => "all"
            ]);
            foreach ($serviceItems as $item) {
                $existing = $this->db->getRows("service_cart_items", [
                    "where" => [
                        "cart_id" => $primaryId,
                        "service_id" => $item['service_id']
                    ],
                    "return_type" => "single"
                ]);

                if ($existing) {
                    $this->db->update(
                        "service_cart_items",
                        ["quantity" => (int)$existing['quantity'] + (int)$item['quantity']],
                        ["service_cart_item_id" => $existing['service_cart_item_id']]
                    );
                    $this->db->delete("service_cart_items", ["service_cart_item_id" => $item['service_cart_item_id']]);
                } else {
                    $this->db->update("service_cart_items", ["cart_id" => $primaryId], ["service_cart_item_id" => $item['service_cart_item_id']]);
                }
            }

            $this->db->delete("cart", ["cart_id" => $duplicateId]);
        }
    }

    private function prepareCartLines($cart_id)
    {
        $cart_id = (int)$cart_id;
        if ($cart_id <= 0) {
            return;
        }

        $this->removeExpiredCartLines();
        $this->removeUnavailableCartLines($cart_id);
        $this->mergeDuplicateLineItems($cart_id, "cart_items", "cart_item_id", "product_id");
        $this->mergeDuplicateLineItems($cart_id, "service_cart_items", "service_cart_item_id", "service_id");
    }

    private function removeExpiredCartLines()
    {
        if (self::$expiredCartCleanupRan) {
            return;
        }

        self::$expiredCartCleanupRan = true;

        try {
            $stmt = $this->db->prepare("DELETE FROM service_cart_items WHERE added_at < DATE_SUB(NOW(), INTERVAL 1 MONTH)");
            $stmt->execute();
        } catch (Exception $e) {
            try {
                $stmt = $this->db->prepare("
                    DELETE sct FROM service_cart_items sct
                    INNER JOIN cart c ON sct.cart_id = c.cart_id
                    WHERE c.created_at < DATE_SUB(NOW(), INTERVAL 1 MONTH)
                ");
                $stmt->execute();
            } catch (Exception $fallbackException) {
                error_log("Expired service cart cleanup failed: " . $fallbackException->getMessage());
            }
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM cart_items WHERE added_at < DATE_SUB(NOW(), INTERVAL 1 MONTH)");
            $stmt->execute();
        } catch (Exception $e) {
            try {
                $stmt = $this->db->prepare("
                    DELETE ci FROM cart_items ci
                    INNER JOIN cart c ON ci.cart_id = c.cart_id
                    WHERE c.created_at < DATE_SUB(NOW(), INTERVAL 1 MONTH)
                ");
                $stmt->execute();
            } catch (Exception $fallbackException) {
                error_log("Expired product cart cleanup failed: " . $fallbackException->getMessage());
            }
        }
    }

    private function removeUnavailableCartLines($cart_id)
    {
        try {
            $stmt = $this->db->prepare("
                DELETE ci FROM cart_items ci
                LEFT JOIN products p ON ci.product_id = p.product_id
                WHERE ci.cart_id = :cart_id
                  AND (p.product_id IS NULL OR LOWER(p.status) <> 'active')
            ");
            $stmt->execute([':cart_id' => (int)$cart_id]);
        } catch (Exception $e) {
            error_log("Product cart cleanup failed: " . $e->getMessage());
        }

        try {
            $stmt = $this->db->prepare("
                DELETE sct FROM service_cart_items sct
                LEFT JOIN services s ON sct.service_id = s.id
                WHERE sct.cart_id = :cart_id
                  AND (s.id IS NULL OR LOWER(s.status) <> 'active')
            ");
            $stmt->execute([':cart_id' => (int)$cart_id]);
        } catch (Exception $e) {
            error_log("Service cart cleanup failed: " . $e->getMessage());
        }
    }

    private function mergeDuplicateLineItems($cart_id, $table, $primaryKey, $sourceKey)
    {
        try {
            $items = $this->db->getRows($table, [
                "where" => ["cart_id" => (int)$cart_id],
                "order_by" => $sourceKey . " ASC, " . $primaryKey . " ASC",
                "return_type" => "all"
            ]);
        } catch (Exception $e) {
            error_log("Cart duplicate lookup failed: " . $e->getMessage());
            return;
        }

        $seen = [];

        foreach ($items as $item) {
            $sourceId = (int)($item[$sourceKey] ?? 0);
            if ($sourceId <= 0) {
                continue;
            }

            if (!isset($seen[$sourceId])) {
                $seen[$sourceId] = $item;
                continue;
            }

            $primary = $seen[$sourceId];
            $newQuantity = (int)$primary['quantity'] + (int)$item['quantity'];

            $this->db->update($table, ["quantity" => max(1, $newQuantity)], [$primaryKey => $primary[$primaryKey]]);
            $this->db->delete($table, [$primaryKey => $item[$primaryKey]]);
            $seen[$sourceId]['quantity'] = max(1, $newQuantity);
        }
    }

    public function addToCart($product_id, $quantity, $price)
    {
        $cart_id = $this->getCartId();
        $this->prepareCartLines($cart_id);
        $product_id = (int)$product_id;
        $quantity = max(1, (int)$quantity);
        $price = max(0, (float)$price);

        $row = $this->db->getRows("cart_items", [
            "where" => [
                "cart_id" => $cart_id,
                "product_id" => $product_id
            ],
            "return_type" => "single"
        ]);

        if ($row) {
            $newQty = (int)$row['quantity'] + $quantity;
            $this->db->update("cart_items", ["quantity" => $newQty, "price" => $price], ["cart_item_id" => $row['cart_item_id']]);
            return $row['cart_item_id'];
        }

        $this->db->insert("cart_items", [
            "cart_id" => $cart_id,
            "product_id" => $product_id,
            "quantity" => $quantity,
            "price" => $price
        ]);

        return $this->db->lastInsertId();
    }

    public function addServiceToCart($service_id, $quantity, $price)
    {
        $cart_id = $this->getCartId();
        $this->prepareCartLines($cart_id);
        $service_id = (int)$service_id;
        $quantity = max(1, (int)$quantity);
        $price = max(0, (float)$price);

        $row = $this->db->getRows("service_cart_items", [
            "where" => [
                "cart_id" => $cart_id,
                "service_id" => $service_id
            ],
            "return_type" => "single"
        ]);

        if ($row) {
            $newQty = (int)$row['quantity'] + $quantity;
            $this->db->update(
                "service_cart_items",
                ["quantity" => $newQty, "price" => $price],
                ["service_cart_item_id" => $row['service_cart_item_id']]
            );
            return $row['service_cart_item_id'];
        }

        $this->db->insert("service_cart_items", [
            "cart_id" => $cart_id,
            "service_id" => $service_id,
            "quantity" => $quantity,
            "price" => $price
        ]);

        return $this->db->lastInsertId();
    }

    public function getCartCount()
    {
        $summary = $this->getCartSummary();
        return $summary['count'];
    }

    public function getCartItems()
    {
        $cart_id = $this->getCartId();
        $this->prepareCartLines($cart_id);
        $rows = $this->db->getRows("cart c", [
            "select" => "ct.cart_item_id, c.cart_id, ct.product_id, ct.quantity, ct.price,
                       (ct.quantity * ct.price) AS line_total,
                       p.product_name AS name, p.product_slug, p.image_main",
            "join" => [
                "cart_items ct" => " ON c.cart_id = ct.cart_id",
                "products p" => " ON ct.product_id = p.product_id"
            ],
            "where_raw" => "c.cart_id = " . (int)$cart_id . " AND LOWER(p.status) = 'active'",
            "order_by" => "ct.cart_item_id DESC",
            "return_type" => "all"
        ]);

        foreach ($rows as &$row) {
            $row['item_type'] = 'product';
            $row['item_id'] = (int)$row['cart_item_id'];
            $row['source_id'] = (int)$row['product_id'];
            $row['image'] = "../view/assets/images/product/main/" . ($row['image_main'] ?: 'default.png');
            $row['url'] = "viewproduct.php?slug=" . urlencode($row['product_slug'] ?? '');
        }

        return $rows ?: [];
    }

    public function getServiceCartItems()
    {
        $cart_id = $this->getCartId();
        $this->prepareCartLines($cart_id);
        $rows = $this->db->getRows("cart c", [
            "select" => "sct.service_cart_item_id, c.cart_id, sct.service_id, sct.quantity, sct.price,
                       (sct.quantity * sct.price) AS line_total,
                       s.name AS name, s.slug, s.image",
            "join" => [
                "service_cart_items sct" => " ON c.cart_id = sct.cart_id",
                "services s" => " ON sct.service_id = s.id"
            ],
            "where_raw" => "c.cart_id = " . (int)$cart_id . " AND LOWER(s.status) = 'active'",
            "order_by" => "sct.service_cart_item_id DESC",
            "return_type" => "all"
        ]);

        foreach ($rows as &$row) {
            $row['item_type'] = 'service';
            $row['item_id'] = (int)$row['service_cart_item_id'];
            $row['cart_item_id'] = (int)$row['service_cart_item_id'];
            $row['source_id'] = (int)$row['service_id'];
            $row['image'] = "../view/assets/images" . ($row['image'] ?: '/services/default.png');
            $row['url'] = "viewservice.php?slug=" . urlencode($row['slug'] ?? '');
        }

        return $rows ?: [];
    }

    public function getAllCartItems()
    {
        return array_merge($this->getCartItems(), $this->getServiceCartItems());
    }

    public function getCartSummary()
    {
        $productItems = $this->getCartItems();
        $serviceItems = $this->getServiceCartItems();
        $items = array_merge($productItems, $serviceItems);
        $subtotal = 0.00;
        $count = 0;

        foreach ($items as $item) {
            $subtotal += (float)$item['price'] * (int)$item['quantity'];
            $count += (int)$item['quantity'];
        }

        return [
            'product_items' => $productItems,
            'service_items' => $serviceItems,
            'items' => $items,
            'subtotal' => round($subtotal, 2),
            'count' => $count
        ];
    }

    public function getCartItemID($product_id)
    {
        $cart_id = $this->getCartId();
        return $this->db->getRows("cart_items", [
            "where" => [
                "cart_id" => $cart_id,
                "product_id" => (int)$product_id
            ],
            "return_type" => "single"
        ]);
    }

    public function removeFromCart($cart_item_id, $itemType = 'product')
    {
        $cart_item_id = (int)$cart_item_id;
        if ($cart_item_id <= 0) {
            return false;
        }

        $cart_id = (int)$this->getCartId();

        if ($itemType === 'service') {
            $line = $this->db->getRows("service_cart_items", [
                "where" => ["service_cart_item_id" => $cart_item_id, "cart_id" => $cart_id],
                "return_type" => "single"
            ]);

            if (!$line) {
                return false;
            }

            return $this->db->delete("service_cart_items", [
                "cart_id" => $cart_id,
                "service_id" => $line['service_id']
            ]);
        }

        $line = $this->db->getRows("cart_items", [
            "where" => ["cart_item_id" => $cart_item_id, "cart_id" => $cart_id],
            "return_type" => "single"
        ]);

        if (!$line) {
            return false;
        }

        return $this->db->delete("cart_items", [
            "cart_id" => $cart_id,
            "product_id" => $line['product_id']
        ]);
    }

    public function updateQuantity($cart_item_id, $quantity, $itemType = 'product')
    {
        $cart_item_id = (int)$cart_item_id;
        $quantity = max(1, (int)$quantity);
        $cart_id = (int)$this->getCartId();

        if ($itemType === 'service') {
            $line = $this->db->getRows("service_cart_items", [
                "where" => ["service_cart_item_id" => $cart_item_id, "cart_id" => $cart_id],
                "return_type" => "single"
            ]);

            if (!$line) {
                return false;
            }

            $this->removeDuplicateLineItems($cart_id, "service_cart_items", "service_cart_item_id", "service_id", (int)$line['service_id'], $cart_item_id);
            return $this->db->update("service_cart_items", ["quantity" => $quantity], ["service_cart_item_id" => $cart_item_id]);
        }

        $line = $this->db->getRows("cart_items", [
            "where" => ["cart_item_id" => $cart_item_id, "cart_id" => $cart_id],
            "return_type" => "single"
        ]);

        if (!$line) {
            return false;
        }

        $this->removeDuplicateLineItems($cart_id, "cart_items", "cart_item_id", "product_id", (int)$line['product_id'], $cart_item_id);
        return $this->db->update("cart_items", ["quantity" => $quantity], ["cart_item_id" => $cart_item_id]);
    }

    private function removeDuplicateLineItems($cart_id, $table, $primaryKey, $sourceKey, $sourceId, $keepId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$table}
            WHERE cart_id = :cart_id
              AND {$sourceKey} = :source_id
              AND {$primaryKey} <> :keep_id
        ");

        $stmt->execute([
            ':cart_id' => (int)$cart_id,
            ':source_id' => (int)$sourceId,
            ':keep_id' => (int)$keepId
        ]);
    }

    public function getLineItem($cart_item_id, $itemType = 'product')
    {
        $cart_id = (int)$this->getCartId();

        if ($itemType === 'service') {
            return $this->db->getRows("service_cart_items", [
                "where" => ["service_cart_item_id" => (int)$cart_item_id, "cart_id" => $cart_id],
                "return_type" => "single"
            ]);
        }

        return $this->db->getRows("cart_items", [
            "where" => ["cart_item_id" => (int)$cart_item_id, "cart_id" => $cart_id],
            "return_type" => "single"
        ]);
    }

    public function clearCart()
    {
        $cart_id = $this->getCartId();
        $this->db->delete("cart_items", ["cart_id" => $cart_id]);
        $this->db->delete("service_cart_items", ["cart_id" => $cart_id]);
        return true;
    }
}
