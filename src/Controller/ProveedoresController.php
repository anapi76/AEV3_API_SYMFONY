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

    //método para mostrar todos los proveedores
    #[Route('/proveedor', name: 'app_proveedor_all', methods: ['GET'])]
    public function showAll(): JsonResponse
    {
        $data = $this->proveedoresRepository->proveedoresAllJSON();
        if (is_null($data)) {
            return new JsonResponse(['status' => 'No existen proveedores en la bd'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    //método para mostrar un proveedor por su id
    #[Route('/proveedor/{id}', name: 'app_proveedor', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $data = $this->proveedoresRepository->proveedorJSON($id);
        if (is_null($data)) {
            return new JsonResponse(['status' => 'El proveedor no existe en la bd'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    //método para añadir un proveedor
    #[Route('/proveedor', name: 'app_proveedor_new', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            if (is_null($data)) {
                return new JsonResponse(['status' => 'Error al decodificar el archivo json'], Response::HTTP_BAD_REQUEST);
            }
            if ((!isset($data->nombre) || empty($data->nombre)) || (!isset($data->cif) || empty($data->cif)) || (!isset($data->direccion) || empty($data->direccion))) {
                return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
            }
            $telefono = (isset($data->telefono) && !empty($data->telefono)) ? $data->telefono : null;
            $email = (isset($data->email) && !empty($data->email)) ? $data->email : null;
            $contacto = (isset($data->contacto) && !empty($data->contacto)) ? $data->contacto : null;
            $this->proveedoresRepository->new($data->nombre, $data->cif, $data->direccion, $telefono, $email, $contacto, true);
            if ($this->proveedoresRepository->testInsert($data->nombre)) {
                return new JsonResponse(['status' => 'Proveedor insertado correctamente'], Response::HTTP_CREATED);
            } else {
                return new JsonResponse(['status' => 'La inserción del proveedor falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //método para modificar un proveedor con diferentes métodos por id o por su nombre
    #[Route('/proveedor/{nombre}', name: 'app_proveedor_edit_ByName', methods: ['PUT'])]
    public function editByNombre(Request $request, ?string $nombre = null): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            if (is_null($data)) {
                return new JsonResponse(['status' => 'Error al decodificar el archivo json'], Response::HTTP_BAD_REQUEST);
            }
            if (is_null($nombre)) {
                return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
            }
            $proveedor = $this->proveedoresRepository->findOneBy(['nombre' => $nombre]);
            if (is_null($proveedor)) {
                return new JsonResponse(['status' => 'El proveedor no existe en la bd'],  Response::HTTP_NOT_FOUND);
            }
            $nombre = isset($data->nombre) && !empty($data->nombre) ? $data->nombre : null;
            $cif = (isset($data->cif) && !empty($data->cif)) ? $data->cif : null;
            $direccion = (isset($data->direccion) && !empty($data->direccion)) ? $data->direccion : null;
            $telefono = (isset($data->telefono) && !empty($data->telefono)) ? $data->telefono : null;
            $email = (isset($data->email) && !empty($data->email)) ? $data->email : null;
            $contacto = (isset($data->contacto) && !empty($data->contacto)) ? $data->contacto : null;
            if (!is_null($nombre) || !is_null($cif) || !is_null($direccion) || !is_null($telefono) || !is_null($email) || !is_null($contacto)) {
                $this->proveedoresRepository->update($proveedor, $nombre, $cif, $direccion, $telefono, $email, $contacto, true);
                if ($this->proveedoresRepository->testUpdate($proveedor)) {
                    return new JsonResponse(['status' => 'Proveedor actualizado correctamente'], Response::HTTP_CREATED);
                } else {
                    return new JsonResponse(['status' => 'La actualización del proveedor falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                return new JsonResponse(['status' => 'No hay campos que actualizar'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //método para modificar un proveedor con diferentes métodos por id o por su nombre
    #[Route('/proveedor/{id}', name: 'app_proveedor_edit_ById', methods: ['PATCH'])]
    public function editById(Request $request, ?int $id = null): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            if (is_null($data)) {
                return new JsonResponse(['status' => 'Error al decodificar el archivo json'], Response::HTTP_BAD_REQUEST);
            }
            if ((is_null($id))) {
                return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
            }
            $proveedor = $this->proveedoresRepository->find($id);
            if (is_null($proveedor)) {
                return new JsonResponse(['status' => 'El proveedor no existe en la bd'],  Response::HTTP_NOT_FOUND);
            }
            $nombre = isset($data->nombre) && !empty($data->nombre) ? $data->nombre : null;
            $cif = (isset($data->cif) && !empty($data->cif)) ? $data->cif : null;
            $direccion = (isset($data->direccion) && !empty($data->direccion)) ? $data->direccion : null;
            $telefono = (isset($data->telefono) && !empty($data->telefono)) ? $data->telefono : null;
            $email = (isset($data->email) && !empty($data->email)) ? $data->email : null;
            $contacto = (isset($data->contacto) && !empty($data->contacto)) ? $data->contacto : null;
            if (!is_null($nombre) || !is_null($cif) || !is_null($direccion) || !is_null($telefono) || !is_null($email) || !is_null($contacto)) {
                $this->proveedoresRepository->update($proveedor, $nombre, $cif, $direccion, $telefono, $email, $contacto, true);
                if ($this->proveedoresRepository->testUpdate($proveedor)) {
                    return new JsonResponse(['status' => 'Proveedor actualizado correctamente'], Response::HTTP_CREATED);
                } else {
                    return new JsonResponse(['status' => 'La actualización del proveedor falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                return new JsonResponse(['status' => 'No hay campos que actualizar'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //método para borrar un proveedor
    #[Route('/proveedor/{id}', name: 'app_proveedor_delete', methods: ['DELETE'])]
    public function delete(?int $id = null): JsonResponse
    {
        try {
            if (is_null($id)) {
                return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
            }
            $proveedor = $this->proveedoresRepository->find($id);
            if (is_null($proveedor)) {
                return new JsonResponse(['status' => 'El proveedor no existe en la bd'], Response::HTTP_NOT_FOUND);
            }
            if (count($proveedor->getPedidos()) < 1) {
                $this->proveedoresRepository->remove($proveedor, true);
                if ($this->proveedoresRepository->testDelete($proveedor)) {
                    return new JsonResponse('El proveedor ha sido borrado', Response::HTTP_OK);
                } else {
                    return new JsonResponse(['status' => 'La eliminación del proveedor falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                return new JsonResponse(['status' => 'El proveedor no puede ser borrado porque tiene pedidos activos'], Response::HTTP_BAD_REQUEST);
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
