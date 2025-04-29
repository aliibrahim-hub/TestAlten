<?php

namespace App\Service;

use src\Entity\Product;

class ProductStorageService
{
    private string $file;

    public function __construct()
    {
        $this->file = __DIR__ . '/../../data/products.json';
        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([]));
        }
    }

    public function getAll(): array
    {
        $data = json_decode(file_get_contents($this->file), true);
        return $data ?? [];
    }

    public function getById(int $id): ?array
    {
        foreach ($this->getAll() as $product) {
            if ($product['id'] === $id) {
                return $product;
            }
        }
        return null;
    }

    public function save(array $products): void
    {
        file_put_contents($this->file, json_encode($products, JSON_PRETTY_PRINT));
    }

    public function add(array $product): void
    {
        $products = $this->getAll();
        $products[] = $product;
        $this->save($products);
    }

    public function update(int $id, array $newData): bool
    {
        $products = $this->getAll();
        foreach ($products as &$product) {
            if ($product['id'] === $id) {
                $product = array_merge($product, $newData);
                $this->save($products);
                return true;
            }
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $products = $this->getAll();
        foreach ($products as $index => $product) {
            if ($product['id'] === $id) {
                array_splice($products, $index, 1);
                $this->save($products);
                return true;
            }
        }
        return false;
    }
}
