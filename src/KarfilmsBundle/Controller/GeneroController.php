<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Genero;
use KarfilmsBundle\Form\GeneroType;

class GeneroController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function mostrarGeneroAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $genero_repo = $em->getRepository("KarfilmsBundle:Genero");
        $generos = $genero_repo->findAll();

        return $this->render('@Karfilms/genero/mostrargenero.html.twig', [
                    "generos" => $generos
        ]);
    }

    public function categoriaGeneroAction($nombre) {
        $em = $this->getDoctrine()->getEntityManager();
        $genero_repo = $em->getRepository('KarfilmsBundle:Genero');
        $genero = $genero_repo->findOneBy(["nombre" => $nombre]);

        $peliculas_obj = $genero->getGeneropelicula();

        foreach ($peliculas_obj as $pelicula) {
            $peliculas[] = $pelicula->getIdPelicula();
        }

        if (isset($peliculas)) {
            return $this->render('@Karfilms/genero/categoriagenero.html.twig', [
                        'peliculas' => $peliculas,
                        'genero' => $genero
            ]);
        } else {
            return $this->render('@Karfilms/genero/categoriagenero.html.twig', [
                        'genero' => $genero
            ]);
        }
    }

    public function indiceGeneroAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $genero_repo = $em->getRepository("KarfilmsBundle:Genero");
        $generos = $genero_repo->findAll();

        return $this->render('@Karfilms/genero/indicegenero.html.twig', [
                    "generos" => $generos
        ]);
    }

    public function addGeneroAction(Request $request) {
        $genero = new Genero();
        $form = $this->createForm(GeneroType::class, $genero);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $genero = new Genero();
                $genero->setNombre($form->get("nombre")->getData());

                $em->persist($genero);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Género añadido correctamente.";
                } else {
                    $status = "Error al añadir el género.";
                }
            } else {
                $status = "El género no se ha añadido porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_genero");
        }

        return $this->render('@Karfilms/genero/addgenero.html.twig', [
                    "form" => $form->createView()
        ]);
    }

    public function eliminarGeneroAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $genero_repo = $em->getRepository("KarfilmsBundle:Genero");
        $genero = $genero_repo->find($id);

        if (count($genero->getGeneropelicula()) == 0) {
            $em->remove($genero);
            $em->flush();
        }

        return $this->redirectToRoute("indice_genero");
    }

    public function editarGeneroAction($id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $genero_repo = $em->getRepository("KarfilmsBundle:Genero");
        $genero = $genero_repo->find($id);

        $form = $this->createForm(GeneroType::class, $genero);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $genero->setNombre($form->get("nombre")->getData());

                $em->persist($genero);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Género editado correctamente.";
                } else {
                    $status = "Error al editar el género.";
                }
            } else {
                $status = "El género no se ha editado porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_genero");
        }

        return $this->render('@Karfilms/genero/editargenero.html.twig', [
                    "form" => $form->createView()
        ]);
    }

}
