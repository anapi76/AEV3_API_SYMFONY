<?php

namespace App\Controller;

use App\Repository\ComandasRepository;
use App\Repository\TicketsRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketsController extends AbstractController
{
    //método para crear un ticket a partir del id de la comanda
    #[Route('/tickets/{id}', name: 'app_tickets', methods: 'POST')]
    public function add(?int $id = null, ComandasRepository $comandasRepository, TicketsRepository $ticketRepository): JsonResponse
    {
        try {
            if (is_null($id)) {
                return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
            }
            $comanda = $comandasRepository->find($id);
            if (is_null($comanda)) {
                return new JsonResponse(['status' => 'La comanda no existe en la bd'], Response::HTTP_NOT_FOUND);
            }
            if ($comanda->isEstado()) {
                return new JsonResponse(['status' => 'El ticket no se puede generar, faltan lineas comanda por entregar'], Response::HTTP_BAD_REQUEST);
            }
            $lineasComanda = $comanda->getLineasComandas();
            $importe = 0;
            foreach ($lineasComanda as $linea) {
                $cantidad = $linea->getCantidad();
                $precio = $linea->getProducto()->getPrecio();
                $total = $precio + $cantidad;
                $importe += $total;
            }
            $ticket = $ticketRepository->new($comanda, $importe, true);
            if ($ticketRepository->testInsert($ticket)) {
                $data = $ticketRepository->ticketJSON($ticket);
                return new JsonResponse($data, Response::HTTP_CREATED);
            } else {
                return new JsonResponse(['status' => 'La creación del ticket falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
