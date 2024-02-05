<?php

namespace App\Controller;

use App\Repository\ComandasRepository;
use App\Repository\LineasComandasRepository;
use App\Repository\StockRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LineasComandasController extends AbstractController
{
    #[Route('/entregadaLineaComanda/{id}', name: 'app_lineas_comandas_update', methods: ['PATCH'])]
    public function edit(int $id, LineasComandasRepository $lineasComandasRepository, StockRepository $stockRepository, ComandasRepository $comandasRepository): JsonResponse
    {
        try {
            $lineaComanda = $lineasComandasRepository->find($id);
            if (is_null($lineaComanda)) {
                return new JsonResponse(['status' => "La linea comanda no existe en la bd"], Response::HTTP_NOT_FOUND);
            }
            $comanda = $lineaComanda->getComanda();
            if (is_null($lineaComanda)) {
                return new JsonResponse(['status' => "La comanda no existe en la bd"], Response::HTTP_NOT_FOUND);
            }
            $producto = $lineaComanda->getProducto();
            $cantidad = $lineaComanda->getCantidad();
            $stockProduct = $stockRepository->stockProducto($producto);
            if (is_null($stockProduct)) {
                return new JsonResponse(['status' => 'No se puede entregar la lineaComanda porque no hay suficiente stock del producto.'], Response::HTTP_BAD_REQUEST);
            }
            $cantidadStock = $stockProduct->getCantidad();
            $nuevaCantidad = $cantidadStock - $cantidad;
            if ($nuevaCantidad < 0) {
                return new JsonResponse(['status' => 'No se puede entregar la lineaComanda porque no hay suficiente stock del producto.'], Response::HTTP_BAD_REQUEST);
            }
            $lineaComanda->setEntregado(true);
            $lineasComandasRepository->persist($lineaComanda);
            $lineasComandasRepository->save(true);
            $lineas = $comanda->getLineasComandas();
            $entregado = true;
            foreach ($lineas as $linea) {
                if ($linea->isEntregado() == false) {
                    $entregado = false;
                }
            }
            if ($entregado) {
                $comanda->setEstado(false);
                $comandasRepository->persist($comanda);
                $comandasRepository->save(true);
            }
            $stock=$stockRepository->newStock($nuevaCantidad, $producto);
            if($stockRepository->testInsert($stock)){
                return new JsonResponse(['status' => 'Stock creado correctamente'], Response::HTTP_CREATED);
            }else{
                return new JsonResponse(['status' => 'La inserción del stock falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
