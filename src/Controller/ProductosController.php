<?php

namespace App\Controller;

use App\Repository\ProductosRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductosController extends AbstractController
{
    private ProductosRepository $productosRepository;

    public function __construct(ProductosRepository $productosRepository)
    {
        $this->productosRepository = $productosRepository;
    }

    #[Route('/productos', name: 'app_productos_all', methods: ['GET'])]
    public function showAll(): JsonResponse
    {
        if (empty($_GET)) {
            $data = $this->productosRepository->productosAllJSON();
            if (is_null($data)) {
                return new JsonResponse(['status' => 'No existen productos en la bd'], Response::HTTP_NOT_FOUND);
            } else {
                return new JsonResponse($data, Response::HTTP_OK);
            }
        } else {
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $producto = $this->productosRepository->find($_GET['id']);
            } elseif (isset($_GET['nombre']) && !empty($_GET['nombre'])) {
                $producto = $this->productosRepository->findOneBy(['nombre' => $_GET['nombre']]);
            }
            if (!is_null($producto)) {
                $data = $this->productosRepository->productoJSON($producto);
                if (is_null($data)) {
                    return new JsonResponse(['status' => 'El producto no existe en la bd'], Response::HTTP_NOT_FOUND);
                } else {
                    return new JsonResponse($data, Response::HTTP_OK);
                }
            } else {
                return new JsonResponse(['status' => 'El producto no existe en la bd'], Response::HTTP_NOT_FOUND);
            }
        }
    }

    #[Route('/productos/{id?}', name: 'app_producto_new', methods: ['POST', 'PUT'])]
    public function add(Request $request, ?int $id = null): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            $method = $request->getMethod();
            if (!is_null($data)) {
                if ($method === 'POST' && is_null($id)) {
                    if ((isset($data->nombre) && !empty($data->nombre)) && (isset($data->precio) && !empty($data->precio))) {
                        $nombre = $data->nombre;
                        $precio = $data->precio;
                        $descripcion = (isset($data->descripcion) && !empty($data->descripcion)) ? $data->descripcion : null;
                    } else {
                        return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
                    }
                } elseif ($method === 'PUT' && !is_null($id)) {
                    $nombre = (isset($data->nombre) && !empty($data->nombre)) ? $data->nombre : null;
                    $precio = (isset($data->precio) && !empty($data->precio)) ? $data->precio : null;
                    $descripcion = (isset($data->descripcion) && !empty($data->descripcion)) ? $data->descripcion : null;
                } else {
                    return new JsonResponse(['status' => 'Método incorrecto'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                if ($this->productosRepository->new($nombre, $precio, $descripcion)) {
                    return new JsonResponse(['status' => 'Producto insertado correctamente'], Response::HTTP_CREATED);
                } else {
                    return new JsonResponse(['status' => 'La inserción del producto falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
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
