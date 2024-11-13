<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Utils\Logger;

class OrderService extends BaseService
{
    private $productModel;

    protected function initializeModel()
    {
        $this->model = new Order();
        $this->productModel = new Product();
    }

    public function createOrder(array $input)
    {
        try {
            $productList = $this->prepareProductList($input['products']);
            
            $orderData = [
                'status' => 'pending',
                'total_amount' => $this->calculateTotalAmount($productList),
                'currency_label' => $input['currency_label'],
                'currency_symbol' => $input['currency_symbol'],
                'product_list' => $productList
            ];

            $orderId = $this->model->create($orderData);
            $createdOrder = $this->model->find($orderId);
            $createdOrder['product_list'] = json_decode($createdOrder['product_list'], true);
            
            return $createdOrder;

        } catch (\Exception $e) {
            Logger::error("Failed to create order: " . $e->getMessage());
            throw new \Exception("Failed to create order: " . $e->getMessage());
        }
    }

    private function prepareProductList(array $products)
    {
        $productList = [];
        
        foreach ($products as $orderProduct) {
            $product = $this->productModel->find($orderProduct['id']);
            if (!$product) {
                throw new \Exception("Product not found: " . $orderProduct['id']);
            }

            $productList[] = [
                'id' => $orderProduct['id'],
                'name' => $product['name'],
                'quantity' => $orderProduct['quantity'],
                'price' => $orderProduct['price'],
                'selectedAttributes' => $orderProduct['selectedAttributes'] ?? []
            ];
        }

        return $productList;
    }

    private function calculateTotalAmount(array $productList)
    {
        return array_reduce($productList, function($total, $item) {
            return $total + ($item['price'] * $item['quantity']);
        }, 0);
    }
}