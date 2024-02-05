<?php

namespace App\Controller;

use App\Repository\ProductosRepository;
use App\Repository\StockRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StockController extends AbstractController
{
    private StockRepository $stockRepository;
    private ProductosRepository $productosRepository;

    public function __construct(StockRepository $stockRepository, ProductosRepository $productosRepository)
    {
        $this->stockRepository = $stockRepository;
        $this->productosRepository = $productosRepository;
    }

    #[Route('/stock/{id}', name: 'app_stock_producto',methods: ['GET'])]
    public function showStock(int $id): JsonResponse
    {

        $producto = $this->productosRepository->find($id);
        if (is_null($producto)) {
            return new JsonResponse(['status' => 'El producto no existe en la bd'], Response::HTTP_NOT_FOUND);
        }
        $data = $this->stockRepository->stockProductoJSON($producto);
        if (is_null($data)) {
            return new JsonResponse(['status' => 'El proveedor no existe en la bd'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    //Método que devuelve un array con el stock de todos los productos con la fecha que le pasamos
    #[Route('/stock', name: 'app_stock_fecha',methods: ['POST'])]
    public function stockFechaArray(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());
        if (is_null($data)) {
            return new JsonResponse(['status' => 'Error al decodificar el archivo json'], Response::HTTP_BAD_REQUEST);
        }
        $fecha = new DateTime($data->fecha);
        $productos = $this->productosRepository->findAll();
        if (empty($productos)) {
            return new JsonResponse(['status' => 'No hay productos en la bd'], Response::HTTP_NOT_FOUND);
        }
        $data=$this->stockRepository->stockFechaJSON($fecha,$productos);
        return new JsonResponse($data, Response::HTTP_OK);
    }

     //Método que devuelve un array con el stock de todos los productos con la fecha que le pasamos
     #[Route('/inventarios', name: 'app_stock_inventario',methods: ['GET'])]
     public function showInventario(): JsonResponse
     {
         $fecha = new DateTime();
         $productos = $this->productosRepository->findAll();
         if (empty($productos)) {
             return new JsonResponse(['status' => 'No hay productos en la bd'], Response::HTTP_NOT_FOUND);
         }
         $data=$this->stockRepository->inventarioJSON($fecha,$productos);
         return new JsonResponse($data, Response::HTTP_OK);
     }
}
