<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Sesion;
use KarfilmsBundle\Form\SesionType;

class SesionController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    /*
     * Muestra las sesiones que están en la base de datos, listándolos por orden
     * de fechas y paginadas (5 sesiones por página).
     */
    public function indiceSesionAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();

        $dql = "SELECT s FROM KarfilmsBundle:Sesion s ORDER BY s.horarios ASC";
        $query = $em->createQuery($dql);

        /*
         * Paginación
         */
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );

        return $this->render('@Karfilms/sesion/indicesesion.html.twig', [
                    "pagination" => $pagination
        ]);
    }

    /*
     * Función para crear un formulario para añadir nuevas seiones a la base de
     * datos.
     */
    public function addSesionAction(Request $request) {
        /*
         * Se crea un objeto sesion nuevo y se manda con el formulario para que
         * muestre los campos de la entidad que tienen que rellenarse.
         */
        $sesion = new Sesion();
        $form = $this->createForm(SesionType::class, $sesion);

        //Se recogen los datos enviados desde el formulario.
        $form->handleRequest($request);

        //Esta parte de la función se ejecuta cuando el formulario se ha enviado y es válido.
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                /*
                 * Comprobación de si la sesión que acaba de enviarse por el formulario
                 * ya está en la base de datos, buscándola según su horario y su sala.
                 * Esto es porque no puede haber dos sesiones con la misma hora y
                 * la misma sala al mismo tiempo.
                 */
                $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
                $sesion_ocupada = $sesion_repo->findOneBy([
                    "idSala" => $form->get("idSala")->getData(),
                    "horarios" => $form->get("horarios")->getData()
                ]);

                 /*
                 * Si el resultado de la consulta anterior es null, significa
                 * que no hay ninguna sesión registrada con esas características.
                 */
                if ($sesion_ocupada == null) {
                    /*
                     * Se crea el objeto sesion, llamando a los setters de 
                     * la clase Sesion para añadir el horario, el id de la película
                     * y el id de la sala que se han enviado por el formulario.
                     */
                    $sesion = new Sesion();
                    $sesion->setHorarios($form->get("horarios")->getData());
                    $sesion->setIdPelicula($form->get("idPelicula")->getData());
                    $sesion->setIdSala($form->get("idSala")->getData());

                    /*
                     * Se guardan esos sets. Si la variable $flush es nula, el asiento
                     * se ha guardado correctamente.
                     */
                    $em->persist($sesion);
                    $flush = $em->flush();

                    if ($flush == null) {
                        $status = "Sesión añadida correctamente.";
                    }
                } else {
                    $status = "Esa sesión ya está ocupada.";
                }
            } else {
                $status = "La sesión no se ha añadida porque el formulario no es válido.";
            }

            /*
             * Creación del mensaje que se mostrará para el usuario al haber mandado
             * el formulario. Se redirige a la lista de sesiones.
             */
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_sesion");
        }

        return $this->render('@Karfilms/sesion/addsesion.html.twig', [
                    "form" => $form->createView()
        ]);
    }

    //Método para eliminar sesiones, reconociendo la sesión en específico por el id enviado desde la url
    public function eliminarSesionAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
        $sesion = $sesion_repo->find($id);
        
        /*
         * Repositorio para comprobar si los asientos de esa sesión están reservados.
         */
        $reserva_repo = $em->getRepository("KarfilmsBundle:Asientoreservado");
        $reservas = $reserva_repo->findAll();

        /*
         * En caso de que estén reservados, se borrarán estas reservas para esta
         * sesión.
         */
        foreach ($reservas as $reserva) {
            if ($reserva->getIdSesion()->getId() == $sesion->getId()) {
                $em->remove($reserva);
            }
        }

        /*
         * Finalmente se borra la sesión indicada.
         */
        $em->remove($sesion);
        $em->flush();

        return $this->redirectToRoute("indice_sesion");
    }

    /*
     * Función para editar las sesiones de la base de datos, recogiendo el id de
     * la sesión en cuestión enviado por la url y también los datos enviados desde
     * el formulario de edición con la variable $request.
     * Funcionamiento similar al de la función para añadir sesiones.
     */
    public function editarSesionAction($id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
        $sesion = $sesion_repo->find($id);

        $form = $this->createForm(SesionType::class, $sesion);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
                $sesion_ocupada = $sesion_repo->findOneBy([
                    "idSala" => $form->get("idSala")->getData(),
                    "horarios" => $form->get("horarios")->getData()
                ]);

                if ($sesion_ocupada == null) {
                    $sesion = new Sesion();
                    $sesion->setHorarios($form->get("horarios")->getData());
                    $sesion->setIdPelicula($form->get("idPelicula")->getData());
                    $sesion->setIdSala($form->get("idSala")->getData());

                    $em->persist($sesion);
                    $flush = $em->flush();

                    if ($flush == null) {
                        $status = "Sesión añadida correctamente.";
                    }
                } else {
                    $status = "Esa sesión ya está ocupada.";
                }
            } else {
                $status = "La sesión no se ha editado porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_sesion");
        }

        return $this->render('@Karfilms/sesion/editarsesion.html.twig', [
                    "form" => $form->createView(),
                    "sesion" => $sesion
        ]);
    }

}
