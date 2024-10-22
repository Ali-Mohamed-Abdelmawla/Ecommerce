<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Utils\Logger;

class OrderService {
    private $orderModel;
    private $productModel;

    public function __construct() {
        $this->orderModel = new Order();
        $this->productModel = new Product();
    }

    public function createOrder(array $input) {
        try {
            $totalAmount = 0;
            $productList = [];

            foreach ($input['products'] as $orderProduct) {
                $product = $this->productModel->find($orderProduct['id']);
                if (!$product) {
                    throw new \Exception("Product not found: " . $orderProduct['id']);
                }

                $itemTotal = $orderProduct['price'] * $orderProduct['quantity'];
                $totalAmount += $itemTotal;

                $productList[] = [
                    'id' => $orderProduct['id'],
                    'name' => $product['name'],
                    'quantity' => $orderProduct['quantity'],
                    'price' => $orderProduct['price'],
                    'selectedAttributes' => $orderProduct['selectedAttributes'] ?? []
                ];
            }

            $orderData = [
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'currency_label' => $input['currency_label'],
                'currency_symbol' => $input['currency_symbol'],
                'product_list' => $productList
            ];

            $orderId = $this->orderModel->create($orderData);
            $createdOrder = $this->orderModel->find($orderId);

            $createdOrder['product_list'] = json_decode($createdOrder['product_list'], true);
            Logger::debug("Created order: " . json_encode($createdOrder));
            return $createdOrder;

        } catch (\Exception $e) {
            Logger::error("Failed to create order: " . $e->getMessage());
            throw new \Exception("Failed to create order: " . $e->getMessage());
        }
    }

}