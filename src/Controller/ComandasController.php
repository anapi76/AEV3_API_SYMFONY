<?php

namespace App\Controller;

use App\Entity\Comandas;
use App\Entity\LineasComandas;
use App\Repository\ComandasRepository;
use App\Repository\LineasComandasRepository;
use App\Repository\MesaRepository;
use App\Repository\ProductosRepository;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComandasController extends AbstractController
{

    private ProductosRepository $productosRepository;
    private ComandasRepository $comandasRepository;
    private LineasComandasRepository $lineasComandasRepository;
    private MesaRepository $mesaRepository;

    public function __construct(ComandasRepository $comandasRepository, LineasComandasRepository $lineasComandasRepository, ProductosRepository $productosRepository, MesaRepository $mesaRepository)
    {
        $this->productosRepository = $productosRepository;
        $this->comandasRepository = $comandasRepository;
        $this->lineasComandasRepository = $lineasComandasRepository;
        $this->mesaRepository = $mesaRepository;
    }

    #[Route('/comandas', name: 'app_comandas_new', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent());
            if (is_null($data)) {
                return new JsonResponse(['status' => 'Error al decodificar el archivo json'], Response::HTTP_BAD_REQUEST);
            }
            if ((!isset($data->mesa) || empty($data->mesa)) || (!isset($data->comensales) || empty($data->comensales)) || (!isset($data->fecha) || empty($data->fecha))) {
                return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
            }
            $comanda = new Comandas();
            $fecha = date_create_from_format("d/m/Y H:i:s", ($data->fecha));
            $comanda->setFecha($fecha);
            //Busco la mesa con el id que hemos recibido y la introduzco
            $mesa = $this->mesaRepository->find($data->mesa);
            if(is_null($mesa)){
                return new JsonResponse(['status' => "La mesa no existe en la bd"], Response::HTTP_NOT_FOUND);
            }
            if ($data->comensales > $mesa->getComensales()) {
                return new JsonResponse(['status' => 'Los comensales no caben en la mesa seleccionada'], Response::HTTP_BAD_REQUEST);
            }
            $comanda->setMesa(($mesa));
            $comanda->setComensales($data->comensales);
            if (isset($data->detalles) && !empty($data->detalles)) {
                $comanda->setDetalles($data->detalles);
            }
            $this->comandasRepository->persist($comanda);
            if (!isset($data->lineas) || empty($data->lineas)) {
                return new JsonResponse(['status' => 'No se puede crear la comanda porque no hay lineas de comanda'], Response::HTTP_BAD_REQUEST);
            }
            $cont = 0;
            $lineas = $data->lineas;
            foreach ($lineas as $linea) {
                if (!isset($linea->producto) || empty($linea->producto) || !isset($linea->cantidad) || empty($linea->cantidad)) {
                    return new JsonResponse(['status' => 'Faltan parámetros'], Response::HTTP_BAD_REQUEST);
                }
                $lineasComanda = new LineasComandas();
                $producto = $this->productosRepository->find($linea->producto);
                if (is_null($producto)) {
                    return new JsonResponse(['status' => "El producto no existe en la bd"], Response::HTTP_NOT_FOUND);
                }
                $lineasComanda->setProducto($producto);
                $lineasComanda->setCantidad($linea->cantidad);
                $lineasComanda->setComanda($comanda);
                $this->lineasComandasRepository->persist($lineasComanda);
                $comanda->addLineasComanda($lineasComanda);
                $cont++;
            }
            if ($cont > 0) {
                $this->comandasRepository->save(true);
                if ($this->comandasRepository->testInsert($comanda)) {
                    return new JsonResponse(['status' => "La comanda ".$comanda->getId()." se ha creado correctamente"], Response::HTTP_CREATED);
                } else {
                    return new JsonResponse(['status' => 'La creación de la comanda falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                return new JsonResponse(['status' => 'No se puede crear la comanda porque no hay lineas de comanda válidas'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/comandas/{id}', name: 'app_comandas_update', methods: ['PUT'])]
    public function edit(Request $request, int $id): JsonResponse
    {
        try {
            $data = json_decode($request->getContent());
            if (is_null($data)) {
                return new JsonResponse(['status' => 'Error al decodificar el archivo json'], Response::HTTP_BAD_REQUEST);
            }
            $comanda = $this->comandasRepository->find($id);
            if (is_null($comanda)) {
                return new JsonResponse(['status' => "La comanda no existe en la bd"], Response::HTTP_NOT_FOUND);
            }
            if (isset($data->fecha) && !empty($data->fecha)) {
                $fecha = date_create_from_format("d/m/Y H:i:s", ($data->fecha));
                $comanda->setFecha($fecha);
            }
            if (isset($data->mesa) && !empty($data->mesa)) {
                $mesa = $this->mesaRepository->find($data->mesa);
            }
            if(is_null($mesa)){
                return new JsonResponse(['status' => "La mesa no existe en la bd"], Response::HTTP_NOT_FOUND);
            }
            if ($data->comensales > $mesa->getComensales()) {
                return new JsonResponse(['status' => 'Los comensales no caben en la mesa seleccionada'], Response::HTTP_BAD_REQUEST);
            }
            $comanda->setMesa($mesa);
            $comanda->setComensales($data->comensales);
            if (isset($data->detalles) && !empty($data->detalles)) {
                $comanda->setDetalles($data->detalles);
            }
            $this->comandasRepository->persist($comanda);
            if (!isset($data->lineas) || empty($data->lineas)) {
                return new JsonResponse(['status' => 'No se puede crear la comanda porque no hay lineas de comanda'], Response::HTTP_BAD_REQUEST);
            }
            $lineas = $data->lineas;
            foreach ($lineas as $index => $linea) {
                $lineasComanda = $this->lineasComandasRepository->findOneBy(['id' => $index]);
                if (is_null($lineasComanda)) {
                    $lineasComanda = new LineasComandas();
                    $comanda->addLineasComanda($lineasComanda);
                    $lineasComanda->setComanda($comanda);
                }
                if (isset($linea->producto) && !empty($linea->producto)) {
                    $producto = $this->productosRepository->find($linea->producto);
                    if (is_null($producto)) {
                        return new JsonResponse(['status' => "El producto no existe en la bd"], Response::HTTP_NOT_FOUND);
                    }
                    $lineasComanda->setProducto($producto);
                }
                if (isset($linea->cantidad) && !empty($linea->cantidad)) {
                    $lineasComanda->setCantidad($linea->cantidad);
                }
                $this->lineasComandasRepository->persist($lineasComanda);
            }
           $this->comandasRepository->save(true);
            if ($this->comandasRepository->testInsert($comanda)) {
                return new JsonResponse(['status' => "La comanda ".$comanda->getId()." se ha actualizado correctamente"], Response::HTTP_CREATED);
            } else {
                return new JsonResponse(['status' => 'La actualización de la comanda falló'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (Exception $e) {
            $msg = 'Error del servidor: ' . $e->getMessage();
            return new JsonResponse(['status' => $msg], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

/* {
    "fecha":"10/01/2023 12:00:00",
    "mesa":"1",
    "comensales":"3",
    "detalles":"Los comensales tienen prisa",
    "lineas":{
        "1":{
            "producto":"1",
            "cantidad":"7"
         },
        "2":{
            "producto":"2",
            "cantidad":"10"
        }
   }
} */
