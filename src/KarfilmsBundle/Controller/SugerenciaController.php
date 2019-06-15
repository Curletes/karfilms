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

    /*
     * Muestra las sugerencias que están en la base de datos, paginándolas (5 actores por página).
     * Además crea un formulario para añadir nuevas sugerencias a la base de
     * datos.
     */

    public function mostrarYaddSugerenciaAction(Request $request, $id = null) {
        $authenticationUtils = $this->get("security.authentication_utils");
        $error = $authenticationUtils->getLastAuthenticationError();
        $em = $this->getDoctrine()->getEntityManager();
        $sugerencia_repo = $em->getRepository("KarfilmsBundle:Sugerencia");
        $sugerencias = $sugerencia_repo->findAll();

        /*
         * Se crea un objeto sugerencia nuevo y se manda con el formulario para que
         * muestre los campos de la entidad que tienen que rellenarse.
         */
        $sugerencia = new Sugerencia();
        $form = $this->createForm(SugerenciaType::class, $sugerencia);

        //Se recogen los datos enviados desde el formulario.
        $form->handleRequest($request);

        //Esta parte de la función se ejecuta cuando el formulario se ha enviado y es válido.
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $sugerencia = new Sugerencia();
                /*
                 * Se hace un set en la entidad Sugerencia con el texto introdudido.
                 */
                $sugerencia->setTexto($form->get("texto")->getData());

                /*
                 * Se recoge el id del usuario que ha mandado la sugerencia y se
                 * añade a la tabla de sugerencias para identificar quién la ha enviado.
                 */
                $usuario = $this->getUser();
                $sugerencia->setIdUsuario($usuario);

                /*
                 * Se guardan los cambios con persist y flush.
                 */
                $em->persist($sugerencia);
                $flush = $em->flush();

                //Si la variable flush está vacía, significa que los datos se han añadido sin problema.
                if ($flush == null) {
                    $status = "Sugerencia añadida correctamente.";
                } else {
                    $status = "Error al añadir la sugerencia.";
                }
            } else {
                $status = "La sugerencia no se ha añadido porque el formulario no es válido.";
            }

            /*
             * Se envía a la vista el mensaje creado y guardado en la variable status,
             * y redirige hacia la vista de todas las sugerencias.
             */
            if ($status == "Sugerencia añadida correctamente.") {
                $this->session->getFlashBag()->add("status", $status);
                return $this->redirectToRoute('mostrar_sugerencia');
            }
        }

        /*
         * Creación de una query para realizar una consulta a la base de datos.
         * Selecciona todos las sugerencias.
         */
        $dql = "SELECT s FROM KarfilmsBundle:Sugerencia s";
        $query = $em->createQuery($dql);

        /*
         * Paginación.
         */
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), 5
        );

        return $this->render('@Karfilms/sugerencia/mostrarsugerencia.html.twig', [
                    "sugerencias" => $sugerencias,
                    "form" => $form->createView(),
                    "pagination" => $pagination,
                    "error" => $error
        ]);
    }

    /* étodo para eliminar sugerencias, reconociendo la sugerencia en específico 
     * por el id enviado desde la url.
     */

    public function eliminarSugerenciaAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $sugerencia_repo = $em->getRepository("KarfilmsBundle:Sugerencia");
        $sugerencia = $sugerencia_repo->find($id);

        /*
         * Se recogen las valoraciones que tiene dicha sugerencia y si las tiene,
         * estas se borran para luego borrar la sugerencia automáticamente.
         */
        $valoracion_repo = $em->getRepository("KarfilmsBundle:Valoracion");
        $valoraciones = $valoracion_repo->findBy(["idSugerencia" => $id]);

        foreach ($valoraciones as $valoracion) {
            $em->remove($valoracion);
        }

        $em->remove($sugerencia);
        $em->flush();

        return $this->redirectToRoute("mostrar_sugerencia");
    }

    /*
     * Función para valorar las sugerencias que han enviado los usuarios.
     * Se recoge el id de la sugerencia y el id del usuario que ha dado a like a la
     * sugerencia para guardarlos en una tabla.
     */

    public function likeSugerenciaAction($idSugerencia, $idUsuario) {
        $em = $this->getDoctrine()->getEntityManager();
        $sugerencia_repo = $em->getRepository("KarfilmsBundle:Sugerencia");
        $sugerencia = $sugerencia_repo->find($idSugerencia);
        $usuario_repo = $em->getRepository("KarfilmsBundle:Usuario");
        $usuario = $usuario_repo->find($idUsuario);

        $valoracion_repo = $em->getRepository("KarfilmsBundle:Valoracion");
        $valoracion = $valoracion_repo->findOneBy(["idUsuario" => $idUsuario, "idSugerencia" => $idSugerencia]);

        /*
         * Si no existe una valoración de este usuario para esta sugerencia, se
         * añade la valoración creando un objeto Valoracion y guardando el id del 
         * usuario y el id de la sugerencia en la tabla valoraciones.
         */
        if ($valoracion == null) {
            $valoracion = new Valoracion();
            $valoracion->setIdSugerencia($sugerencia);
            $valoracion->setIdUsuario($usuario);
            $em->persist($valoracion);

            $em->flush();
        }
        /*
         * Si ya existía una valoración de este usuario, la valoración se elimina.
         */ else {
            $em->remove($valoracion);
            $em->flush();
        }

        return $this->redirectToRoute("mostrar_sugerencia");
    }

}
