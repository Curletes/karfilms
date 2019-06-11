<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Sugerencia;
use KarfilmsBundle\Form\SugerenciaType;
use KarfilmsBundle\Entity\Valoracion;

class SugerenciaController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function mostrarYaddSugerenciaAction(Request $request, $id = null) {
        $em = $this->getDoctrine()->getEntityManager();
        $sugerencia_repo = $em->getRepository("KarfilmsBundle:Sugerencia");
        $sugerencias = $sugerencia_repo->findAll();

        $sugerencia = new Sugerencia();
        $form = $this->createForm(SugerenciaType::class, $sugerencia);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $sugerencia = new Sugerencia();
                $sugerencia->setTexto($form->get("texto")->getData());

                $usuario = $this->getUser();
                $sugerencia->setIdUsuario($usuario);

                $em->persist($sugerencia);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Sugerencia añadida correctamente.";
                } else {
                    $status = "Error al añadir la sugerencia.";
                }
            } else {
                $status = "La sugerencia no se ha añadido porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute('mostrar_sugerencia');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $dql = "SELECT s FROM KarfilmsBundle:Sugerencia s";
        $query = $em->createQuery($dql);
 
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
        );

        return $this->render('@Karfilms/sugerencia/mostrarsugerencia.html.twig', [
                    "sugerencias" => $sugerencias,
                    "form" => $form->createView(),
                    "pagination" => $pagination
        ]);
    }

    public function eliminarSugerenciaAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $sugerencia_repo = $em->getRepository("KarfilmsBundle:Sugerencia");
        $sugerencia = $sugerencia_repo->find($id);
        
        $valoracion_repo = $em->getRepository("KarfilmsBundle:Valoracion");
        $valoraciones = $valoracion_repo->findBy(["idSugerencia" => $id]);

        foreach($valoraciones as $valoracion)
        {
            $em->remove($valoracion);
        }

        $em->remove($sugerencia);
        $em->flush();

        return $this->redirectToRoute("mostrar_sugerencia");
    }

    public function likeSugerenciaAction($idSugerencia, $idUsuario) {
        $em = $this->getDoctrine()->getEntityManager();
        $sugerencia_repo = $em->getRepository("KarfilmsBundle:Sugerencia");
        $sugerencia = $sugerencia_repo->find($idSugerencia);
        $usuario_repo = $em->getRepository("KarfilmsBundle:Usuario");
        $usuario = $usuario_repo->find($idUsuario);
        
        $valoracion_repo = $em->getRepository("KarfilmsBundle:Valoracion");
        $valoracion = $valoracion_repo->findOneBy(["idUsuario" => $idUsuario, "idSugerencia" => $idSugerencia]);
        
        if($valoracion == null)
        {
            $valoracion = new Valoracion();
            $valoracion->setIdSugerencia($sugerencia);
            $valoracion->setIdUsuario($usuario);
            $em->persist($valoracion);

            $em->flush();
        }
        else
        {
            $em->remove($valoracion);
            $em->flush();
        }

        return $this->redirectToRoute("mostrar_sugerencia");
    }

}
