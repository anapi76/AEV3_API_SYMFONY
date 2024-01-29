<?php

namespace App\Controller;

use App\Repository\ProveedoresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProveedoresController extends AbstractController
{
    #[Route('/proveedor/{id}', name: 'app_proveedor_id', methods: ['GET'])]
    public function show(ProveedoresRepository $proveedoresRepository, int $id): JsonResponse
    {
        $data = $proveedoresRepository->proveedorJSON($id);
        if (is_null($data)) {
            $result = 'El id no existe en la bd';
            return new JsonResponse(['status' => $result], Response::HTTP_BAD_REQUEST);
        } else {
            return new JsonResponse($data, Response::HTTP_OK);
        }
    }

    #[Route('/proveedor', name: 'app_proveedor', methods: ['GET'])]
    public function showAll(ProveedoresRepository $proveedoresRepository): JsonResponse
    {
        $data = $proveedoresRepository->proveedoresJSON();
        if (is_null($data)) {
            $result = 'No se han recibido datos';
            return new JsonResponse(['status' => $result], Response::HTTP_BAD_REQUEST);
        } else {
            return new JsonResponse($data, Response::HTTP_OK);
        }
    }

    
}
