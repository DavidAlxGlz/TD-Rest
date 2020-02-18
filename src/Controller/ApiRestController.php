<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CommandeRepository;
use App\Repository\ProductRepository;
use Normalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Flex\Response;

class ApiRestController extends AbstractController
{
    private $productRepository;
    private $commandeRepository;

    public function __construct(ProductRepository $productRepository,CommandeRepository $commandeRepository)
    {
        $this->productRepository = $productRepository;
        $this->commandeRepository = $commandeRepository;
    }


    /**
     * @Route("/api/product", name="api_rest_create",methods="POST")
     */
    public function createProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $title = $data['title'];
        $description = $data['description'];
        $price = $data['price'];


        if (empty($title) || empty($description) || empty($price)) {
            throw new NotFoundHttpException('Faltan parametros dude!');
        }

        $this->productRepository->saveProduct($title, $description, $price);

        return new JsonResponse(['status' => 'Producto Creado!! yeiii! ']);
    }

    /**
     * @Route("/api/Commande", name="api_rest_createCommande",methods="POST")
     */
     public function createCommande(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $creationDate = $data['creationDate'];
        $amount = $data['amount'];
        $username = $data['username'];


        if ( empty($amount) || empty($username)) {
            throw new NotFoundHttpException('Faltan parametros dude!');
        }

        $this->commandeRepository->saveOrder($creationDate,$amount,$username);

        return new JsonResponse(['status' => 'order Creado!! yeiii! ']);
    } 

    /**
     * @Route("/api/product/{id}", name="api_rest_update",methods="PUT")
     */
    public function updateProduct($id, Request $request): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['title']) ? true : $product->setTitle($data['title']);
        empty($data['description']) ? true : $product->setDescription($data['description']);
        empty($data['price']) ? true : $product->setPrice($data['price']);

        $updatedProduct = $this->productRepository->updateProduct($product);

        return new JsonResponse(['status' => 'Producto Editado!! yeiii! ']);
    }

    /**
     * @Route("/api/product/{id}", name="api_rest_delete", methods={"DELETE"})
     */
    public function deleteProduct($id): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);

        $this->productRepository->removeProduct($product);

        return new JsonResponse(['status' => 'product deleted']);
    }

    /**
     * @Route("/api/product", name="api_rest_all", methods={"GET"})
     */
    public function getAllProducts(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice()
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/product/{id}", name="api_rest_one", methods={"GET"})
     */
    public function getOneProduct($id): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $product->getId(),
                'title' => $product->getTitle(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice()
        ];

        return new JsonResponse($data);
    }
}
