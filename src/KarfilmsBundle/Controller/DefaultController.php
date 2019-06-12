<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {
    /*
     * Función para mostrar en la página de inicio todas las películas que 
     * tengan alguna sesión asignada.
     */

    public function indexAction(Request $request) {
        /*
         * Se recogen todas las películas de la base de datos.
         */
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas_obj = $pelicula_repo->findAll();

        /*
         * Se recorren todas las películas y, si tienen alguna sesión, se guardan
         * en el array $peliculas.
         */
        foreach ($peliculas_obj as $pelicula) {
            if (count($pelicula->getSesiones()) != 0) {
                $peliculas[] = $pelicula;
            }
        }

        /*
         * Consulta para crear la paginación (5 películas por página) y ordenarlas
         * alfabéticamente.
         */
        $dql = "SELECT p FROM KarfilmsBundle:Pelicula p "
                . "JOIN KarfilmsBundle:Sesion s "
                . "WHERE s.idPelicula = p.id "
                . "ORDER BY p.titulo ASC";
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );

        /*
         * Si existe el array de peliculas, significa que hay películas con sesiones,
         * por lo que se manda a la vista dicho array además de la paginación.
         */
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
