<?php
class Cart
{
    private $db;
    private $session_id;
    private $user_id;

    public function __construct($db, $user_id = null)
    {
        $this->db = $db;
        $this->session_id = session_id();  // PHP’s session ID
        $this->user_id = $user_id;
    }
    private function getCartId()
    {
        // Check if cart already exists for this session
        $row = $this->db->getRows("cart", [
            "where" => ["session_id" => $this->session_id],
            "return_type" => "single"
        ]);

        if ($row) {
            return $row['cart_id'];
        } else {
            // Create new cart
            $this->db->insert("cart", [
                "session_id" => $this->session_id
            ]);
            return $this->db->lastInsertId();
        }
    }

    public function addToCart($product_id, $quantity, $price)
    {
        $cart_id = $this->getCartId();

        // Check if product already exists in this cart
        $row = $this->db->getRows("cart_items", [
            "where" => ["cart_id" => $cart_id, "product_id" => $product_id],
            "return_type" => "single"
        ]);

        if ($row) {
            // Update quantity
            $newQty = $row['quantity'] + $quantity;
            $this->db->update("cart_items", ["quantity" => $newQty], [
                "cart_item_id" => $row["cart_item_id"]
            ]);
        } else {
            // Insert new item
            $this->db->insert("cart_items", [
                "cart_id"    => $cart_id,
                "product_id" => $product_id,
                "quantity"   => $quantity,
                "price"      => $price
            ]);
        }
    }

    public function getCartCount()
    {
        $cart_id = $this->getCartId();
        $row = $this->db->getRows("cart_items", [
            "where" => ["cart_id" => $cart_id],
            "return_type" => "count"
        ]);
        return $row;
    }

    public function getCartItems()
    {
        // Build the WHERE clause using user_id if available, else session_id
        $where = [];
        if (!empty($this->user_id)) {
            $where['c.user_id'] = $this->user_id;
        } else {
            $where['c.session_id'] = $this->session_id;
        }

        // Compose the query using getRows + INNER JOINs via joinx
        $conditions = [
            "select"   => "ct.cart_item_id, c.cart_id, ct.product_id, ct.quantity, ct.price,
                       (ct.quantity * ct.price) AS line_total,
                       p.product_name AS name, p.image_main",
            "joinx"    => [
                "cart_items ct" => " ON c.cart_id = ct.cart_id",
                "products p"    => " ON ct.product_id = p.product_id"
            ],
            "where"    => $where,
            "order_by" => "ct.cart_item_id DESC"
        ];

        $rows = $this->db->getRows("cart c", $conditions);
        return $rows ?: [];
    }
    public function removeFromCart($cart_item_id)
    {
        // Validate input
        if (!$cart_item_id) return false;
        // Use model->delete to remove item
        return $this->db->delete("cart_items", ["cart_item_id" => $cart_item_id]);
    }
}
