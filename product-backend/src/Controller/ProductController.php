<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\ProductStorageService;

class ProductController extends AbstractController
{
    private ProductStorageService $storage;

    public function __construct(ProductStorageService $storage)
    {
        $this->storage = $storage;
    }

    #[Route('/products', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $products = $this->storage->getAll();
        $data['id'] = count($products) ? max(array_column($products, 'id')) + 1 : 1;
        $data['createdAt'] = time();
        $data['updatedAt'] = time();

        $this->storage->add($data);

        return $this->json($data, Response::HTTP_CREATED);
    }

    #[Route('/products', methods: ['GET'])]
    public function getAll(): Response
    {
        return $this->json($this->storage->getAll());
    }

    #[Route('/products/{id}', methods: ['GET'])]
    public function getOne(int $id): Response
    {
        $product = $this->storage->getById($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($product);
    }

    #[Route('/products/{id}', methods: ['PATCH'])]
    public function update(Request $request, int $id): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $updated = $this->storage->update($id, array_merge($data, ['updatedAt' => time()]));

        if (!$updated) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['message' => 'Product updated']);
    }

    #[Route('/products/{id}', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $deleted = $this->storage->delete($id);

        if (!$deleted) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['message' => 'Product deleted']);
    }
}
