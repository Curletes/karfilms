<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Pelicula;
use KarfilmsBundle\Form\PeliculaType;
use KarfilmsBundle\Form\EditarPeliculaType;

class PeliculaController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function indicePeliculaAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas = $pelicula_repo->findAll();

        return $this->render('@Karfilms/pelicula/indicepelicula.html.twig', [
                    "peliculas" => $peliculas
        ]);
    }
    
    public function detallesPeliculaAction($id) {
        $directores = [];
        $actores = [];
        
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $pelicula = $pelicula_repo->find($id);

        $Directorpelicula = $pelicula->getDirectorpelicula();

        foreach ($Directorpelicula as $director) {
            $directores[] = $director->getIdDirector()->getNombre();
        }
        
        $Actorpelicula = $pelicula->getActorpelicula();
        
        foreach ($Actorpelicula as $actor) {
            $actores[] = $actor->getIdActor()->getNombre();
        }

        return $this->render('@Karfilms/pelicula/detallespelicula.html.twig', [
                    "pelicula" => $pelicula,
                    "directores" => $directores,
                    "actores" => $actores
        ]);
    }

    public function addPeliculaAction(Request $request) {
        $pelicula = new Pelicula();
        $form = $this->createForm(PeliculaType::class, $pelicula);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $pelicula = new Pelicula();
                $pelicula->setTitulo($form->get("titulo")->getData());
                $pelicula->setSinopsis($form->get("sinopsis")->getData());

                $ficheroCartel = $form["cartel"]->getData();

                if ($ficheroCartel != null) {
                    $ext = $ficheroCartel->guessExtension();

                    if ($ext == "jpg" || $ext == "png" || $ext == "jpeg" || $ext == "pdf") {
                        $nombre_fichero = time() . "." . $ext;
                        $ficheroCartel->move("imagenes/carteles", $nombre_fichero);

                        $pelicula->setCartel($nombre_fichero);
                    } else {
                        $pelicula->setCartel($cartel);
                        $status2 = "Sólo se permiten las extensiones .jpg, .jpeg, .png y .bmp.";
                    }
                } else {
                    $pelicula->setCartel($cartel);
                }

                $ficheroTrailer = $form["trailer"]->getData();

                if ($ficheroTrailer != null) {
                    $ext = $ficheroTrailer->guessExtension();

                    if ($ext == "mp4" || $ext == "avi" || $ext == "wmv" || $ext == "mov") {
                        $nombre_fichero = time() . "." . $ext;
                        $ficheroTrailer->move("imagenes/trailers", $nombre_fichero);

                        $pelicula->setTrailer($nombre_fichero);
                    } else {
                        $pelicula->setTrailer($trailer);
                        $status2 = "Sólo se permiten las extensiones .mp4, .avi, .wmv y .mov.";
                    }
                } else {
                    $pelicula->setTrailer($trailer);
                }

                $pelicula->setDuracion($form->get("duracion")->getData());
                $pelicula->setIdEdad($form->get("idEdad")->getData());

                $em->persist($pelicula);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Película añadida correctamente.";
                } else {
                    $status = "Error al añadir la película.";
                }
            } else {
                $status = "La pelicula no se ha añadido porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_pelicula");
        }

        return $this->render('@Karfilms/pelicula/addpelicula.html.twig', [
                    "form" => $form->createView()
        ]);
    }

    public function eliminarPeliculaAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $pelicula = $pelicula_repo->find($id);

        if (count($pelicula->getSesiones()) == 0) {
            $em->remove($pelicula);
            $em->flush();
        }

        return $this->redirectToRoute("indice_pelicula");
    }

    public function editarPeliculaAction($id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $pelicula = $pelicula_repo->find($id);
        
        $cartel = $pelicula->getCartel();
        $trailer = $pelicula->getTrailer();

        $form = $this->createForm(EditarPeliculaType::class, $pelicula);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $pelicula->setTitulo($form->get("titulo")->getData());
                $pelicula->setSinopsis($form->get("sinopsis")->getData());

                $ficheroCartel = $form["cartel"]->getData();

                if ($ficheroCartel != null) {
                    $ext = $ficheroCartel->guessExtension();

                    if ($ext == "jpg" || $ext == "png" || $ext == "jpeg") {
                        $nombre_fichero = time() . "." . $ext;
                        $ficheroCartel->move("imagenes/carteles", $nombre_fichero);

                        $pelicula->setCartel($nombre_fichero);
                    } else {
                        $pelicula->setCartel($cartel);
                        $status2 = "Sólo se permiten las extensiones .jpg, .jpeg y .png";
                    }
                } else {
                    $pelicula->setCartel($cartel);
                }

                $ficheroTrailer = $form["trailer"]->getData();

                if ($ficheroTrailer != null) {
                    $ext = $ficheroTrailer->guessExtension();

                    if ($ext == "mp4" || $ext == "avi" || $ext == "wmv" || $ext == "mov") {
                        $nombre_fichero = time() . "." . $ext;
                        $ficheroTrailer->move("imagenes/trailers", $nombre_fichero);

                        $pelicula->setTrailer($nombre_fichero);
                    } else {
                        $pelicula->setTrailer($trailer);
                        $status2 = "Sólo se permiten las extensiones .mp4, .avi, .wmv y .mov.";
                    }
                } else {
                    $pelicula->setTrailer($trailer);
                }

                $pelicula->setDuracion($form->get("duracion")->getData());
                $pelicula->setIdEdad($form->get("idEdad")->getData());

                $em->persist($pelicula);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Película editada correctamente.";
                } else {
                    $status = "Error al editar la película.";
                }
            } else {
                $status = "La película no se ha editado porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_pelicula");
        }

        return $this->render('@Karfilms/pelicula/editarpelicula.html.twig', [
                    "form" => $form->createView(),
                    "cartel" => $cartel,
                    "trailer" => $trailer
        ]);
    }
}