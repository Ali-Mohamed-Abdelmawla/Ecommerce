<?php

namespace App\Services;

use App\Models\Product;
use App\Utils\Logger;

class ProductService extends BaseService
{
    protected function initializeModel()
    {
        $this->model = new Product();
    }

    public function findAll(?string $categoryId = null)
    {
        try {
            if ($categoryId) {
                return $this->model->findByCategoryId($categoryId);
            }
            return $this->model->findAll();
        } catch (\Throwable $e) {
            Logger::error('Error in ProductService findAll: ' . $e->getMessage());
            throw $e;
        }
    }

    public function findById(string $id)
    {
        try {
            $product = $this->model->find($id);
            
            if (!$product) {
                return null;
            }
            
            return array_merge($product, [
                'gallery' => $this->getProductGallery($id),
                'attributes' => $this->getProductAttributes($id),
                'prices' => $this->getProductPrices($id)
            ]);
            
        } catch (\Throwable $e) {
            Logger::error("Error fetching product with ID $id: " . $e->getMessage());
            throw $e;
        }
    }

    private function getProductAttributes(string $productId)
    {
        return $this->model->getWithAttributes($productId)['attributes'] ?? [];
    }

    private function getProductPrices(string $productId)
    {
        return $this->model->getProductPrices($productId);
    }

    private function getProductGallery($productId)
    {
        return $this->model->getProductGallery($productId);
    }
}