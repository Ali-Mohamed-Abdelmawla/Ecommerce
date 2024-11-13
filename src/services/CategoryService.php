<?php

namespace App\Services;

use App\Models\Category;

class CategoryService extends BaseService
{
    protected function initializeModel()
    {
        $this->model = new Category();
    }

    public function findAll()
    {
        return $this->model->findAll();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function getWithProducts($id)
    {
        return $this->model->getWithProducts($id);
    }
}