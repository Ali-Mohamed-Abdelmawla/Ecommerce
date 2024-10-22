<?php

namespace App\Models;
// src/models/Product.php

use App\Utils\Logger;
use Ramsey\Uuid\Uuid;


class Product extends AbstractModel {
    protected string $table = 'products';

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByCategoryId($id){
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE category_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function create(array $data) {
        $id = Uuid::uuid4()->toString();
        $sql = "INSERT INTO {$this->table} (id, name, inStock, description, category_id, brand) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $id,
            $data['name'],
            $data['inStock'] ?? true,
            $data['description'] ?? null,
            $data['category_id'] ?? null,
            $data['brand'] ?? null
        ]);
        
        return $id;
    }

    public function update($id, array $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getWithAttributes($id) {
        $product = $this->find($id);
        if (!$product) return null;
    
        // Fetch all attributes and their items for this product
        $stmt = $this->db->prepare("
            SELECT a.id AS attribute_id, a.name AS attribute_name, a.type AS attribute_type, 
                   ai.id AS item_id, ai.value AS item_value, ai.display_value AS item_display_value
            FROM attributes a
            LEFT JOIN attribute_items ai ON ai.attribute_id = a.id
            WHERE a.product_id = ?
        ");
        $stmt->execute([$id]);
        $attributes = $stmt->fetchAll();
    
        // Group items by attribute
        $groupedAttributes = [];
        foreach ($attributes as $row) {
            $attributeId = $row['attribute_id'];
            
            if (!isset($groupedAttributes[$attributeId])) {
                $groupedAttributes[$attributeId] = [
                    'id' => $row['attribute_id'],
                    'name' => $row['attribute_name'],
                    'type' => $row['attribute_type'],
                    'items' => []
                ];
            }
            
            // Add each item to the corresponding attribute
            $groupedAttributes[$attributeId]['items'][] = [
                'id' => $row['item_id'],
                'value' => $row['item_value'],
                'display_value' => $row['item_display_value']
            ];
        }
    
        // Assign the grouped attributes to the product
        $product['attributes'] = array_values($groupedAttributes); // Reset keys to keep it as an array
    
        // Fetch other details like gallery and prices
        $product['gallery'] = $this->getProductGallery($id);
        $product['prices'] = $this->getProductPrices($id);
    
        return $product;
    }
    

    public function getProductPrices($id) {
        $stmt = $this->db->prepare("SELECT * FROM prices WHERE product_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }
    
    public function getProductGallery($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, image_url 
                FROM galleries 
                WHERE product_id = ?
            ");
            $stmt->execute([$id]);
            $result = $stmt->fetchAll();
            
            // Debug log
            Logger::debug('Gallery DB query result: ' . json_encode($result));
            
            return $result ?: null; // Return null if no results
        } catch (\PDOException $e) {
            Logger::error('Database error in getProductGallery: ' . $e->getMessage());
            throw $e;
        }
    }

}