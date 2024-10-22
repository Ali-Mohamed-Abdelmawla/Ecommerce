<?php

namespace App\Services;

use App\Models\Product;
use App\Utils\Logger;

class ProductService {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    public function findAll(?string $categoryId = null) {
        try {
            if ($categoryId) {
                $result = $this->productModel->findByCategoryId($categoryId);
                Logger::debug('FindByCategoryId result: ' . json_encode($result));
                return $result;
            }
            $result = $this->productModel->findAll();
            Logger::debug('FindAll result: ' . json_encode($result));
            return $result;
        } catch (\Throwable $e) {
            Logger::error('Error in ProductService findAll: ' . $e->getMessage());
            throw $e;
        }
    }

    public function findById(string $id) {
        try {
            Logger::debug("Fetching product with ID: $id");
            
            // Fetch basic product data
            $product = $this->productModel->find($id);
            
            if (!$product) {
                Logger::debug("Product not found with ID: $id");
                return null;
            }
            
            // Fetch gallery
            $product['gallery'] = $this->getProductGallery($id);
            
            // Fetch attributes
            $product['attributes'] = $this->getProductAttributes($id);

            // Fetch prices
            $product['prices'] = $this->getProductPrices($id);
            
            Logger::debug("Complete product data: " . json_encode($product));
            
            return $product;
        } catch (\Throwable $e) {
            Logger::error("Error fetching product with ID $id: " . $e->getMessage());
            throw $e;
            
        }
    }

    private function getProductAttributes(string $productId) {
        Logger::debug("Fetching attributes for product: $productId");
        $attributes = $this->productModel->getWithAttributes($productId)['attributes'] ?? [];
        Logger::debug("Attributes result: " . json_encode($attributes));
        return $attributes;
    }


    private function getProductPrices(string $productId) {
        Logger::debug("Fetching prices for product: $productId");
        $prices = $this->productModel->getProductPrices($productId);
        Logger::debug("Prices result: " . json_encode($prices));
        return $prices;
    }

    private function getProductGallery($productId) {
        Logger::debug("Fetching gallery for product: $productId");
        $gallery = $this->productModel->getProductGallery($productId);
        Logger::debug("Gallery result: " . json_encode($gallery));
        return $gallery;
    }
}