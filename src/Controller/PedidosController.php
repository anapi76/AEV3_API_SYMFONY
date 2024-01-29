<?php

namespace App\Controller;

use App\Entity\LineasPedidos;
use App\Entity\Pedidos;
use App\Repository\LineasPedidosRepository;
use App\Repository\PedidosRepository;
use App\Repository\ProductosRepository;
use App\Repository\ProveedoresRepository;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PedidosController extends AbstractController
{
    #[Route('/pedidos', name: 'app_pedidos')]
    public function index(PedidosRepository $pedidosRepository)/* : JsonResponse */
    {
        $data = $pedidosRepository->findAll();
        dump($data);
    }

    #[Route('/pedido', name: 'app_pedido', methods: ['POST'])]
    public function new(Request $request, ProveedoresRepository $proveedoresRepository,ProductosRepository $productosRepository, PedidosRepository $pedidosRepository,LineasPedidosRepository $lineasPedidoRepository): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            if (!is_null($data)) {
                $proveedor = $proveedoresRepository->findOneBy(['nombre' => $data->proveedor]);
                if (isset($proveedor) && !empty($proveedor)) {
                    $pedido = new Pedidos();
                    $pedido->setFecha(new DateTime());
                    $pedido->setProveedor($proveedor);
                    if (isset($data->detalles) && !empty($data->detalles)) {
                        $pedido->setDetalles($data->detalles);
                    }
                    $pedidosRepository->persist($pedido);
                    $proveedor->addPedido($pedido);
                    if (isset($data->productos) && !empty($data->productos)) {
                        $cont = 0;
                        $productos=$data->productos;
                        foreach($productos as $producto){
                            if((isset($producto->nombre)&& !empty($producto->nombre))&&(isset($producto->cantidad)&& !empty($producto->cantidad))){
                                $lineasPedido = new LineasPedidos();
                                $productoPedido=$productosRepository->findOneBy(['nombre'=>$producto->nombre]);
                                $lineasPedido->setProducto($productoPedido);
                                $lineasPedido->setCantidad($producto->cantidad);
                                $lineasPedido->setPedido($pedido);
                                $lineasPedidoRepository->persist($lineasPedido);
                                $pedido->addLineasPedido($lineasPedido);
                                $cont++;
                            }
                        }
                    }
                    if($cont>0){
                        $pedidosRepository->save(true);
                        if ($pedidosRepository->testInsert($pedido)) {
                            return new JsonResponse(['status' => 'Pedido insertado correctamente'], Response::HTTP_CREATED);
                        } else {
                            return new JsonResponse(['status' => 'El pedido no se ha insertado'], Response::HTTP_BAD_REQUEST);
                        }
                    }
                } else {
                    return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
                }
            } else {
                return new JsonResponse(['status' => 'Error al decodificar el archivo json'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
/*
{
    "proveedor": "Proveedor1",
    "detalle": "detalle1",
    "productos": [
        {"nombre": "Producto1", "cantidad": 10},
        {"nombre": "Producto2", "cantidad": 15}
    ]
}
*/
