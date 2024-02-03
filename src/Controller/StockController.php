<?php

namespace App\Controller;

use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StockController extends AbstractController
{
    private StockRepository $stockRepository;

    public function __construct(StockRepository $stockRepository)
    {
        $this->stockRepository = $stockRepository;
    }

    #[Route('/stock/{id}', name: 'app_stock_producto')]
    public function showStock(int $id): JsonResponse
    {
        $data = $this->stockRepository->stockProductoJSON($id);
        if (!is_null($data)) {
            return new JsonResponse($data, Response::HTTP_OK);
        } else {
            return new JsonResponse(['status' => 'El proveedor no existe en la bd'], Response::HTTP_NOT_FOUND);
        }
    }
}
