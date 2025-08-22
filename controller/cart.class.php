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

    public function getLatestCartItemId($product_id)
    {
        // Fetch latest cart_item_id for this session and product
        $row = $this->db->getRows("cart_items", [
            "join" => [
                "cart" => "ON cart.cart_id = cart_items.cart_id"
            ],
            "where" => [
                "cart.session_id" => $this->session_id,
                "cart_items.product_id" => $product_id
            ],
            "order_by" => "cart_items.cart_item_id DESC",
            "return_type" => "single"
        ]);

        if ($row) {
            return $row['cart_item_id'];
        }

        return null; // No matching record found
    }


    public function addToCart($product_id, $quantity, $price)
    {
        $cart_id = $this->getCartId(); // ensures cart exists for this session

        // Check if product already exists in cart_items
        $row = $this->db->getRows("cart_items", [
            "where" => [
                "cart_id" => $cart_id,
                "product_id" => $product_id
            ],
            "return_type" => "single"
        ]);

        if ($row) {
            // Update quantity
            $newQty = $row['quantity'] + $quantity;
            $this->db->update("cart_items", ["quantity" => $newQty], ["cart_item_id" => $row['cart_item_id']]);
            return $row['cart_item_id']; // return the existing ID
        } else {
            // Insert new product into cart_items
            $this->db->insert("cart_items", [
                "cart_id"    => $cart_id,
                "product_id" => $product_id,
                "quantity"   => $quantity,
                "price"      => $price
            ]);

            return $this->db->lastInsertId(); // return the new cart_item_id
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

    public function getCartItemID($product_id)
    {
        $cart_id = $this->getCartId(); // ensures cart exists for this session
        // Check if product already exists in cart_items
        $row = $this->db->getRows("cart_items", [
            "where" => [
                "cart_id" => $cart_id,
                "product_id" => $product_id
            ],
            "return_type" => "single"
        ]);
        return $row;
    }
    public function removeFromCart($cart_item_id)
    {
        // Validate input
        if (!$cart_item_id) return false;
        // Use model->delete to remove item
        $row = $this->db->delete("cart_items", ["cart_item_id" => $cart_item_id]);
        return $row;
    }
}
