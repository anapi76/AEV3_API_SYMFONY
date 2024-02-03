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
        if (!is_null($data)) {
            return new JsonResponse($data, Response::HTTP_OK);
        } else {
            return new JsonResponse(['status' => 'No existen productos en la bd'], Response::HTTP_NOT_FOUND);
        }
    }

    //método que devuelve un producto por su nombre o por su id (recibidos por json)
    #[Route('/productos/{find}', name: 'app_productos', methods: ['GET'])]
    public function show(mixed $find): JsonResponse
    {
        if (!is_null($find)) {
            if (is_numeric($find)) {
                $producto = $this->productosRepository->find($find);
            } else {
                $producto = $this->productosRepository->findOneBy(['nombre' => $find]);
            }
            if (!is_null($producto)) {
                $data = $this->productosRepository->productoJSON($producto);
                return new JsonResponse($data, Response::HTTP_OK);
            } else {
                return new JsonResponse(['status' => 'El producto no existe en la bd'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
        }
    }

    //método para insertar un producto o actualizarlo si ya existe en la bd
    #[Route('/productos/{id}', name: 'app_producto_new', methods: ['POST', 'PUT'])]
    public function add(Request $request, ?int $id=null): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            $method = $request->getMethod();
            if (!is_null($data)) {
                $producto = $this->productosRepository->find($id);
                if ($method === 'POST' && is_null($id)) {
                    if ((isset($data->nombre) && !empty($data->nombre)) && (isset($data->precio) && !empty($data->precio))) {
                        $nombre = $data->nombre;
                        $precio = $data->precio;
                        $descripcion = (isset($data->descripcion) && !empty($data->descripcion)) ? $data->descripcion : null;
                        if ($this->productosRepository->new($nombre, $precio, $descripcion)) {
                            return new JsonResponse(['status' => 'Producto insertado correctamente'], Response::HTTP_CREATED);
                        } else {
                            return new JsonResponse(['status' => 'La inserción del producto falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    } else {
                        return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
                    }
                } elseif ($method === 'PUT' && !is_null($producto)) {
                    $nombre = (isset($data->nombre) && !empty($data->nombre)) ? $data->nombre : null;
                    $precio = (isset($data->precio) && !empty($data->precio)) ? $data->precio : null;
                    $descripcion = (isset($data->descripcion) && !empty($data->descripcion)) ? $data->descripcion : null;
                    if (!is_null($nombre) || !is_null($precio) || !is_null($descripcion)) {
                        if ($this->productosRepository->update($producto, $nombre, $precio, $descripcion)) {
                            return new JsonResponse(['status' => 'Producto actualizado correctamente'], Response::HTTP_CREATED);
                        } else {
                            return new JsonResponse(['status' => 'La actualización del producto falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    } else {
                        return new JsonResponse(['status' => 'No hay campos que actualizar'], Response::HTTP_BAD_REQUEST);
                    }
                } else {
                    return new JsonResponse(['status' => 'Método incorrecto'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                return new JsonResponse(['status' => 'Error al decodificar el archivo json'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*     //método para insertar un producto
    #[Route('/productos', name: 'app_producto_new', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            if (!is_null($data)) {
                if ((isset($data->nombre) && !empty($data->nombre)) && (isset($data->precio) && !empty($data->precio))) {
                    $nombre = $data->nombre;
                    $precio = $data->precio;
                    $descripcion = (isset($data->descripcion) && !empty($data->descripcion)) ? $data->descripcion : null;
                } else {
                    return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
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

    //método para actualizar un producto
    #[Route('/productos/{id}', name: 'app_producto_edit', methods: ['PUT'])]
    public function edit(Request $request, int $id): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            if (!is_null($data)) {
                $producto = $this->productosRepository->find($id);
                if (!is_null($producto)) {
                    $nombre = (isset($data->nombre) && !empty($data->nombre)) ? $data->nombre : null;
                    $precio = (isset($data->precio) && !empty($data->precio)) ? $data->precio : null;
                    $descripcion = (isset($data->descripcion) && !empty($data->descripcion)) ? $data->descripcion : null;
                    if(!is_null($nombre) ||!is_null($precio)||!is_null($descripcion) ){
                        if ($this->productosRepository->update($producto, $nombre, $precio, $descripcion)) {
                            return new JsonResponse(['status' => 'Producto actualizado correctamente'], Response::HTTP_CREATED);
                        } else {
                            return new JsonResponse(['status' => 'La actualización del producto falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    }else{
                        return new JsonResponse(['status' => 'No hay campos que actualizar'], Response::HTTP_BAD_REQUEST);
                    }
                    
                } else {
                    return new JsonResponse(['status' => 'El producto no existe en la bd'], Response::HTTP_NOT_FOUND);
                }
            } else {
                return new JsonResponse(['status' => 'Error al decodificar el archivo json'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    } */
}
