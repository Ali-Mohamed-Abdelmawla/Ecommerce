<?php

namespace App\Models;

use App\Utils\Logger;
use Ramsey\Uuid\Uuid;

class Order extends AbstractModel
{
    protected string $table = 'orders';

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        Logger::debug("Query result after request: " . json_encode($order));

        if ($order) {
            $order['createdAt'] = date('Y-m-d\TH:i:s\Z', strtotime($order['created_at']));
        }

        return $order;
    }

    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        $orders = $stmt->fetchAll();

        foreach ($orders as &$order) {
            $order['product_list'] = json_decode($order['product_list'], true);
        }

        return $orders;
    }

    public function create(array $data)
    {
        try {
            $id = Uuid::uuid4()->toString();
            Logger::debug("Creating order with ID: $id");

            // Ensure product_list is JSON
            if (is_array($data['product_list'])) {
                $data['product_list'] = json_encode($data['product_list']);
            }

            Logger::debug("Order data: " . json_encode($data));

            $sql = "INSERT INTO {$this->table} (
            id, status, total_amount, currency_label, currency_symbol, product_list, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, NOW())";

            Logger::debug("SQL: $sql");

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $id,
                $data['status'] ?? 'pending',
                $data['total_amount'],
                $data['currency_label'],
                $data['currency_symbol'],
                $data['product_list']
            ]);

            Logger::debug("statement executed :  " . json_encode($stmt->errorInfo()));
            Logger::debug("Order created with ID: $id");

            return $id;
        } catch (\Exception $e) {
            Logger::error($e->getMessage());
            return null;
        }
    }

    public function update($id, array $data)
    {
        $fields = [];
        $values = [];

        // Handle product_list JSON conversion
        if (isset($data['product_list']) && is_array($data['product_list'])) {
            $data['product_list'] = json_encode($data['product_list']);
        }

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }

        $values[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateStatus($id, string $status)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}
