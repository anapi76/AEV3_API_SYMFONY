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

    //método que devuelve todos los productos
    #[Route('/productos', name: 'app_productos_all', methods: ['GET'])]
    public function showAll(): JsonResponse
    {
        $data = $this->productosRepository->productosAllJSON();
        if (is_null($data)) {
            return new JsonResponse(['status' => 'No existen productos en la bd'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    //método que devuelve un producto por su nombre o por su id (recibidos por json)
    #[Route('/productos', name: 'app_productos_id_nombre', methods: ['PATCH'])]
    public function show(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());
        if (is_null($data)) {
            return new JsonResponse(['status' => 'Error al decodificar el archivo json'], Response::HTTP_BAD_REQUEST);
        }
        if (isset($data->id) && !empty($data->id)) {
            $producto = $this->productosRepository->find($data->id);
        } elseif (isset($data->nombre) && !empty($data->nombre)) {
            $producto = $this->productosRepository->findOneBy(['nombre' => $data->nombre]);
        } else {
            return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
        }
        if (is_null($producto)) {
            return new JsonResponse(['status' => 'El producto no existe en la bd'], Response::HTTP_NOT_FOUND);
        }
        $json = $this->productosRepository->productoJSON($producto);
        return new JsonResponse($json, Response::HTTP_OK);
    }

    //método para insertar un producto
    #[Route('/productos', name: 'app_producto_new', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            if (is_null($data)) {
                return new JsonResponse(['status' => 'Error al decodificar el archivo json'], Response::HTTP_BAD_REQUEST);
            }
            if ((!isset($data->nombre) || empty($data->nombre)) || (!isset($data->precio) || empty($data->precio))) {
                return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
            }
            $producto=$this->productosRepository->findOneBy(['nombre'=>$data->nombre]);
            if(!is_null($producto)){
                return new JsonResponse(['status' => 'Nombre incorrecto, ya existe en la bd'], Response::HTTP_BAD_REQUEST);
            }
            $nombre = $data->nombre;
            $precio = $data->precio;
            $descripcion = (isset($data->descripcion) && !empty($data->descripcion)) ? $data->descripcion : null;
            $this->productosRepository->new($nombre, $precio, $descripcion, true);
            if ($this->productosRepository->testInsert($nombre)) {
                return new JsonResponse(['status' => 'Producto insertado correctamente'], Response::HTTP_CREATED);
            } else {
                return new JsonResponse(['status' => 'La inserción del producto falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //método para actualizar un producto
    #[Route('/productos/{id}', name: 'app_producto_edit', methods: ['PUT'])]
    public function edit(Request $request, ?int $id = null): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            if (is_null($data)) {
                return new JsonResponse(['status' => 'Error al decodificar el archivo json'], Response::HTTP_BAD_REQUEST);
            }
            if (is_null($id)) {
                return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
            }
            $producto = $this->productosRepository->find($id);
            if (is_null($producto)) {
                return new JsonResponse(['status' => 'El producto no existe en la bd'], Response::HTTP_NOT_FOUND);
            }
            $nombre = (isset($data->nombre) && !empty($data->nombre)) ? $data->nombre : null;
            $precio = (isset($data->precio) && !empty($data->precio)) ? $data->precio : null;
            $descripcion = (isset($data->descripcion) && !empty($data->descripcion)) ? $data->descripcion : null;
            if (!is_null($nombre) || !is_null($precio) || !is_null($descripcion)) {
                $this->productosRepository->update($producto, $nombre, $precio, $descripcion, true);
                if ($this->productosRepository->testUpdate($producto)) {
                    return new JsonResponse(['status' => 'Producto actualizado correctamente'], Response::HTTP_CREATED);
                } else {
                    return new JsonResponse(['status' => 'La actualización del producto falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                return new JsonResponse(['status' => 'No hay campos que actualizar'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

/* {
    "nombre":"tarta de queso",
    "descripcion":"postre",
    "precio":4.5
} */