<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Actor;
use KarfilmsBundle\Form\ActorType;

class ActorController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function mostrarActorAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $actor_repo = $em->getRepository("KarfilmsBundle:Actor");
        $actores = $actor_repo->findAll();
        
        $dql = "SELECT a FROM KarfilmsBundle:Actor a";
        $query = $em->createQuery($dql);
 
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
        );

        return $this->render('@Karfilms/actor/mostraractor.html.twig', [
                    "actores" => $actores,
                    "pagination" => $pagination
        ]);
    }

    public function categoriaActorAction(Request $request, $nombre) {
        $em = $this->getDoctrine()->getEntityManager();
        $actor_repo = $em->getRepository('KarfilmsBundle:Actor');
        $actor = $actor_repo->findOneBy(["nombre" => $nombre]);

        $peliculas_obj = $actor->getActorpelicula();

        foreach ($peliculas_obj as $pelicula) {
            $peliculas[] = $pelicula->getIdPelicula();
        }
        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $peliculas,
                $request->query->getInt('page', 1),
                5
        );

        if (isset($peliculas)) {
            return $this->render('@Karfilms/actor/categoriaactor.html.twig', [
                        "pagination" => $pagination,
                        'actor' => $actor
            ]);
        } else {
            return $this->render('@Karfilms/actor/categoriaactor.html.twig', [
                        'actor' => $actor
            ]);
        }
    }

    public function indiceActorAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $actor_repo = $em->getRepository("KarfilmsBundle:Actor");
        $actores = $actor_repo->findAll();
        
        $dql = "SELECT a FROM KarfilmsBundle:Actor a";
        $query = $em->createQuery($dql);
 
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                5
        );

        return $this->render('@Karfilms/actor/indiceactor.html.twig', [
                    "actores" => $actores,
                    "pagination" => $pagination
        ]);
    }

    public function addActorAction(Request $request) {
        $actor = new Actor();
        $form = $this->createForm(ActorType::class, $actor);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $actor = new Actor();
                $actor->setNombre($form->get("nombre")->getData());

                $em->persist($actor);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Actor añadido correctamente.";
                } else {
                    $status = "Error al añadir el actor.";
                }
            } else {
                $status = "El actor no se ha añadido porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_actor");
        }

        return $this->render('@Karfilms/actor/addactor.html.twig', [
                    "form" => $form->createView()
        ]);
    }

    public function eliminarActorAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $actor_repo = $em->getRepository("KarfilmsBundle:Actor");
        $actor = $actor_repo->find($id);

        if (count($actor->getActorpelicula()) == 0) {
            $em->remove($actor);
            $em->flush();
        }

        return $this->redirectToRoute("indice_actor");
    }

    public function editarActorAction($id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $actor_repo = $em->getRepository("KarfilmsBundle:Actor");
        $actor = $actor_repo->find($id);

        $form = $this->createForm(ActorType::class, $actor);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $actor->setNombre($form->get("nombre")->getData());

                $em->persist($actor);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Actor editado correctamente.";
                } else {
                    $status = "Error al editar el actor.";
                }
            } else {
                $status = "El actor no se ha editado porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_actor");
        }

        return $this->render('@Karfilms/actor/editaractor.html.twig', [
                    "form" => $form->createView()
        ]);
    }

}
