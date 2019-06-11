<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {

    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas_obj = $pelicula_repo->findAll();

        foreach ($peliculas_obj as $pelicula) {
            if (count($pelicula->getSesiones()) != 0) {
                $peliculas[] = $pelicula;
            }
        }

        $dql = "SELECT p FROM KarfilmsBundle:Pelicula p JOIN KarfilmsBundle:Sesion s WHERE s.idPelicula = p.id ORDER BY p.titulo ASC";
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );

        if (isset($peliculas)) {

            return $this->render('@Karfilms/Default/index.html.twig', [
                        "peliculas" => $peliculas,
                        "pagination" => $pagination,
            ]);
        } else {
            return $this->render('@Karfilms/Default/index.html.twig');
        }
    }

}
