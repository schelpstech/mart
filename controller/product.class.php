<?php

class Product
{
    private $db;

    public function __construct($db_conn)
    {
        $this->db = $db_conn;
    }

    // Get products by section
    public function getProductsBySection($section_id)
    {
        $model = new Model($this->db);
        return $model->getRows("products p", [
            "select" => "p.*, c.category_name, s.section_name",
            "join" => "categories c ON p.category_id = c.id INNER JOIN sections s ON c.section_id = s.id",
            "where" => [
                "s.id" => $section_id,
                "p.status" => 'Active'
            ],
            "order_by" => "p.date_added DESC"
        ]);
    }

    // Get products by category
    public function getProductsByCategory($category_id)
    {
        $model = new Model($this->db);
        return $model->getRows("products p", [
            "select" => "p.*, c.category_name, s.section_name",
            "join" => "categories c ON p.category_id = c.id INNER JOIN sections s ON c.section_id = s.id",
            "where" => [
                "p.category_id" => $category_id,
                "p.status" => 'Active'
            ],
            "order_by" => "p.date_added DESC"
        ]);
    }

    // Get single product by ID
    public function getProductById($product_id)
    {
        $model = new Model($this->db);
        return $model->getRows("products p", [
            "select" => "p.*, c.category_name, s.section_name",
            "join" => "categories c ON p.category_id = c.id INNER JOIN sections s ON c.section_id = s.id",
            "where" => ["p.id" => $product_id],
            "return_type" => "single"
        ]);
    }

    // Search products by keyword (name, tag, category)
    public function searchProducts($keyword)
    {
        $model = new Model($this->db);
        return $model->getRows("products p", [
            "select" => "p.*, c.category_name, s.section_name",
            "join" => "categories c ON p.category_id = c.id INNER JOIN sections s ON c.section_id = s.id",
            "where_raw" => "(p.product_name LIKE '%$keyword%' OR p.tags LIKE '%$keyword%') AND p.status = 'Active'",
            "order_by" => "p.date_added DESC"
        ]);
    }
}
