<?php

namespace App\Services;

use App\Models\Category;

class CategoryService {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new Category();
    }

    public function findAll() {
        return $this->categoryModel->findAll();
    }

    public function findById($id) {
        return $this->categoryModel->find($id);
    }

    public function getWithProducts($id) {
        return $this->categoryModel->getWithProducts($id);
    }
}