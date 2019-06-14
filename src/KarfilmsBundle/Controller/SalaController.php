<?php

namespace KarfilmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use KarfilmsBundle\Entity\Sala;
use KarfilmsBundle\Form\SalaType;

class SalaController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    /*
     * Muestra las salas que están en la base de datos.
     */

    public function indiceSalaAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $sala_repo = $em->getRepository("KarfilmsBundle:Sala");
        $salas = $sala_repo->findAll();

        /*
         * Se comprueba que no tienen sesiones asignadas para que no puedan
         * borrarse.
         */
        $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
        $sesiones = $sesion_repo->findAll();

        return $this->render('@Karfilms/sala/indicesala.html.twig', [
                    "salas" => $salas,
                    "sesiones" => $sesiones
        ]);
    }

    /*
     * Función para crear un formulario para añadir nuevas salas a la base de
     * datos.
     */

    public function addSalaAction(Request $request) {
        /*
         * Se crea un objeto sala nuevo y se manda con el formulario para que
         * muestre los campos de la entidad que tienen que rellenarse.
         */
        $sala = new Sala();
        $form = $this->createForm(SalaType::class, $sala);

        //Se recogen los datos enviados desde el formulario.
        $form->handleRequest($request);

        //Esta parte de la función se ejecuta cuando el formulario se ha enviado y es válido.
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $sala = new Sala();

                /*
                 * Se hace un set en la entidad Sala con el nombre introdudido
                 * en el formulario y se guarda con persist y flush.
                 */
                $sala->setNombre($form->get("nombre")->getData());

                $em->persist($sala);
                $flush = $em->flush();

                //Si la variable flush está vacía, significa que los datos se han añadido sin problema.
                if ($flush == null) {
                    $status = "Sala añadida correctamente.";
                } else {
                    $status = "Error al añadir la sala.";
                }
            } else {
                $status = "El sala no se ha añadida porque el formulario no es válido.";
            }

            /*
             * Se envía a la vista el mensaje creado y guardado en la variable status,
             * y redirige hacia la vista de todas las salas.
             */
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_sala");
        }

        return $this->render('@Karfilms/sala/addsala.html.twig', [
                    "form" => $form->createView()
        ]);
    }

    //Método para eliminar salas, reconociendo la sala en específico por el id enviado desde la url
    public function eliminarSalaAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $sala_repo = $em->getRepository("KarfilmsBundle:Sala");
        $sala = $sala_repo->find($id);

        $sesion_repo = $em->getRepository("KarfilmsBundle:Sesion");
        $sesiones = $sesion_repo->findAll();

        $asiento_repo = $em->getRepository("KarfilmsBundle:Asiento");
        $asientos = $asiento_repo->findAll();

        $reserva_repo = $em->getRepository("KarfilmsBundle:Asientoreservado");
        $reservas = $reserva_repo->findAll();

        foreach ($sesiones as $sesion) {
            if ($sesion->getIdSala()->getId() == $sala->getId()) {
                $em->remove($sesion);
            }
        }

        foreach ($reservas as $reserva) {
            if ($reserva->getIdAsiento()->getIdSala()->getId() == $sala->getId()) {
                $em->remove($reserva);
            }
        }

        foreach ($asientos as $asiento) {
            if ($asiento->getIdSala()->getId() == $sala->getId()) {
                $em->remove($asiento);
            }
        }

        $em->remove($sala);
        $em->flush();

        return $this->redirectToRoute("indice_sala");
    }

    /*
     * Función para editar las salas de la base de datos, recogiendo el id de
     * la sala en cuestión enviado por la url y también los datos enviados desde
     * el formulario de edición con la variable $request.
     * Funcionamiento similar al de la función para añadir salas.
     */

    public function editarSalaAction($id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $sala_repo = $em->getRepository("KarfilmsBundle:Sala");
        $sala = $sala_repo->find($id);

        $form = $this->createForm(SalaType::class, $sala);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $sala->setNombre($form->get("nombre")->getData());

                $em->persist($sala);
                $flush = $em->flush();

                if ($flush == null) {
                    $status = "Sala editada correctamente.";
                } else {
                    $status = "Error al editar la sala.";
                }
            } else {
                $status = "El sala no se ha editada porque el formulario no es válido.";
            }

            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("indice_sala");
        }

        return $this->render('@Karfilms/sala/editarsala.html.twig', [
                    "form" => $form->createView(),
                    "sala" => $sala
        ]);
    }

}
