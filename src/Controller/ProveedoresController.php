<?php

namespace App\Controller;

use App\Repository\ProveedoresRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProveedoresController extends AbstractController
{
    private ProveedoresRepository $proveedoresRepository;

    public function __construct(ProveedoresRepository $proveedoresRepository)
    {
        $this->proveedoresRepository = $proveedoresRepository;
    }

    #[Route('/proveedor', name: 'app_proveedor_all', methods: ['GET'])]
    public function showAll(): JsonResponse
    {
        $data = $this->proveedoresRepository->proveedoresAllJSON();
        if (is_null($data)) {
            return new JsonResponse(['status' => 'No existen proveedores en la bd'], Response::HTTP_NOT_FOUND);
        } else {
            return new JsonResponse($data, Response::HTTP_OK);
        }
    }

    #[Route('/proveedor/{id}', name: 'app_proveedor', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $data = $this->proveedoresRepository->proveedorJSON($id);
        if (is_null($data)) {
            return new JsonResponse(['status' => 'El proveedor no existe en la bd'], Response::HTTP_NOT_FOUND);
        } else {
            return new JsonResponse($data, Response::HTTP_OK);
        }
    }

    #[Route('/proveedor', name: 'app_proveedor_new', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            if (!is_null($data)) {
                if ((isset($data->nombre) && !empty($data->nombre)) && (isset($data->cif) && !empty($data->cif)) && (isset($data->direccion) && !empty($data->direccion))) {
                    $telefono = (isset($data->telefono) && !empty($data->telefono)) ? $data->telefono : null;
                    $email = (isset($data->email) && !empty($data->email)) ? $data->email : null;
                    $contacto = (isset($data->contacto) && !empty($data->contacto)) ? $data->contacto : null;
                    if ($this->proveedoresRepository->new($data->nombre, $data->cif, $data->direccion, $telefono, $email, $contacto)) {
                        return new JsonResponse(['status' => 'Proveedor insertado correctamente'], Response::HTTP_CREATED);
                    } else {
                        return new JsonResponse(['status' => 'La inserción del proveedor falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
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

    #[Route('/proveedor/{id?}', name: 'app_proveedor_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, ?int $id = null): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            $method = $request->getMethod();
            if (!is_null($data)) {
                if ($method === 'PUT' && (isset($_GET['nombre']) && !empty($_GET['nombre']))) {
                    $proveedor = $this->proveedoresRepository->findOneBy(['nombre' => $_GET['nombre']]);
                } elseif ($method === 'PATCH' && !is_null($id)) {
                    $proveedor = $this->proveedoresRepository->find($id);
                } else {
                    return new JsonResponse(['status' => 'Método incorrecto'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                if (!is_null($proveedor)) {
                    $cont = 0;
                    if (isset($data->nombre) && !empty($data->nombre)) {
                        $nombre = $data->nombre;
                        $cont++;
                    } else {
                        $nombre = null;
                    }
                    if (isset($data->cif) && !empty($data->cif)) {
                        $cif = $data->cif;
                        $cont++;
                    } else {
                        $cif = null;
                    }
                    if (isset($data->direccion) && !empty($data->direccion)) {
                        $direccion = $data->direccion;
                        $cont++;
                    } else {
                        $direccion = null;
                    }
                    if (isset($data->telefono) && !empty($data->telefono)) {
                        $telefono = $data->telefono;
                        $cont++;
                    } else {
                        $telefono = null;
                    }
                    if (isset($data->email) && !empty($data->email)) {
                        $email = $data->email;
                        $cont++;
                    } else {
                        $email = null;
                    }
                    if (isset($data->contacto) && !empty($data->contacto)) {
                        $contacto = $data->contacto;
                        $cont++;
                    } else {
                        $contacto = null;
                    }
                    if ($cont > 0) {
                        if ($this->proveedoresRepository->update($proveedor, $nombre, $cif, $direccion, $telefono, $email, $contacto)) {
                            return new JsonResponse(['status' => 'Proveedor actualizado correctamente'], Response::HTTP_CREATED);
                        } else {
                            return new JsonResponse(['status' => 'La actualización del proveedor falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    } else {
                        return new JsonResponse(['status' => 'No hay campos que actualizar'], Response::HTTP_BAD_REQUEST);
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

    #[Route('/proveedor/{id}', name: 'app_proveedor_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $proveedor = $this->proveedoresRepository->find($id);
            if (!is_null($proveedor)) {
                if (count($proveedor->getPedidos()) < 1) {
                    if ($this->proveedoresRepository->remove($proveedor)) {
                        return new JsonResponse('El proveedor ha sido borrado', Response::HTTP_OK);
                    } else {
                        return new JsonResponse(['status' => 'La eliminación del proveedor falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                } else {
                    return new JsonResponse(['status' => 'El proveedor no puede ser borrado porque tiene pedidos activos'], Response::HTTP_BAD_REQUEST);
                }
            } else {
                return new JsonResponse(['status' => 'El proveedor no existe en la bd'], Response::HTTP_NOT_FOUND);
            }
        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

/*Crear proveedor
{
    "nombre":"Proveedor3",
    "cif":"C33333333",
    "direccion":"C/cinco",
    "telefono":"644444444",
    "email":"ccc@gmail.com",
    "contacto":"Paula Pérez"
}
*/
/*actualizar proveedor
{
    "nombre":"Proveedor3",
    "nuevoNombre":"Coca-cola",
    "cif":"C33333333",
    "direccion":"C/cinco",
    "telefono":"644444444",
    "email":"ccc@gmail.com",
    "contacto":"Paula Pérez"
}
*/
