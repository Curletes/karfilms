<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Pelicula;
use KarfilmsBundle\Form\PeliculaType;
use KarfilmsBundle\Form\EditarPeliculaType;
use Symfony\Component\HttpFoundation\JsonResponse;

class PeliculaController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function mostrarCarteleraAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");
        $peliculas = $pelicula_repo->findAll();

        return $this->render('@Karfilms/pelicula/mostrarcartelera.html.twig', [
                    "peliculas" => $peliculas,
        ]);
    }
    
    public function pelicula($pelicula) {
        $em = $this->getDoctrine()->getEntityManager();

        return $em->createQuery("SELECT p.id, p.titulo FROM KarfilmsBundle:Pelicula p WHERE p.titulo LIKE :pelicula")
                        ->setParameter("pelicula", "%" . $pelicula . "%")
                        ->getResult();
    }
    
    public function peliculaAjaxAction(Request $request) {
        $pelicula = $request->request->get('pelicula');
        $peliculas = $this->pelicula($pelicula);
        $response = new JsonResponse(['peliculas' => $peliculas]);

        return $response;
    }

    public function sesionesPeliculaCartelera($diames, $id) {
        $em = $this->getDoctrine()->getEntityManager();

        $dias = explode("-", $diames);
        $dia = $dias[1] . "-" . $dias[0];

        return $em->createQuery("SELECT s.horarios FROM KarfilmsBundle:Sesion s WHERE s.idPelicula = :id AND s.horarios LIKE :dia")
                        ->setParameter("id", $id)->setParameter("dia", "%" . $dia . "%")
                        ->getResult();
    }

    public function sesionesPeliculaCarteleraAjaxAction(Request $request) {
        $diames = $request->request->get('diames');
        $id = $request->request->get('id');
        $sesiones = $this->sesionesPeliculaCartelera($diames, $id);

        $response = new JsonResponse(['sesiones' => $sesiones]);

        return $response;
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
        $generos = [];

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

        $Generopelicula = $pelicula->getGeneropelicula();

        foreach ($Generopelicula as $genero) {
            $generos[] = $genero->getIdGenero()->getNombre();
        }

        return $this->render('@Karfilms/pelicula/detallespelicula.html.twig', [
                    "pelicula" => $pelicula,
                    "directores" => $directores,
                    "actores" => $actores,
                    "generos" => $generos
        ]);
    }

    public function mostrarPeliculaAction($id) {
        $directores = [];
        $actores = [];
        $generos = [];
        $estadocartelera = "active";

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

        $Generopelicula = $pelicula->getGeneropelicula();

        foreach ($Generopelicula as $genero) {
            $generos[] = $genero->getIdGenero()->getNombre();
        }

        return $this->render('@Karfilms/pelicula/detallespelicula.html.twig', [
                    "pelicula" => $pelicula,
                    "directores" => $directores,
                    "actores" => $actores,
                    "generos" => $generos,
                    "active" => $estadocartelera
        ]);
    }

    public function addPeliculaAction(Request $request) {
        $pelicula = new Pelicula();
        $form = $this->createForm(PeliculaType::class, $pelicula);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $pelicula_repo = $em->getRepository("KarfilmsBundle:Pelicula");

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
                        $status2 = "Sólo se permiten las extensiones .jpg, .jpeg, .png y .bmp.";
                        $this->session->getFlashBag()->add("status", $status2);
                        return $this->redirectToRoute("indice_pelicula");
                    }
                }

                $ficheroTrailer = $form["trailer"]->getData();

                if ($ficheroTrailer != null) {
                    $ext = $ficheroTrailer->guessExtension();

                    if ($ext == "mp4" || $ext == "avi" || $ext == "wmv" || $ext == "mov") {
                        $nombre_fichero = time() . "." . $ext;
                        $ficheroTrailer->move("imagenes/trailers", $nombre_fichero);

                        $pelicula->setTrailer($nombre_fichero);
                    } else {
                        $status2 = "Sólo se permiten las extensiones .mp4, .avi, .wmv y .mov.";
                        $this->session->getFlashBag()->add("status", $status2);
                        return $this->redirectToRoute("indice_pelicula");
                    }
                }

                $pelicula->setDuracion($form->get("duracion")->getData());
                $pelicula->setIdEdad($form->get("idEdad")->getData());

                $em->persist($pelicula);
                $flush = $em->flush();

                $pelicula_repo->guardarActoresPelicula(
                        $form->get("actores")->getData(), $form->get("titulo")->getData()
                );

                $pelicula_repo->guardarDirectoresPelicula(
                        $form->get("directores")->getData(), $form->get("titulo")->getData()
                );

                $pelicula_repo->guardarGenerosPelicula(
                        $form->get("generos")->getData(), $form->get("titulo")->getData()
                );

                if ($flush == null) {
                    $status = "Película añadida correctamente.";
                } else {
                    $status = "Error al añadir la película.";
                }
            } else {
                die(var_dump($form["trailer"]->getData()));
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
        $actorpelicula_repo = $em->getRepository("KarfilmsBundle:Actorpelicula");
        $directorpelicula_repo = $em->getRepository("KarfilmsBundle:Directorpelicula");
        $generopelicula_repo = $em->getRepository("KarfilmsBundle:Generopelicula");

        $actorespelicula = $actorpelicula_repo->findBy(["idPelicula" => $pelicula]);

        foreach ($actorespelicula as $ap) {
            $em->remove($ap);
            $em->flush();
        }

        $directorespelicula = $directorpelicula_repo->findBy(["idPelicula" => $pelicula]);

        foreach ($directorespelicula as $dp) {
            $em->remove($dp);
            $em->flush();
        }

        $generospelicula = $generopelicula_repo->findBy(["idPelicula" => $pelicula]);

        foreach ($generospelicula as $gp) {
            $em->remove($gp);
            $em->flush();
        }

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

        $actores = "";
        $directores = "";
        $generos = "";

        foreach ($pelicula->getActorpelicula() as $Actorpelicula) {
            $actores .= $Actorpelicula->getIdActor()->getNombre() . ", ";
        }

        foreach ($pelicula->getDirectorpelicula() as $Directorpelicula) {
            $directores .= $Directorpelicula->getIdDirector()->getNombre() . ", ";
        }

        foreach ($pelicula->getGeneropelicula() as $Generopelicula) {
            $generos .= $Generopelicula->getIdGenero()->getNombre() . ", ";
        }

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
                        $status2 = "Sólo se permiten las extensiones .jpg, .jpeg y .png.";
                        $this->session->getFlashBag()->add("status", $status2);
                        return $this->redirectToRoute("indice_pelicula");
                    }
                }

                $ficheroTrailer = $form["trailer"]->getData();

                if ($ficheroTrailer != null) {
                    $ext = $ficheroTrailer->guessExtension();

                    if ($ext == "mp4" || $ext == "avi" || $ext == "wmv" || $ext == "mov") {
                        $nombre_fichero = time() . "." . $ext;
                        $ficheroTrailer->move("imagenes/trailers", $nombre_fichero);

                        $pelicula->setTrailer($nombre_fichero);
                    } else {
                        $status2 = "Sólo se permiten las extensiones .mp4, .avi, .wmv y .mov.";
                        $this->session->getFlashBag()->add("status", $status2);
                        return $this->redirectToRoute("indice_pelicula");
                    }
                }

                $pelicula->setDuracion($form->get("duracion")->getData());
                $pelicula->setIdEdad($form->get("idEdad")->getData());

                $em->persist($pelicula);
                $flush = $em->flush();

                $actorpelicula_repo = $em->getRepository("KarfilmsBundle:Actorpelicula");
                $directorpelicula_repo = $em->getRepository("KarfilmsBundle:Directorpelicula");
                $generopelicula_repo = $em->getRepository("KarfilmsBundle:Generopelicula");

                $actorespelicula = $actorpelicula_repo->findBy(["idPelicula" => $pelicula]);

                foreach ($actorespelicula as $ap) {
                    $em->remove($ap);
                    $em->flush();
                }

                $directorespelicula = $directorpelicula_repo->findBy(["idPelicula" => $pelicula]);

                foreach ($directorespelicula as $dp) {
                    $em->remove($dp);
                    $em->flush();
                }

                $generospelicula = $generopelicula_repo->findBy(["idPelicula" => $pelicula]);

                foreach ($generospelicula as $gp) {
                    $em->remove($gp);
                    $em->flush();
                }

                $pelicula_repo->guardarActoresPelicula(
                        $form->get("actores")->getData(), $form->get("titulo")->getData()
                );

                $pelicula_repo->guardarDirectoresPelicula(
                        $form->get("directores")->getData(), $form->get("titulo")->getData()
                );

                $pelicula_repo->guardarGenerosPelicula(
                        $form->get("generos")->getData(), $form->get("titulo")->getData()
                );

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
                    "trailer" => $trailer,
                    "actores" => $actores,
                    "directores" => $directores,
                    "generos" => $generos
        ]);
    }

}
