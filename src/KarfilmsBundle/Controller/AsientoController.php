<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Asiento;
use KarfilmsBundle\Form\AsientoType;
use KarfilmsBundle\Form\ReservarAsientoType;
use KarfilmsBundle\Entity\Asientoreservado;

class AsientoController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function indiceAsientoAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();

        $dql = "SELECT a FROM KarfilmsBundle:Asiento a";
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 12
        );

        $asientoreservado_repo = $em->getRepository("KarfilmsBundle:Asientoreservado");
        $asientosreservados = $asientoreservado_repo->findAll();

        return $this->render('@Karfilms/asiento/indiceasiento.html.twig', [
                    "pagination" => $pagination,
                    "asientosreservados" => $asientosreservados
        ]);
    }

    public function addAsientoAction(Request $request) {
        $asiento = new Asiento();
        $form = $this->createForm(AsientoType::class, $asiento);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
                $asiento_existe = $asiento_repo->findOneBy([
                    "idSala" => $form->get("idSala")->getData(),
                    "fila" => $form->get("fila")->getData(),
                    "butaca" => $form->get("butaca")->getData()
                ]);

                if ($asiento_existe == null) {
                    $asiento = new Asiento();
                    $asiento->setFila($form->get("fila")->getData());
                    $asiento->setButaca($form->get("butaca")->getData());
                    $asiento->setIdSala($form->get("idSala")->getData());

                    $em->persist($asiento);
                    $flush = $em->flush();

                    if ($flush == null) {
                        $status = "Asiento añadido correctamente.";
                    }
                } else {
                    $status = "Error ese asiento ya existe.";
                }
            } else {
                $status = "El asiento no se ha añadido porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_asiento");
        }

        return $this->render('@Karfilms/asiento/addasiento.html.twig', [
                    "form" => $form->createView()
        ]);
    }

    public function eliminarAsientoAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
        $asiento = $asiento_repo->find($id);

        $sala_repo = $em->getRepository("KarfilmsBundle:Sala");
        $salas = $sala_repo->findAll();

        foreach ($salas as $sala) {
            if ($asiento->getIdSala()->getId() != $sala->getId()) {
                $em->remove($asiento);
                $em->flush();
            }
        }

        return $this->redirectToRoute("indice_asiento");
    }

    public function editarAsientoAction($id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
        $asiento = $asiento_repo->find($id);

        $form = $this->createForm(AsientoType::class, $asiento);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
                $asiento_existe = $asiento_repo->findOneBy([
                    "idSala" => $form->get("idSala")->getData(),
                    "fila" => $form->get("fila")->getData(),
                    "butaca" => $form->get("butaca")->getData()
                ]);

                if ($asiento_existe == null) {

                    $asiento->setFila($form->get("fila")->getData());
                    $asiento->setButaca($form->get("butaca")->getData());
                    $asiento->setIdSala($form->get("idSala")->getData());

                    $em->persist($asiento);
                    $flush = $em->flush();

                    if ($flush == null) {
                        $status = "Asiento añadido correctamente.";
                    }
                } else {
                    $status = "Ese asiento ya existe.";
                }
            } else {
                $status = "El asiento no se ha editada porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_asiento");
        }

        return $this->render('@Karfilms/asiento/editarasiento.html.twig', [
                    "form" => $form->createView()
        ]);
    }

    public function reservarEntradaAction(Request $request, $pelicula, $sesion) {
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $pelicula = $pelicula_repo->find(["id" => $pelicula]);

        $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
        $sesion = $sesion_repo->find(["id" => $sesion]);

        $sala = $sesion->getIdSala()->getId();

        $asientos = new Asiento();

        $form = $this->createForm(ReservarAsientoType::class, $asientos, ["idSala" => $sala]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $asientoreservado = new Asientoreservado();
                $em = $this->getDoctrine()->getEntityManager();
                $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");

                $asiento = $asiento_repo->findOneBy([
                    "fila" => $form->get("fila")->getData()->getFila(),
                    "butaca" => $form->get("butaca")->getData()->getButaca()
                ]);

                $reserva_repo = $em->getRepository("KarfilmsBundle:Asientoreservado");
                $reserva = $reserva_repo->findOneBy([
                    "idSesion" => $sesion,
                    "idAsiento" => $asiento
                ]);

                if ($reserva == null) {
                    $asientoreservado->setIdAsiento($asiento);
                    $asientoreservado->setIdSesion($sesion);

                    $em->persist($asientoreservado);
                    $flush = $em->flush();

                    if ($flush == null) {
                        $status = "Asiento reservado correctamente.";
                    }
                } else {
                    $status = "Este asiento ya está reservado. Por favor, elige otro asiento.";
                }
            } else {
                $status = "El asiento no se ha reservado porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            if ($status == "Asiento reservado correctamente.") {
                return $this->render('@Karfilms/reserva/mostrarentrada.html.twig', [
                            "fila" => $asiento->getFila(),
                            "butaca" => $asiento->getButaca(),
                            "pelicula" => $pelicula->getTitulo(),
                            "sesion" => $sesion->getHorarios(),
                            "sala" => $sesion->getIdSala()->getNombre()
                ]);
            } else {
                return $this->redirectToRoute("inicio");
            }
        }
        return $this->render('@Karfilms/reserva/reservarentrada.html.twig', [
                    "pelicula" => $pelicula,
                    "sesion" => $sesion,
                    "form" => $form->createView()
        ]);
    }

    public function addTodosLosAsientosAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();

        $sala_repo = $em->getRepository("KarfilmsBundle:Sala");
        $salas = $sala_repo->findAll();
        foreach ($salas as $sala) {
            for ($fila = 1; $fila <= 12; $fila++) {
                for ($butaca = 1; $butaca <= 12; $butaca++) {

                    $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
                    $asiento_existe = $asiento_repo->findOneBy([
                        "idSala" => $sala,
                        "butaca" => $butaca,
                        "fila" => $fila
                    ]);

                    if ($asiento_existe == null) {
                        $asiento = new Asiento();
                        $asiento->setFila($fila);
                        $asiento->setButaca($butaca);
                        $asiento->setIdSala($sala);

                        $em->persist($asiento);
                        $flush = $em->flush();

                        if ($flush == null) {
                            $status = "Asientos añadidos correctamente.";
                        }
                    } else {
                        $status = "Error. Estos asientos ya existen.";
                    }
                }
            }
        }

        $this->session->getFlashBag()->add("status", $status);
        return $this->redirectToRoute("indice_asiento");
    }

    public function eliminarTodosLosAsientosAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $sala_repo = $em->getRepository("KarfilmsBundle:Sala");
        $salas = $sala_repo->findAll();

        $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
        $asientos = $asiento_repo->findAll();

        foreach ($salas as $sala) {
            foreach ($asientos as $asiento) {

                if ($asiento->getIdSala()->getId() == $sala->getId()) {
                    $em->remove($asiento);
                    $em->flush();
                }
            }
        }

        return $this->redirectToRoute("indice_asiento");
    }

}
