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
    //Método para crear un nuevo pedido
    #[Route('/pedido', name: 'app_pedido', methods: ['POST'])]
    public function add(Request $request, ProveedoresRepository $proveedoresRepository, ProductosRepository $productosRepository, PedidosRepository $pedidosRepository, LineasPedidosRepository $lineasPedidoRepository): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            if (is_null($data)) {
                return new JsonResponse(['status' => 'Error al decodificar el archivo json'], Response::HTTP_BAD_REQUEST);
            }
            if ((!isset($data->proveedor) || empty($data->proveedor)) || (!isset($data->fecha) || empty($data->fecha))) {
                return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
            }
            $proveedor = $proveedoresRepository->findOneBy(['nombre' => $data->proveedor]);
            if (is_null($proveedor)) {
                return new JsonResponse(['status' => 'El proveedor no existe en la bd'], Response::HTTP_NOT_FOUND);
            }
            $pedido = new Pedidos();
            $pedido->setProveedor($proveedor);
            $fecha = DateTime::createFromFormat("d/m/Y H:i:s", $data->fecha);
            $pedido->setFecha($fecha);
            if (isset($data->detalles) && !empty($data->detalles)) {
                $pedido->setDetalles($data->detalles);
            }
            if (isset($data->estado) && !empty($data->estado)) {
                if ($data->estado === 'creado') {
                    $estado = true;
                } elseif ($data->estado === 'entregado') {
                    $estado = false;
                }
                $pedido->setEstado($estado);
            }
            $pedidosRepository->persist($pedido);
            $proveedor->addPedido($pedido);
            if (!isset($data->productos) || empty($data->productos)) {
                return new JsonResponse(['status' => 'No se puede crear el pedido porque no hay lineas de pedido'], Response::HTTP_BAD_REQUEST);
            }
            $cont = 0;
            $productos = $data->productos;
            foreach ($productos as $producto) {
                if ((!isset($producto->nombre) || empty($producto->nombre)) || (!isset($producto->cantidad) || empty($producto->cantidad))) {
                    return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
                }
                $lineasPedido = new LineasPedidos();
                $productoPedido = $productosRepository->findOneBy(['nombre' => $producto->nombre]);
                if (is_null($productoPedido)) {
                    return new JsonResponse(['status' => "El producto " . $producto->nombre . " no existe en la bd"], Response::HTTP_NOT_FOUND);
                }
                $lineasPedido->setProducto($productoPedido);
                $lineasPedido->setCantidad($producto->cantidad);
                if (isset($data->entregado) && !empty($data->entregado)) {
                    if ($data->estado === 'entregado') {
                        $estado = true;
                    } elseif ($data->estado === 'pendiente') {
                        $estado = false;
                    }
                    $pedido->setEstado($estado);
                }
                $lineasPedido->setPedido($pedido);
                $lineasPedidoRepository->persist($lineasPedido);
                $pedido->addLineasPedido($lineasPedido);
                $cont++;
            }
            if ($cont > 0) {
                $pedidosRepository->save(true);
                if ($pedidosRepository->testInsert($pedido)) {
                    return new JsonResponse(['status' => 'Pedido creado correctamente'], Response::HTTP_CREATED);
                } else {
                    return new JsonResponse(['status' => 'La creación del pedido falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                return new JsonResponse(['status' => 'No se puede crear el pedido porque no hay lineas de pedido válidas'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
