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
        $this->user_id = $_SESSION["user_id"] ?? $user_id;
    }
    public function getCartId()
    {
        $identifier = !empty($this->user_id) ? 'user_id' : 'session_id';
        $value = !empty($this->user_id) ? $this->user_id : $this->session_id;

        // Check if cart exists
        $row = $this->db->getRows("cart", [
            "where" => [$identifier => $value],
            "return_type" => "single"
        ]);

        if ($row) {
            return $row['cart_id'];
        }

        // Prepare new cart data
        $cartData = [
            "session_id" => $this->session_id,
            "created_at" => date("Y-m-d H:i:s")
        ];
        if (!empty($this->user_id)) {
            $cartData["user_id"] = $this->user_id;
        }

        // Create new cart
        $inserted = $this->db->insert("cart", $cartData);

        if ($inserted) {
            return $this->db->lastInsertId();
        }

        // If insert fails
        throw new Exception("Failed to create cart.");
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
        $identifier = !empty($this->user_id) ? 'user_id' : 'session_id';
        $value = !empty($this->user_id) ? $this->user_id : $this->session_id;

        // Check if cart exists
        $row = $this->db->getRows("cart", [
            "where" => [$identifier => $value],
            "return_type" => "single"
        ]);

        $cart_id =  $row['cart_id'];

        $row = $this->db->getRows("cart_items", [
            "where" => ["cart_id" => $cart_id],
            "return_type" => "count"
        ]);
        return $row;
    }

    public function getCartItems()
    {
        if (!empty($this->user_id)) {
            $user = $this->user_id;
            $check = "user_id";
        } else {
            $user  = $this->session_id;
            $check = "session_id";
        }

        $conditions = [
            "select"   => "ct.cart_item_id, c.cart_id, ct.product_id, ct.quantity, ct.price,
                       (ct.quantity * ct.price) AS line_total,
                       p.product_name AS name, p.image_main",
            "join"    => [
                "cart_items ct" => " ON c.cart_id = ct.cart_id",
                "products p"    => " ON ct.product_id = p.product_id"
            ],
            "where" => [$check => $user],
            "order_by" => "ct.cart_item_id DESC",
            "return_type" => "all"
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

    public function clearCart()
    {
        if (!empty($this->user_id)) {
            $user = $this->user_id;
            $check = "user_id";
        } else {
            $user  = $this->session_id;
            $check = "session_id";
        }

        $row = $this->db->getRows("cart", [
            "where" => [$check => $user],
            "return_type" => "single"
        ]);
        // Validate input
        if (!$row) return false;
        // Use model->delete to remove items
        $action = $this->db->delete("cart_items", ["cart_id" => $row['cart_id']]);
        return $action;
    }
}
