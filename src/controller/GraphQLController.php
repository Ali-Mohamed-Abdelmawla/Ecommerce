<?php

namespace App\Controller;

use GraphQL\GraphQL;
use GraphQL\Utils\BuildSchema;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\OrderService;
use App\Utils\Logger;

class GraphQLController {
    // Static method as required
    public static function handle() {
        try {
            $productService = new ProductService();   // Static instantiation
            $categoryService = new CategoryService(); // Static instantiation
            $orderService = new OrderService();       // Static instantiation

            $schemaFile = file_get_contents(__DIR__ . '/../schemas/schema.graphql');
            $schema = BuildSchema::build($schemaFile);

            $rootValue = [
                'categories' => function() use ($categoryService) { 
                    return $categoryService->findAll();
                },
                
                'category' => function($rootValue, $args) use ($categoryService, $productService) {
                    $categoryId = $args['id'];

                    // If category ID is 1, return all products
                    if ($categoryId == 1) {
                        // Fetch all products
                        $allProducts = $productService->findAll();

                        // For each product, return one image and the price
                        return [
                            'id' => 1,
                            'name' => 'All Products',
                            'typename' => 'Category',
                            'products' => array_map(function($product) use ($productService) {
                                $productDetails = $productService->findById($product['id']);
                                
                                // Return only the first image in the gallery and price
                                $product['gallery'] = $productDetails['gallery'] ? [$productDetails['gallery'][0]] : [];
                                $product['prices'] = $productDetails['prices'] ?? [];
                                $product['attributes'] = $productDetails['attributes'] ?? [];

                                return $product;
                            }, $allProducts)
                        ];
                    }

                    // For other categories, return the category and its products
                    $category = $categoryService->findById($categoryId);
                    if ($category) {
                        $category['products'] = array_map(function($product) use ($productService) {
                            $productDetails = $productService->findById($product['id']);

                            // Return only the first image in the gallery and price
                            $product['gallery'] = $productDetails['gallery'] ? [$productDetails['gallery'][0]] : [];
                            $product['prices'] = $productDetails['prices'] ?? [];
                            $product['attributes'] = $productDetails['attributes'] ?? [];
                            return $product;
                        }, $productService->findAll($categoryId));  // Get products by category
                    }

                    return $category;
                },

                'products' => function($rootValue, $args) use ($productService) {
                    return $productService->findAll($args['categoryId'] ?? null);
                },

                'product' => function($rootValue, $args) use ($productService) {
                    return $productService->findById($args['id']);
                },

                'createOrder' => function($rootValue, $args) use ($orderService) {
                    try {
                        Logger::debug("createOrder called with input: " . json_encode($args['input']));
                        return $orderService->createOrder($args['input']);
                    } catch (\Exception $e) {
                        Logger::error("Error in createOrder resolver: " . $e->getMessage());
                        throw new \GraphQL\Error\UserError($e->getMessage());
                    }
                }
                
            ];

            // Get query input
            $rawInput = file_get_contents('php://input');
            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;
            // Execute the query
            $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues);
            $output = $result->toArray();
        } catch (\Throwable $e) {
            $output = [
                'errors' => [
                    [
                        'message' => $e->getMessage(),
                    ]
                ]
            ];
            Logger::error("GraphQL error: " . $e->getMessage());
        }

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($output);
    }
}
