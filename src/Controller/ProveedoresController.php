<?php

namespace App\Controller;

use App\Entity\Proveedores;
use App\Repository\ProveedoresRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/proveedor')]
class ProveedoresController extends AbstractController
{

    private ProveedoresRepository $proveedoresRepository;

    public function __construct(ProveedoresRepository $proveedoresRepository)
    {
        $this->proveedoresRepository = $proveedoresRepository;
    }

    #[Route('/{id}', name: 'app_proveedor_id', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $data = $this->proveedoresRepository->proveedorJSON($id);
        if (is_null($data)) {
            $result = 'El id no existe en la bd';
            return new JsonResponse(['status' => $result], Response::HTTP_BAD_REQUEST);
        } else {
            return new JsonResponse($data, Response::HTTP_OK);
        }
    }

    #[Route('/', name: 'app_proveedores', methods: ['GET'])]
    public function showAll(): JsonResponse
    {
        $data = $this->proveedoresRepository->proveedoresJSON();
        if (is_null($data)) {
            $result = 'No se han recibido datos';
            return new JsonResponse(['status' => $result], Response::HTTP_BAD_REQUEST);
        } else {
            return new JsonResponse($data, Response::HTTP_OK);
        }
    }

    #[Route('/', name: 'app_proveedor_new', methods: ['POST'])]
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
                    $this->proveedoresRepository->new($data->nombre, $data->cif, $data->direccion, $telefono, $email, $contacto);
                    if ($this->proveedoresRepository->testInsert($data->nombre)) {
                        return new JsonResponse(['status' => 'Proveedor insertado correctamente'], Response::HTTP_CREATED);
                    } else {
                        return new JsonResponse(['status' => 'El proveedor no se ha insertado'], Response::HTTP_BAD_REQUEST);
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

    #[Route('/', name: 'app_proveedor_edit', methods: ['PUT'])]
    public function edit(Request $request): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            if (!is_null($data)) {
                $proveedor = $this->proveedoresRepository->findOneBy(['nombre' => $data->nombre]);
                if (!is_null($proveedor)) {
                    $cont = 0;
                    if (isset($data->nuevoNombre) && !empty($data->nuevoNombre)) {
                        $nuevoNombre = $data->nuevoNombre;
                        $cont++;
                    } else {
                        $nuevoNombre = null;
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
                        $this->proveedoresRepository->update($proveedor, $nuevoNombre, $cif, $direccion, $telefono, $email, $contacto);
                        return new JsonResponse(['status' => 'Proveedor actualizado correctamente'], Response::HTTP_CREATED);
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

    #[Route('/{id}', name: 'app_proveedor_edit_id', methods: ['PATCH'])]
    public function editById(int $id, Request $request): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            if (!is_null($data)) {
                $proveedor = $this->proveedoresRepository->find($id);
                if (!is_null($proveedor)) {
                    $cont = 0;
                    if (isset($data->nuevoNombre) && !empty($data->nuevoNombre)) {
                        $nuevoNombre = $data->nuevoNombre;
                        $cont++;
                    } else {
                        $nuevoNombre = null;
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
                        $this->proveedoresRepository->update($proveedor, $nuevoNombre, $cif, $direccion, $telefono, $email, $contacto);
                        return new JsonResponse(['status' => 'Proveedor actualizado correctamente'], Response::HTTP_CREATED);
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

    #[Route('/{id}', name: 'app_proveedor_delete', methods: ['DELETE'])]
    public function delete(int $id): mixed
    {
        try {
            $proveedor = $this->proveedoresRepository->find($id);
            if (!is_null($proveedor)) {
                dump(count($proveedor->getPedidos()));
                if (count($proveedor->getPedidos()) == 0) {
                    $this->proveedoresRepository->remove($proveedor);
                    if ($this->proveedoresRepository->testDelete($proveedor)) {
                        return new JsonResponse('El proveedor ha sido borrado', Response::HTTP_OK);
                    } else {
                        $result = 'Error al eliminar el proveedor';
                        return new JsonResponse(['status' => $result], Response::HTTP_BAD_REQUEST);
                    }
                } else {
                    $result = 'El proveedor no puede ser borrado porque tiene pedidos activos';
                    return new JsonResponse(['status' => $result], Response::HTTP_BAD_REQUEST);
                }
            } else {
                $result = 'El id no existe en la bd';
                return new JsonResponse(['status' => $result], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /* #[Route('/proveedor', name: 'app_proveedor_new', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            if (!is_null($data)) {
                if ((isset($data->nombre) && !empty($data->nombre)) && (isset($data->cif) && !empty($data->cif)) && (isset($data->direccion) && !empty($data->direccion))) {
                    $proveedor = new Proveedores();
                    $proveedor->setNombre($data->nombre);
                    $proveedor->setCif($data->cif);
                    $proveedor->setDireccion($data->direccion);
                    if (isset($data->telefono) && !empty($data->telefono)) {
                        $proveedor->setTelefono($data->telefono);
                    }
                    if (isset($data->email) && !empty($data->email)) {
                        $proveedor->setEmail($data->email);
                    }
                    if (isset($data->contacto) && !empty($data->contacto)) {
                        $proveedor->setContacto($data->contacto);
                    }
                    $this->proveedoresRepository->save($proveedor, true);
                    if ($this->proveedoresRepository->testInsert($proveedor)) {
                        return new JsonResponse(['status' => 'Proveedor insertado correctamente'], Response::HTTP_CREATED);
                    } else {
                        return new JsonResponse(['status' => 'El proveedor no se ha insertado'], Response::HTTP_BAD_REQUEST);
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
    } */

    /* #[Route('/proveedor', name: 'app_proveedor_edit', methods: ['PUT'])]
    public function edit(Request $request): JsonResponse
    {
        try {
            // Decodifico el contenido de la petición http
            $data = json_decode($request->getContent());
            if (!is_null($data)) {
                $proveedor = $this->proveedoresRepository->findOneBy(['nombre' => $data->nombre]);
                if (!is_null($proveedor)) {
                    $cont = 0;
                    if (isset($data->nombre) && !empty($data->nombre)) {
                        $proveedor->setNombre($data->nombre);
                        $cont++;
                    }
                    if (isset($data->cif) && !empty($data->cif)) {
                        $proveedor->setCif($data->cif);
                        $cont++;
                    }
                    if (isset($data->direccion) && !empty($data->direccion)) {
                        $proveedor->setDireccion($data->direccion);
                        $cont++;
                    }
                    if (isset($data->telefono) && !empty($data->telefono)) {
                        $proveedor->setTelefono($data->telefono);
                        $cont++;
                    }
                    if (isset($data->email) && !empty($data->email)) {
                        $proveedor->setEmail($data->email);
                        $cont++;
                    }
                    if (isset($data->contacto) && !empty($data->contacto)) {
                        $proveedor->setContacto($data->contacto);
                        $cont++;
                    }
                    if ($cont > 0) {
                        $this->proveedoresRepository->save($proveedor, true);
                        if ($this->proveedoresRepository->testInsert($data->nombre)) {
                            return new JsonResponse(['status' => 'Proveedor actualizado correctamente'], Response::HTTP_CREATED);
                        } else {
                            return new JsonResponse(['status' => 'El proveedor no se ha actualizado'], Response::HTTP_BAD_REQUEST);
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
    } */
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
