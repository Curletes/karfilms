<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function indexAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas_obj = $pelicula_repo->findAll();

        foreach ($peliculas_obj as $pelicula) {
            if (count($pelicula->getSesiones()) != 0) {
                $peliculas[] = $pelicula;
            }
        }

        if (isset($peliculas)) {
            return $this->render('@Karfilms/Default/index.html.twig', [
                        "peliculas" => $peliculas,
            ]);
        } else {
            return $this->render('@Karfilms/Default/index.html.twig');
        }
    }

}
